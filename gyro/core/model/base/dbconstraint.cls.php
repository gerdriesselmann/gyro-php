<?php
Load::components('policyholder');

/**
 * Base class for constraints
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBConstraint extends PolicyHolder implements IDBConstraint {
	const NONE = 0;
	/**
	 * The table this contraint holds for
	 *
	 * @var string
	 */
	protected $tablename;
	/**
	 * Array of field names
	 *
	 * @var array
	 */
	protected $arr_fields = array();

	public function __construct($tablename, $fields = null, $policy = self::NONE) {
		parent::__construct($policy);
		$this->tablename = $tablename;
		
		if (!empty($fields)) {
			$this->arr_fields = Arr::force($fields);
		}
	}

	/**
	 * Returns field taking parts in this relation
	 * 
	 * @return array Array with column name as value 
	 */
	public function get_fields() {
		return $this->arr_fields;
	}
	
	/**
	 * Check if constraints are fullfiled.  
	 *
	 * @param array Cosntraint Column data Associative array of form fieldname => fieldvalue
	 * @param array Key Column Data Associative array of form fieldname => fieldvalue
	 * @return Status
	 */
	public function validate($arr_fields, $arr_keys) {
		return new Status();
	}
}
