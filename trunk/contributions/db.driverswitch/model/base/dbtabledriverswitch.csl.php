<?php
/**
 * Change driver of given DBTable
 * 
 * @author Gerd Riesselmann
 * @ingroup DriverSwitch
 */
class DBTableDriverSwitch implements IDBTable {
	/**
	 * Table to change driver for
	 *
	 * @var IDBTable
	 */
	protected $delegate;
	/**
	 * New driver
	 *
	 * @var IDBDriver
	 */
	protected $driver;
	
	/**
	 * Constructor
	 *
	 * @param IDBTable $table The table
	 * @param IDBDriver $driver
	 */
	public function __construct(IDBTable $table, $driver) {
		$this->delegate = $table;
		$this->driver = $driver;
	}

	/**
	 * Switch given table to connection
	 * 
	 * @param string Table or model name
	 * @param IDBDriver|string Driver or driver name
	 */
	public static function switch_table($table_name, $driver) {
		DB::create($table_name);
		$table = DBTableRepository::get($table_name);
		$switched = new DBTableDriverSwitch($table, DB::get_connection($driver));
		DBTableRepository::register($switched, $table_name);
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
		return $this->delegate->get_table_alias();
	}
	
	/**
	 * Returns name of table, but escaped
	 * 
	 * @return string
	 */
	public function get_table_name_escaped() {
		return $this->get_table_driver()->escape_database_entity($this->get_table_name(), IDBDriver::TABLE);
	}

	/**
	 * Returns alias of table, if any - but escaped
	 * 
	 * @return string
	 */
	public function get_table_alias_escaped() {
		return $this->delegate->get_table_alias_escaped();
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
 	 * Returns field for given column
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
 		return $this->driver;
 	} 		
}
