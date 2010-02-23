<?php
/**
 * Alias for given DBTable
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBTableAlias implements IDBTable {
	/**
	 * Table to overwrite Alias for
	 *
	 * @var IDBTable
	 */
	protected $delegate;
	/**
	 * Alias
	 *
	 * @var string
	 */
	protected $alias;
	
	/**
	 * Constructor
	 *
	 * @param IDBTable $table The table to overload alias for
	 * @param string $alias
	 */
	public function __construct(IDBTable $table, $alias) {
		$this->delegate = $table;
		$this->alias = $alias;
	}
	
	/**
	 * Returns name of table
	 * 
	 * @return string
	 */
	public function get_table_name() {
		return $this->delegate->get_table_name();
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
		return $this->delegate->get_table_name_escaped();
	}

	/**
	 * Returns alias of table, if any - but escaped
	 * 
	 * @return string
	 */
	public function get_table_alias_escaped() {
		return $this->get_table_driver()->escape_database_entity($this->alias, IDBDriver::ALIAS);
	}	

	/**
	 * Returns array of columns
	 * 
	 * @return array Associative array with column name as key and IDBField instance as value 
	 */
 	public function get_table_fields() {
		return $this->delegate->get_table_fields();
	}
 	
 	/**
 	 * Returns field fpr given column
 	 *
 	 * @param string $column Column name
 	 * @return IDBField Either field or false if no such field exists
 	 */
 	public function get_table_field($column) {
		return $this->delegate->get_table_field($column);
	}
 	
 	/**
 	 * Returns array of keys
 	 * 
 	 * @return array Associative array with column name as key and IDField instance as value
 	 */
 	public function get_table_keys() {
		return $this->delegate->get_table_keys();
	}
 	
 	/**
 	 * Returns array of relations 
 	 * 
 	 * @return array Array with IDBRelation instance as value 
 	 */
 	public function get_table_relations() {
		return $this->delegate->get_table_relations();
	}
 	
 	/**
 	 * Returns relations between two tables
 	 *
 	 * @param IDBTable $other
 	 * @return array Array of IDBRelations
 	 */
 	public function get_matching_relations(IDBTable $other) {
		return $this->delegate->get_matching_relations($other);
	}
 	
 	/**
 	 * Returns array of constraints
 	 * 
 	 * @return array Array with IDBConstraint instance as value 
 	 */
 	public function get_table_constraints() {
		return $this->delegate->get_table_constraints();
	}
	
 	/**
 	 * Returns DB driver fro this table
 	 *  
 	 * @return IDBDriver
 	 */
 	public function get_table_driver() {
 		return $this->delegate->get_table_driver();
 	} 		
}
