<?php
require_once dirname(__FILE__) . '/iaction.cls.php';
require_once dirname(__FILE__) . '/iserializable.cls.php';

/**
 * Interface for commands
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ICommand extends IAction, ISerializable  {
	/**
	 * Returns TRUE, if the command can be executed by given user
	 */
	public function can_execute($user);
	
	/**
	 * Executes commands
	 * 
	 * @return Status
	 */
	public function execute();
	
	/**
	 * Revert execution
	 */
	public function undo();
	
	/**
	 * Returns success message for this command
	 */
	public function get_success_message();
	
	/**
	 * Return result of command
	 * 
	 * @return mixed
	 */
	public function get_result();
	
	/**
	 * Returns a name that has parameters build in  
	 *
	 * @return string
	 */
	public function get_name_serialized();	
} 
