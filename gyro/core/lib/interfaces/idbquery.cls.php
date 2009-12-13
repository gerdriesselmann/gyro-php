<?php
require_once dirname(__FILE__) . '/idbsqlbuilder.cls.php';
require_once dirname(__FILE__) . '/ipolicyholder.cls.php';
require_once dirname(__FILE__) . '/idbwhereholder.cls.php';

/**
 * Represents a DB query
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBQuery extends IDBSqlBuilder, IPolicyHolder, IDBWhereHolder {
	/**
	 * Used for clearing fields
	 */
	const CLEAR = null;
	/**
	 * Default policy
	 */
	const NORMAL = 0;

	/**
	 * Returns table
	 *
	 * @return IDBTable
	 */
	public function get_table();

	/**
	 * Set the fields this query affects
	 *
	 * Dependent on the query time, the array passed can be of different forms
	 *
	 * UPDATE and INSERT: Associative array with field name as key and field value as value
	 *
	 * $query = new DBQuery($some_table);
	 * $query->set_fields(array('name' => 'Johnny'));
	 * $query->add_where('id', '=', 3);
	 * $sql = $query->build_update_sql(); // returns UPDATE some_table SET name = 'Johnny' WHERE id = 3
	 *
	 * SELECT: Either array of field names or associative array with field name as key and field alias as value.
	 * Both can be combined.
	 *
	 * $query = new DBQuery($some_table);
	 * $query->set_fields(array('count(name)' => 'c', 'phone'));
	 * $sql = $query->build_select_sql(); // returns SELECT count(name) AS c, phone FROM some_table
	 *
	 * If invoked with DBQuery::CLEAR, the fields will get cleared
	 *
	 * @param array $arr_fields
	 * @return void
	 */
	public function set_fields($arr_fields);

	/**
	 * Add a field
	 *
	 * @param mixed $field
	 */
	public function add_field($field);

	/**
	 * Return fields
	 *
	 * @return array
	 */
	public function get_fields();
}
