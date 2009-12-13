<?php
/**
 * A field holding a reference to an instance
 */
class DBFieldInstanceReference extends DBFieldText {
	public function __construct($name, $policy = self::NOT_NULL) {
		parent::__construct($name, 255, null, $policy);
	}
		
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param mixed $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = new Status;
		if (is_null($value)) {
			$ret->merge(parent::validate($value));
		}
		else if (!$value instanceof IDataObject) {
			$ret->append(tr(
				'%field must be a data object', 
				'instancereference', 
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
			return parent::format($value);
		}
		
		$value_to_format = '';
		if ($value instanceof IDataObject) {
			$value_to_format = InstanceReferenceSerializier::instance_to_string($value);
		}
		else if (is_string($value)) {
			$value_to_format = $value;
		}
		return parent::format($value_to_format);
	}

	/**
	 * Transform result from SELECT to native
	 * 
	 * @param mixed $value
	 * @return mixed    
	 */
	public function convert_result($value) {
		$ret = InstanceReferenceSerializier::string_to_instance($value);
		return $ret;
	}	
}
