<?php
/**
 * Represents a DB table
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBTable implements IDBTable {
	/**
	 * name of table
	 *
	 * @var string
	 */
	protected $name = '';
	/**
	 * Alias of table
	 *
	 * @var string
	 */
	protected $alias = '';
	/**
	 * Fields of table
	 *
	 * @var array
	 */
	protected $fields = array();
	/**
	 * Array of key fields
	 *
	 * @var array
	 */
	protected $keys = array();
	/**
	 * Array of relation objects
	 *
	 * @var array
	 */
	protected $relations = array();
	/**
	 * Array of contraint objects
	 *
	 * @var array
	 */
	protected $constraints = array();
	/**
	 * DB Driver for this table
	 * 
	 * @var IDBDriver
	 */
	protected $driver;
	
	public function __construct($name, $fields = null, $keys = null, $relations = null, $constraints = null, $driver = null) {
		$this->driver = empty($driver) ? DB::get_connection(DB::DEFAULT_CONNECTION) : DB::get_connection($driver);
		$this->name = $name;
		$this->alias = $name;
		if (is_array($fields)) {
			foreach($fields as $field) {
				$this->add_field($field);
			}
		}
		if (!empty($keys)) {
			foreach(Arr::force($keys) as $key) {
				$this->set_as_key($key);
			}
		}
		if (!empty($relations)) {
			foreach(Arr::force($relations) as $relation) {
				$this->add_relation($relation);
			}
		}		
		if (!empty($constraints)) {
			foreach(Arr::force($constraints) as $constraint) {
				$this->add_constraint($constraint);
			}
		}		
	}
	
 	// *****************************************
 	// IDBTable
 	// *****************************************
	
	/**
	 * Returns name of table
	 */
	public function get_table_name() {
		return $this->name;
	}
	
	/**
	 * Returns alias of table, if any
	 * 
	 * @return string
	 */
	public function get_table_alias() {
		return $this->alias;
	}
	
	/**
	 * Returns name of table, but escaped
	 * 
	 * @return string
	 */
	public function get_table_name_escaped() {
		return $this->driver->escape_database_entity($this->name, IDBDriver::TABLE);
	}

	/**
	 * Returns alias of table, if any - but escaped
	 * 
	 * @return string
	 */
	public function get_table_alias_escaped() {
		return $this->driver->escape_database_entity($this->alias, IDBDriver::ALIAS);
	}	

	/**
	 * Returns array of columns
	 * 
	 * @return array Associative array with column name as key and IDBField instance as value 
	 */
 	public function get_table_fields() {
 		return $this->fields;
 	}
 	
 	/**
 	 * Returns field fpr given column
 	 *
 	 * @param string $column Column name
 	 * @return IDBField Either field or false if no such field exists
 	 */
 	public function get_table_field($column) {
 		$ret = Arr::get_item($this->fields, $column, false);
  		return $ret;
 	}
 	
 	/**
 	 * Returns array of keys
 	 * 
 	 * @return array Associative array with column name as key and IDField instance as value
 	 */
 	public function get_table_keys() {
 		return $this->keys;
 	}
	
 	/**
 	 * Returns DB driver fro this table
 	 *  
 	 * @return IDBDriver
 	 */
 	public function get_table_driver() {
 		return $this->driver;
 	} 	
 	
 	/**
 	 * Returns array of relations 
 	 * 
 	 * @return array Array with IDBRelation instance as value 
 	 */
 	public function get_table_relations() {
 		return $this->relations;
 	}
 
 	/**
 	 * Returns relations between two tables
 	 *
 	 * @param IDBTable $other
 	 * @return array Array of IDBRelations
 	 */
 	public function get_matching_relations(IDBTable $other) {
		$relations_this2other = $this->find_relations($this, $other);
		$relations_other2this = $this->find_relations($other, $this);
		
		$relations_other2this = $this->remove_duplicated_relations($relations_other2this, $relations_this2other);
		
		foreach($relations_other2this as  $relation) {
			$relations_this2other[] = new DBRelation($other->get_table_name(), $relation->get_reversed_fields());
		}
				
		return $relations_this2other;
 	}
 	
 	/**
 	 * Find relations between source and target 
 	 *
 	 * @param IDBTable $source
 	 * @param IDBTable $target
 	 * @return array Array of IDBRelations
 	 */
 	protected function find_relations(IDBTable $source, IDBTable $target) {
 		$ret = array();
 		$table_name_to_check = $target->get_table_name();
 		foreach($source->get_table_relations() as $relation) {
 			if ($relation->get_target_table_name() == $table_name_to_check) {
				$ret[] = $relation;
			}
		}
		return $ret;
 	}	

 	/**
 	 * Returns relations from arr1 that are not contained in arr2 
 	 *
 	 * @param array $arr1 Array of IDBRelations
 	 * @param array $arr2 Array of IDBRelations
 	 * @return array Array of IDBRelations
 	 */
 	protected function remove_duplicated_relations($arr1, $arr2) {
 		$ret = array();
 		foreach($arr1 as $parent_relation) {
			if (!$this->relation_is_in_array($parent_relation, $arr2)) {
				$ret[] = $parent_relation;
			}
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Returns true, if there is a counterpart for given relation in array of others
 	 *
 	 * @param IDBRelation $relation
 	 * @param array $arr_relations
 	 * @return bool
 	 */
 	protected function relation_is_in_array(IDBRelation $relation, $arr_relations) {
 		$duplicate = false;
 		// These are the fields  
 		$fields_to_check = array();
 		foreach($relation->get_fields() as $fieldrelation) {
 			$fields_to_check[$fieldrelation->get_source_field_name()] = $fieldrelation->get_target_field_name();
		}
 		$c_check = count($fields_to_check);
 		foreach($arr_relations as $relation_to_test_against) {
 			$fields = $relation_to_test_against->get_fields();
			if (count($fields) != $c_check) {
				continue;
			}
			$duplicate = true;
 			foreach($fields as $fieldrelation) {
				$fieldname_to_check = Arr::get_item($fields_to_check, $fieldrelation->get_target_field_name(), ''); 
 				if ($fieldname_to_check != $fieldrelation->get_source_field_name()) { 
 					$duplicate = false;
 					break;
 				}
 			}
 			if ($duplicate) {
 				break;
 			}
 		}
 		return $duplicate;
 	}
 		
 	/**
 	 * Returns array of constraints
 	 * 
 	 * @return array Array with IDBConstraint instance as value 
 	 */
 	public function get_table_constraints() {
 		return $this->constraints;
 	}
 	
 	// *****************************************
 	// Own methods
 	// *****************************************
 	
	/**
	 * Adds a field to the collection of fields
	 *
	 * @param IDBField $field
	 */
	public function add_field(IDBField $field) {
		$field->set_connection($this->driver);
		$this->fields[$field->get_field_name()] = $field;
	}

	/**
	 * Adds a relation to the collection of relations
	 *
	 * @param IDBRelation $relation
	 */
	public function add_relation(IDBRelation $relation) {
		$this->relations[] = $relation;
	}	

	/**
	 * Adds a constraint to the collection of constraints
	 *
	 * @param IDBConstraint $constraint
	 */
	public function add_constraint(IDBConstraint $constraint) {
		$this->constraints[] = $constraint;
	}		
	
	/**
	 * Define column $column as key
	 *
	 * @param string $column
	 */
 	public function set_as_key($column) {
 		$key_field = $this->get_table_field($column);
 		if ($key_field) {
 			$this->keys[$column] = $key_field;
 		}
 		else {
 			throw new Exception(tr('Can not set key - no field %col', 'core', array('%col' => $column)));
 		}
 	}
}