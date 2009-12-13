<?php
/**
 * Base class to represent a field
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBField implements IDBField {
	const NOT_NULL = 1;
	
	/**
	 * Name of the field
	 *
	 * @var string
	 */
	protected $name = '';
	/**
	 * Default value
	 *
	 * @var mixed
	 */
	protected $default_value = null;
	/**
	 * Is a null value allowed here?
	 *
	 * @var bool
	 */
	protected $policy;
	/**
	 * Connection for this field
	 * 
	 * @var string|IDBDriver
	 */
	protected $connection;
	
	public function __construct($name, $default_value = null, $policy = self::NONE, $connection = DB::DEFAULT_CONNNECTION) {
		$this->name = $name;
		$this->default_value = $default_value;
		$this->policy = $policy;
		$this->connection = $connection;
	}
	
	/**
	 * Returns name
	 *
	 * @return string
	 */
	public function get_field_name() {
		return $this->name;
	}

	/**
	 * Returns the default value for this field
	 *
	 * @return mixed
	 */
	public function get_field_default() {
		return $this->default_value;
	}

	/**
	 * Returns true, if field has default value
	 */
	public function has_default_value() {
		return !is_null($this->default_value);
	}
	
	/**
	 * Returns true, if null values are allowed
	 *
	 * @return bool
	 */
	public function get_null_allowed() {
		return !Common::flag_is_set($this->policy, self::NOT_NULL);
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = new Status();
		if (is_null($value) && !$this->has_default_value() && !$this->get_null_allowed()) {
			$ret->append(tr(
				'%field may not be empty', 
				'core', 
				array(
					'%field' => tr($this->get_field_name(), 'global'),
				)
			));
		}
		return $ret;
	}
	
	/**
	 * Reformat passed value to DB format
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function format($value) {
		if (is_null($value)) {
			return 'NULL';
		}
		else {
			return $this->quote($value);
		}
	}
	
	/**
	 * Format for use in WHERE clause
	 *  
	 * @param mixed $value
	 * @return string
	 */
	public function format_where($value) {
		return $this->format($value);
	}

	/**
	 * Allow replacements for field in select from clause
	 */
	public function format_select() {
		return $this->get_field_name();
	}	

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		return $value;
	}
	
	/**
	 * Reads value from array (e.g $_POST) and converts it into something meaningfull
	 */
	public function read_from_array($arr) {
		return Arr::get_item($arr, $this->get_field_name(), null);
	}
	
	/**
	 * Set connection of field
	 * 
	 * @param string|IDBDriver $connection
	 * @return void
	 */
	public function set_connection($connection) {
		$this->connection = $connection;
	}

	/**
	 * Returns connection
	 * @return string|IDBDriver
	 */
	protected function get_connection() {
		return $this->connection;
	}

	/**
	 * Return policy
	 *
	 * @return int
	 */
	public function get_policy() {
		return $this->policy;	
	}
	
	/**
	 * Set policy
	 *
	 * @param int $policy
	 */
	public function set_policy($policy) {
		$this->policy = $policy;
	}
	
	/**
	 * Returns true, if client has given policy
	 *
	 * @param int $policy
	 * @return bool
	 */
	public function has_policy($policy) {
		$ret = Common::flag_is_set($this->policy, $policy);
		return $ret;
	}
	
	/**
	 * Quote value
	 */
	protected function quote($value) {
		return DB::quote(Cast::string($value), $this->get_connection()); 
	}
}
