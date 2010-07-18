<?php
/**
 * Defines a db driver, e.g for MySQL or PostgreSQL
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBDriver {
	const FEATURE_REPLACE = 'replace';
	
	const TABLE = 'TABLE';
	const ALIAS = 'ALIAS';
	const FIELD = 'FIELD';
	
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
	 * @param mixed $params Driver dependend
	 */
	public function initialize($dbname, $user = '', $password = '', $host = 'localhost', $params = false);

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
	 * @param string $type What to escape, field, table, or alias
	 */
	public function escape_database_entity($obj, $type = self::FIELD);
	
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
	 * Explain the given query
	 * 
	 * @since 0.5.1
	 * 
	 * @param string $sql
	 * @return IDBResultSet False if quey cant be explain or driver does not support it
	 */
	public function explain($sql);

	/**
	 * Make this driver the default driver
	 * 
	 * @return Status
	 */
	public function make_default();
	
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