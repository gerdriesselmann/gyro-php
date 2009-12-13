<?php
/**
 * Interface for all DAO objects having actions
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */ 
interface IActionSource {
	/**
	 * Get all actions
	 *
	 * @param dao_User The current user or false if no user is logged on
	 * @param String The context. Some actions may not be approbiate in some situations. For example, 
	 *               action 'edit' should not be returned when editing. THis can be expressed throug a 
	 *               context named 'edit'. Default context is 'view'.   
	 * @return Array Associative array with action url as key and action description as value 
	 */
	public function get_actions($user, $context = 'view', $params = false);
	
	/**
	 * Identify for generic actionh processing
	 * 
	 * @return string
	 */
	public function get_action_source_name();
}
