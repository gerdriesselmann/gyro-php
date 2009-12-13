<?php
/**
 * Simple implementation of an action
 * 
 * @ingroup Behaviour
 */
class ActionBase implements IAction {
	/**
	 * Instance to work upon
	 *
	 * @var mixed
	 */
	protected $inst = null;
	/**
	 * Title of this action
	 *
	 * @var string
	 */
	protected $title = '';
	/**
	 * Description for this action
	 *
	 * @var string
	 */
	protected $description = '';
	
	public function __construct($item, $title, $description = '') {
		$this->inst = $item;
		$this->title = $title;
		$this->description = $description;		
	}
	
	/**
	 * Returns title of action.
	 * 
	 * @return string
	 */
	public function get_name() {
		return $this->title;
	}
	
	/**
	 * Returns a description of this action
	 * 
	 * @return string
	 */
	public function get_description() {
		return $this->description; 	
	}
	
	/**
	 * Returns the object this actionworks upon
	 *
	 * @return mixed
	 */
	public function get_instance() {
		return $this->inst;
	}		
}
