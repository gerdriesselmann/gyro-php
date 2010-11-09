<?php
/**
 * Repository for search index. Used to register models and implementation
 * 
 * @author Gerd Riesselmann
 * @ingroup SearchIndex
 */
class SearchIndexRepository {
	private static $index = null;
	private static $model_rules = array();
	
	/**
	 * Set the index implementation 
	 */
	public static function set_index_implementation(ISearchIndex $index) {
		self::$index = $index;
	}
	
	/**
	 * Get the index implementation
	 * 
	 * @return ISearchIndex
	 */
	public static function get_index_implementation() {
		if (self::$index) {
			return clone(self::$index);
		} else {
			$model = Config::get_value(ConfigSearchIndex::TABLE_NAME);
			return DB::create($model);
		}
	}	
	
	/**
	 * Add a model rule
	 */
	public static function add_model_rule(SearchIndexModelRule $rule) {
		self::$model_rules[$rule->model] = $rule;
	}
	
	/**
	 * Add a model 
	 */
	public static function add_model($model, $id = false, $weight = 1) {
		if (empty($id)) {
			$id = count(self::$model_rules) + 1;
		}
		self::add_model_rule(new SearchIndexModelRule($model, $id, $weight));
	}
	
	public static function get_model_rules() {
		return self::$model_rules;
	}
	
	/**
	 * Retrieve rule for given model
	 * 
	 * @return SearchIndexModelRule
	 */
	public static function get_model_rule($model) {
		return Arr::get_item(self::$model_rules, $model, false);
	}
	
	/**
	 * Return model id
	 */
	public static function get_model_id($model) {
		$ret = false;
		$rule = self::get_model_rule($model);
		if ($rule) {
			$ret = $rule->model_id;
		}
		return $ret;
	}
	
	public static function get_model_for_id($id) {
		$ret = false;
		foreach(self::$model_rules as $model => $rule) {
			if ($rule->model_id == $id) {
				$ret = $model;
				break;
			}
		}
		return $ret;
	}
	
	public static function get_model_rule_for_id($id) {
		$model = self::get_model_for_id($id);
		if ($model) {
			return self::get_model_rule($model);
		}
		return false;
	}
}