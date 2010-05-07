<?php
/**
 * Controller to intercept status change commands
 * 
 * All status change commands are send to command StatusCommand.
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
 */
class StatusCommandsController extends ControllerBase {
	/**
	 * Return routes
	 */
	public function get_routes() {
		return array(
			new CommandsRoute('https://process_commands/{model:s}/{id:ui>}/status/{newstatus:s}', $this, 'status_cmd_handler'),
		);
	}
	
	public function action_status_cmd_handler(PageData $page_data, $model, $id, $newstatus) {
		$dao = DB::create($model);
		if (empty($dao)) {
			// No such type
			return CONTROLLER_NOT_FOUND;		
		}
		$arr_id = explode(GYRO_COMMAND_ID_SEP, $id);
		foreach ($dao->get_table_keys() as $key => $field) {
			$dao->$key = array_shift($arr_id);
		}
		
		if ($dao->find(IDataObject::AUTOFETCH) != 1) {
			// Zero or many items
			return CONTROLLER_NOT_FOUND;					
		}
		
		$cmd = CommandsFactory::create_command($dao, 'status', $newstatus);
		if (!$cmd->can_execute(false)) {
			return CONTROLLER_ACCESS_DENIED;
		}
		
		$status = $cmd->execute();
		if ($status->is_ok()) {
			$status = new Message(tr('The status has been changed', 'status'));
		}
		History::go_to(0, $status);
		exit; 
	}
}
