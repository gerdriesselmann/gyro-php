<?php
/**
 * Defines a db driver, e.g for MySQL or PostgreSQL
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBDriver {
	const FEATURE_REPLACE = 'replace';	
	
	/**
	 * Return name of driver, e.g "mysql". Lowercase!
	 * @return string
	 */
	public function get_driver_name();
	
	/**
	 * Returns host name of database
	 * 
	 * @return string
	 */
	public function get_host();
	
	/**
	 * Returns name of DB
	 * 
	 * @return string
	 */
	public function get_db_name();
	
	/**
	 * Connect to DB
	 *
	 * @param string $dbname Name of DB 
	 * @param string $user Username
	 * @param string $password Password
	 * @param string $host Host
	 */
	public function initialize($dbname, $user = '', $password = '', $host = 'localhost');

	/**
	 * Escape given value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function escape($value);
	
	/**
	 * Quote given value
	 *
	 * @param string $value
	 */
	public function quote($value);

	/**
	 * Escape given database object, like table, field etc
	 *
	 * @param string $obj
	 */
	public function escape_database_entity($obj);
	
	/**
	 * Return current status
	 * 
	 * @return Status
	 */
	public function get_status();
	
	/**
	 * Execute an SQL command (Insert, Update...) 
	 *
	 * @param string $sql
	 * @return Status
	 */
	public function execute($sql);
	
	/**
	 * Execute a Select statement
	 *
	 * @param string $sql
	 * @return IDBResultSet
	 */
	public function query($sql);
	
	/**
	 * Start transaction
	 */
	public function trans_start();

	/**
	 * Commit transaction
	 */
	public function trans_commit();

	/**
	 * Rollback transaction
	 */
	public function trans_rollback();	

	/**
	 * Get last insert ID
	 */
	public function last_insert_id();
	
	/**
	 * Returns true, if a given feature is supported
	 * 
	 * @param string feature
	 * @return bool 
	 */
	public function has_feature($feature);
}