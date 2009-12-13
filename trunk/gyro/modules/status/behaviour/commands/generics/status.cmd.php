<?php
/**
 * Command to set status
 * 
 * Expects new status as param.
 * 
 * Creates delegate, a command named Status[Newstatus][Object]Command, located in a file named
 * status.[newstatus].cmd.php.
 * 
 * If no such command is found, it falls back to 
 * 
 * - StatusAny[Object]Command
 * - StatusAnyCommand in commands/generics
 * 
 * If an application or module provides an overload, the command should be derived from the 
 * generic StatusAnyCommand. 

 * Example: Given a model users that has states UNCONFIRMED and ACTIVE and you want to do
 * something special, if status becomes ACTIVE. Create a class StatusActiveUsersCommand in file
 * behaviour/commands/users/status.active.cmd.php, derive it from generic StatusAnyCommand and overload
 * do_execute().
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
 */
class StatusCommand extends CommandDelegate {
	/**
	 * Constructor
	 *
	 * @param unknown_type $obj
	 * @param unknown_type $params
	 */
	public function __construct($obj, $params) {
		$params = Arr::force($params);
		$new_status = String::to_lower(Arr::get_item($params, 0, ''));
		if ($new_status == '') {
			throw new Exception(tr('Status command called with empty status', 'status'));
			exit;
		}
		$action = 'status.' . $new_status;
		$delegate = CommandsFactory::create_command($obj, $action, $params);
		if (empty($delegate)) {
			$delegate = CommandsFactory::create_command($obj, 'status.any', $params);			
		}
		parent::__construct($delegate);
	}
}
