<?php
/**
 * An enum field
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldEnum extends DBField {
	/**
	 * Allowed values
	 *
	 * @var array
	 */
	protected $allowed;
	
	public function __construct($name, $allowed = array(), $default_value = null, $policy = self::NONE) {
		parent::__construct($name, $default_value, $policy);
		$this->allowed = Arr::force($allowed);
	}
	
	/**
	 * Returns true, if the value passed fits the fields restrictions
	 *
	 * @param string $value
	 * @return Status
	 */
	public function validate($value) {
		$ret = parent::validate($value);
		if ($ret->is_ok() && !is_null($value)) {
			if (!in_array($value, $this->allowed)) {
				$ret->append(tr(
					'Value %val not allowed on %field',
					'core',
					array(
						'%field' => tr($this->get_field_name(), 'global'),
						'%val' => $value
					)
				));
			}
		}
		return $ret;
	}	
}