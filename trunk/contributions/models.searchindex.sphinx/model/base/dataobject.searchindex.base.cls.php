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
	
	
	/**
	 * Create Table features
	 *
	 * @return DBTable
	 */
	protected function create_table_object() {
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
		$exclude = array();
		foreach(Arr::force($models, false) as $model) {
			$exclude[] = SearchIndexRepository::get_model_id($model);
		}
		$exclude = array_filter($exclude);
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
		$include = array();
		foreach(Arr::force($models, false) as $model) {
			$include[] = SearchIndexRepository::get_model_id($model);
		}
		$include = array_filter($include);
		if (count($include)) {
			$this->add_where('item_model', DBWhere::OP_IN, $include);
		}		
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
		try {
			$found = $this->find();
		}
		catch (Exception $ex) {
			$this->set_sphinx_feature(DBDriverSphinx::FEATURE_STRIP_OPERATORS, true);
			$found = $this->find();
		}	
		
		if ($found) {
			while ($this->fetch()) {
				$model = $this->resolve_model($this->item_model);
				$p = false;
				if ($model) {
					//@TODO PK is not necessarily named "id"
					$p = DB::get_item($model, 'id', $this->item_id);
					$p->relevance_w = $this->relevance_w;
				}
				if ($p) {
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
    	$this->set_sphinx_feature(DBDriverSphinx::FEATURE_WEIGHTS, array('title' => 5, 'teaser' => 3, 'text' => 2));
		parent::configure_select_query($query, $policy);
		
		$weight_base = 5000;
		$weight_if_expression = $weight_base;
		// Build model weighting expression
		foreach(SearchIndexRepository::get_model_rules() as $rule) {
			$model_id = $rule->model_id;
			$model_weight = String::number($weight_base * $rule->weight, 2, true); 
			$weight_if_expression = "IF(item_model = $model_id, $model_weight, $weight_if_expression)";
		}
		
		$query->set_fields(array(
			'*',
			'@weight - (' . time() . ' - modificationdate) / 2592000 * 0.4 + ' . $weight_if_expression => 'relevance_w'
		));		
	}
}
