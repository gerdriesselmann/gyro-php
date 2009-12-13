<?php
require_once dirname(__FILE__) . '/isearchadapter.cls.php';
require_once dirname(__FILE__) . '/idbtable.cls.php';
require_once dirname(__FILE__) . '/idbwhereholder.cls.php';

/**
 * Base interface for data objects
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDataObject extends ISearchAdapter, IDBTable, IDBWhereHolder {
	const NORMAL = 0;
	const WHERE_ONLY = 1;
	const AUTOFETCH = 1;
	
	/**
	 * Sets all default values on instance. Previous values are overwritten! 
	 */
	public function set_default_values();
	
	/**
 	 * Either update or insert object
 	 * 
 	 * This function handles autoupdating IDs
 	 * 
 	 * @return Status
 	 */
 	public function save();
 	
 	/**
 	 * Insert data. Autoincrement IDs will be automatically set.
 	 * 
 	 * @return Status
 	 */
 	public function insert();
 	
 	/**
 	 * Inserts if data does not exist, updates else
 	 * 
 	 * This function queries the database, that is it does not use MySQL "replace into"
 	 * It does not handle autoupdated ids. That is: It only works if all keys are set
 	 * 
 	 * @return Status 
 	 */
 	public function replace(); 	 
 	
 	/**
 	 * Update current item
 	 * 
 	 * @param int $policy IDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 */
 	public function update($policy = self::NORMAL);
 	
 	/**
 	 * Delete this object
 	 * 
 	 * @param int $policy If IDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 * 
 	 */
 	public function delete($policy = self::NORMAL);
 	
 	/**
 	 * read properties of object form array
 	 * 
 	 * @param array $values Associative array with property name as key and property value as value 
 	 */
 	public function read_from_array($values);
 	
 	/**
 	 * Validate this object
 	 * 
 	 * @return Status Error
 	 */
 	public function validate();

 	/**
 	 * Returns first result of a select for $value on $column_or_value. 
 	 * 
 	 * If $value is empty, this function assumes $column_or_value contains the value of first key column
 	 *
 	 * @param mixed $column_or_value
 	 * @param mixed $value
 	 */
 	public function get($column_or_value, $value = null);
 	
 	/**
     * Find results, either normal or crosstable
     *
     * for example
     *
     * $object = new mytable();
     * $object->ID = 1;
     * $object->find();
     *
     * @param int $policy If set to IDataObject::AUTOFETCH, first record is fetched automatically
     * @return int Number of rows found
     */
    public function find($policy = self::NORMAL);
 	
	/**
	 * Return array of elements, based on current configuration
	 * 
	 * @return array Array of IDataObject
	 */
	public function find_array();

    /**
     * fetches next row into this objects var's
     *
     * returns true on success false on failure
     *
     * Example
     * $object = new mytable();
     * $object->name = "fred";
     * $object->find();
     * $store = array();
     * while ($object->fetch()) {
     *   echo $this->ID;
     *   $store[] = $object; // builds an array of object lines.
     * }
	 *
     * @return boolean True on success
     */
    public function fetch();

	/**
 	 * Fetch and return an array
 	 * 
 	 * @return array Array of IDataObject
 	 */
 	public function fetch_array();

 	/**
 	 * Quote and escape a string
 	 * 
 	 * @param string $val 
 	 * @return string
 	 */
 	public function quote($val); 
 	
 	/**
 	 * Returns true if a both this and the given object refer to the same instance in the DB
 	 * 
 	 * The codes tests if table and key fields are identical. Note that this function 
 	 * returns true even if some properties are not identical. Use equals() to test this
 	 * 
 	 * @param IDataObject $other
 	 * @return bool 
 	 */
 	public function is_same_as($other);
 	
 	/**
 	 * Returns true if a both this and the given object are identical
 	 * 
 	 * The codes tests if table and all fields are identical. Note that this function
 	 * may return false in cases when is_same_as() returns true. 
 	 * 
 	 * @param IDataObject $other
 	 * @return bool 
 	 */
 	public function equals($other); 	
}