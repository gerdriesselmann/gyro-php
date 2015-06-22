<?php
if (!defined('GYRO_COMMAND_SEP')) define('GYRO_COMMAND_SEP', '_');
if (!defined('GYRO_COMMAND_ID_SEP')) define('GYRO_COMMAND_ID_SEP', ',');

/**
 * Base implementation for commands
 * 
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class CommandBase implements ICommand {
	/**
	 * Instance to work upon
	 *
	 * @var mixed
	 */
	protected $obj;
	/**
	 * Parameters for command
	 * 
	 * @param mixed
	 */
	protected $params;
	/**
	 * Result of command
	 *
	 * @var mixed
	 */
	protected $result = false;

	/**
	 * @param mixed $obj The object the command acts upon. Can also be a string that indicated a type, e.g.
	 * @param mixed $params The parameters for the command
	 */
	public function __construct($obj = null, $params = false) {
		$this->obj = $obj;
		$this->params = $params;
	}
	
	// ************************************
	// ICommand
	// ************************************
	
	/**
	 * Returns TRUE, if the command can be executed by given user
	 */
	public function can_execute($user) {
		$name = $this->get_name();
		$ret = true;
		if (!empty($name)) {
			$ret = AccessControl::is_allowed($name, $this->get_instance(), $this->get_params(), $user);
		}
		return $ret;
	}
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute() {
		$ret = new Status();
		EventSource::Instance()->invoke_event('command_executed', $this, $ret);
		return $ret;
	}
	
	/**
	 * Revert execution
	 */
	public function undo() {
		// Do nothing here
	}
		
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return '';
	}
	
	/**
	 * Returns a description of this command
	 */
	public function get_description() {
		return $this->get_name();
	} 
	
	/**
	 * Returns the object this actionworks upon
	 *
	 * @return mixed
	 */
	public function get_instance() {
		return $this->obj;
	}
	
	/**
	 * Returns params
	 *
	 * @return mixed
	 */
	public function get_params() {
		return $this->params;
	}
	
	/**
	 * Returns success message for this command
	 */
	public function get_success_message() {
		return '';
	}	

	/**
	 * Return result of command
	 * 
	 * @return mixed
	 */
	public function get_result() {
		return $this->result;
	}
	
	// ************************************
	// ISerializable
	// ************************************
	
	/**
	 * Make this command available for text processing systems (that is: the HTML code)
	 * 
	 * The code returned looks like this: cmd_{instance type}_{instance id,[instance 2nd id, ..]}_{command name}_{command param}[_{command param}...]
	 */
	public function serialize() {
		$arr = array(
			'cmd',
			$this->serialize_instance_name($this->get_instance()),
			$this->get_name_serialized()
		);
		return implode(GYRO_COMMAND_SEP, $arr);
	}	

	/**
	 * Returns a name that has parameters build in  
	 *
	 * @return string
	 */
	public function get_name_serialized() {
		$arr = array(
			$this->get_name()
		);
		$params = $this->serialize_params($this->get_params());
		if ($params) {
			$arr[] = $params;
		}
		return implode(GYRO_COMMAND_SEP, $arr);
	}	


	protected function serialize_instance_name($inst) {
		$ret = '';
		if ($inst instanceof IDataObject) {
			$arr = array(
				$inst->get_table_name()
			);
			$arr_id = array();
			foreach ($inst->get_table_keys() as $key => $field) {
				$arr_id[] = $inst->$key;
			}
			$arr[] = implode(GYRO_COMMAND_ID_SEP, $arr_id);
			$ret = implode(GYRO_COMMAND_SEP, $arr);
		}
		else {
			$ret = Cast::string($inst);
		}
		return $ret;		
	}
	
	protected function serialize_params($params) {
		$ret = '';
		if (is_array($params)) {
			$arr = array();
			foreach($params as $key => $value) {
				$arr[] = $key;
				$arr[] = $value;
			}
			$ret = implode(GYRO_COMMAND_SEP, $arr);
		}
		else {
			$ret = Cast::string($params);
		}
		return $ret;
	}
	
	// ************************************
	// Helper functions
	// ************************************
	
	/**
	 * Set result
	 *
	 * @param mixed $result
	 */
	protected function set_result($result) {
		$this->result = $result;
	}
	
	/**
	 * Create an illegal data Status class
	 *
	 * @return Status
	 */
	protected function illegal_data_error() {
		return new Status(tr('Illegal Command Data for Command %cmd%', 'core', array('%cmd%' => get_class($this))));
	}
} 
