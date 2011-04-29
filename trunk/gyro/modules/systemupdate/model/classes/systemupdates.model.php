<?php
/**
 * Table for systemupdate version control
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class DAOSystemupdates extends DataObjectBase {
	public $component;
	public $version;
	
	public $connection = DB::DEFAULT_CONNECTION;
	
	/**
	 * Return table definition
	 *
	 * @return IDBTable
	 */
	public function create_table_object() {
		return new DBTable(
			'systemupdates',
			array(
				new DBFieldText('component', 50, null, DBFieldText::NOT_NULL),	
				new DBFieldInt('version', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL)				
			),
			'component'
		);
	}
	
	/**
 	 * Returns DB driver fro this table
 	 *  
 	 * @return IDBDriver
 	 */
 	public function get_table_driver() {
 		return DB::get_connection($this->connection);
 	}
 	
	/**
	 * Returns name of table, but escaped
	 * 
	 * @return string
	 */
	public function get_table_name_escaped() {
		return $this->get_table_driver()->escape_database_entity($this->get_table_name(), IDBDriver::TABLE);
	}	 	
}
