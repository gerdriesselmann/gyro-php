<?php
/**
 * Model base class for an application wide search index 
 * 
 * @ingroup SearchIndexSphinx
 * @author Gerd Riesselmann
 */
abstract class DataObjectSearchIndexSphinxBase extends DataObjectSphinxBase implements ISearchIndex {
	public $id;
	public $item_id;
	public $item_model;
	
	public $title;
	public $teaser;
	public $text;
	public $meta;
	public $modificationdate;
	public $creationdate;
	
	protected $matching = self::MATCH_NARROW; 
	
	/**
	 * Create Table features
	 *
	 * @return DBTable
	 */
	protected function create_table_object() {
		$this->set_matching(self::MATCH_NARROW);
		return new DBTable(
			Config::get_value(ConfigSearchIndex::TABLE_NAME),
			array_merge(
				array(
					new DBFieldInt('id', null, DBFieldInt::PRIMARY_KEY),
					new DBFieldInt('item_id', null, DBFieldInt::UNSIGNED | DBDriverSphinx::SPHINX_ATTRIBUTE),
					new DBFieldInt('item_model', null, DBFieldInt::UNSIGNED | DBDriverSphinx::SPHINX_ATTRIBUTE),
					new DBFieldText('title', 200, null, DBField::NOT_NULL),
					new DBFieldText('teaser', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NONE),
					new DBFieldTextHtml('text', DBFieldText::BLOB_LENGTH_LARGE, null, DBField::NONE),
					new DBFieldText('meta', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NONE),
					new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::NOT_NULL  | DBDriverSphinx::SPHINX_ATTRIBUTE),
					new DBFieldDateTime('modificationdate', DBFieldDateTime::NOW, DBFieldDateTime::NOT_NULL  | DBDriverSphinx::SPHINX_ATTRIBUTE)													
				),
				$this->get_additional_field_definitions()	
			),
			'id',
			array(),
			array(),
			DBDriverSphinx::DEFAULT_CONNECTION_NAME
		);
	}

	/**
	 * Too be overloaded. Return addition table fields
	 * 
	 * @return array Array of IDBField
	 */
	protected function get_additional_field_definitions() {
		return array();
	}

	// *****************************
	// ISearchIndex 
	// *****************************
	
	/**
	 * Set the search string
	 * 
	 * @param string $search The search string
	 */
	public function set_search($search) {
		$this->sphinx_all_fields = $search;
	}
	
	/**
	 * Exclude models from search
	 * 
	 * @param string|array Name of model or array of names of models
	 */
	public function exclude_models($models) {
		$exclude = $this->extract_model_ids($models);
		if (count($exclude)) {
			$this->add_where('item_model', DBWhere::OP_NOT_IN, $exclude);
		}
	}
	
	/**
	 * Include only given models in search
	 * 
	 * @param string|array Name of model or array of names of models
	 */
	public function limit_to_models($models) {
		$include = $this->extract_model_ids($models);
		switch(count($include)) {
			case 0:
				// Ensure nothing is found!
				$this->add_where('item_model', '=', 0);
				break;
			default:
				$this->add_where('item_model', DBWhere::OP_IN, $include);
				break;
		}
	}

	/**
	 * Return an array of model ids for given models
	 */
	protected function extract_model_ids($models) {
		$ret = array();
		foreach(Arr::force($models, false) as $model) {
			$ret[] = SearchIndexRepository::get_model_id($model);
		}
		// Remove empty ids
		$ret = array_filter($ret);
		return $ret;
	} 
	
	/**
	 * Sort by Relevance 
	 */
	public function sort_by_relevance() {
		$this->sort('relevance_w', self::DESC);
	}
	
	/**
	 * Set matching mode (MATCH_WIDE or MATCH_NARROW) 
	 */
	public function set_matching($matching) {
		$this->matching = $matching;
	}	
	
	
	// **************************************
	// Dataobject
	// **************************************

	/**
	 * Count resulting items
	 * 
	 * Make same Behaviour as count_pager()
	 * 
	 * @return int
	 */
	public function count() {
		try {
			$count = parent::count();
		}
		catch (Exception $ex) {
			$this->set_sphinx_feature(DBDriverSphinx::FEATURE_STRIP_OPERATORS, true);
			$count = parent::count();
		}		
		return min($count, APP_SPHINX_MAX_MATCHES);		
	}
	
	/**
	 * Return array of sortable columns. Array has column name as key and some sort of sort-column-object or an array as values  
	 */
	public function get_sortable_columns() {
		return array(
			'relevance' => new DBSortColumn('relevance_w', 'Relevanz', DBSortColumn::TYPE_MATCH, DBSortColumn::ORDER_FORWARD, true)
		);
	}	
	
	/**
	 * Get the column to sort by default
	 */
	public function get_sort_default_column() {
		return 'relevance';
	}
	
	/**
	 * Execute search and return Entries DAO objects
	 */
	public function execute() {
		$ret = array();
		$log_fail = Config::has_feature(Config::LOG_FAILED_QUERIES);
		try {
			// Disable logging failed queries, since this is allowed to fail
			Config::set_feature(Config::LOG_FAILED_QUERIES, false);
			$found = $this->find();
			Config::set_feature(Config::LOG_FAILED_QUERIES, $log_fail);
		}
		catch (Exception $ex) {
			Config::set_feature(Config::LOG_FAILED_QUERIES, $log_fail);
			// This is a fallback, if combination of operators caus an error 
			$this->set_sphinx_feature(DBDriverSphinx::FEATURE_STRIP_OPERATORS, true);
			$found = $this->find();
		}	
		if ($found) {
			while ($this->fetch()) {
				$model = $this->resolve_model($this->item_model);
				$p = false;
				if ($model) {
					$p = DB::get_item_by_pk($model, $this->item_id);
				}
				if ($p) {
					$p->relevance_w = $this->relevance_w;
					$ret[] = $p;
				}
			}
		}
		return $ret;
	}
	
	/**
	 * Turn a model ID into a model name
	 */
	protected function resolve_model($id) {
		return SearchIndexRepository::get_model_for_id($id);
	}

	/**
	 * Configure a select query
	 *
	 * @param DBQuerySelect $query
	 * @param int $policy
	 */
	protected function configure_select_query($query, $policy) {
    	$this->set_field_weights();
    	$this->sphinx_all_fields = $this->preprocess_query($this->sphinx_all_fields);
 		switch($this->matching) {
			case self::MATCH_WIDE:
				$this->set_sphinx_feature(DBDriverSphinx::FEATURE_MATCH_MODE, DBDriverSphinx::MATCH_OR);
				break;
			default:
				$this->set_sphinx_feature(DBDriverSphinx::FEATURE_MATCH_MODE, DBDriverSphinx::MATCH_EX);
				break;
		}    	
		parent::configure_select_query($query, $policy);
		
		$query->set_fields(array(
			'*',
			$this->compute_relevance_w() => 'relevance_w'
		));		
	}
	
	/**
	 * Preprocess query string
	 * 
	 * @param string $query
	 * @return string
	 */
	protected function preprocess_query($query) {
		// replace "a-b" by "a b", like in 'ad-hoc'
		$query = String::preg_replace('@(\w)\-@', '$1 ', $query);
		return $query;
	}
	
	/**
	 * Set weights for columns
	 */
	protected function set_field_weights() {
		$this->set_sphinx_feature(DBDriverSphinx::FEATURE_WEIGHTS, array('title' => 5, 'teaser' => 3, 'text' => 1));
	}
	
	/**
	 * COmputed weighted relevance
	 * 
	 * @return string An expression Sphinx can evaluate
	 */
	protected function compute_relevance_w() {
		$model_weight = $this->compute_model_weight(3000);
		$age_weight = $this->compute_age_weight(100);
		return "@weight + ($age_weight) + ($model_weight)";
	}
	
	/**
	 * Compute weighting regarding the age of things
	 * 
	 * @attention This gets added, so to vote down older stuff, return a negative expression
	 * 
	 * @return string
	 */
	protected function compute_age_weight($weight_base) {
		$ret = '0';
		if ($this->matching == self::MATCH_NARROW) {
			$month = 30 * GyroDate::ONE_DAY;
			$now = time();
			$weight_base = String::number($weight_base, 2, true);
			
			$ret = "-$weight_base * ($now - modificationdate) / $month";
		}
		return $ret;
	}

	/**
	 * Compute weighting of models in relation to each other, like defined in model rules
	 * 
	 * @param int $weight_base Factor by which a model with a relevance of 2 is voted up
	 * @return strng
	 */
	protected function compute_model_weight($weight_base) {
		$weight_if_expression = $weight_base;
		// Build model weighting expression
		foreach(SearchIndexRepository::get_model_rules() as $rule) {
			$model_id = $rule->model_id;
			$model_weight = String::number($weight_base * $rule->weight, 2, true); 
			$weight_if_expression = "IF(item_model = $model_id, $model_weight, $weight_if_expression)";
		}
		return $weight_if_expression;
	}
}
