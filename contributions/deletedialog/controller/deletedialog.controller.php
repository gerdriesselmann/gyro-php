<?php
/**
 * This controller catches delete commands
 * 
 * @author Gerd Riesselmann
 * @ingroup DeleteDialog
 */
class DeleteDialogController extends ControllerBase {
	/**
	 * returns array of routes
	 * 
	 * @return array 
	 */
	public function get_routes() {
		$ret = array(
			new CommandsRoute('https://process_commands/{model:s}/{id:ui>}/delete', $this, 'deletedialog_cmd_handler'),
			new ParameterizedRoute('https://deletedialog/approve/{_model_:s}/{id:ui>}', $this, 'deletedialog_approve')
		);
		return $ret;
	}
	
	/**
	 * HAndle the command, that is: Display an approval dialog
	 *
	 * @param PageData $page_data
	 * @param string $model
	 * @param string $id
	 */
	public function action_deletedialog_cmd_handler(PageData $page_data, $model, $id) {
		$page_data->in_history = false;
		$dao = $this->get_instance($model, $id);
		if ($dao === false) {
			return CONTROLLER_NOT_FOUND;
		}
		
		Url::create(ActionMapper::get_url('deletedialog_approve', $dao))->redirect();
	}

	/**
	 * Handle link approval
	 *
	 * @param PageData $page_data
	 * @param string $_model_
	 * @param string $id
	 */
	public function action_deletedialog_approve(PageData $page_data, $_model_, $id) {
		$page_data->in_history = false;
		
		$dao = $this->get_instance($_model_, $id);
		if ($dao === false) {
			return CONTROLLER_NOT_FOUND;
		}
		
		$cmd = CommandsFactory::create_command($dao, 'delete', false);
		if (!$cmd->can_execute(false)) {
			return CONTROLLER_ACCESS_DENIED;
		}
		
		$formhandler = $this->create_formhandler();
		if ($page_data->has_post_data()) {
			$err = $formhandler->validate();
			if ($err->is_ok()) {
				if ($page_data->get_post()->get_item('cancel', false) !== false) {
					$err = new Message(tr('Deletion has been canceled by user', 'deletedialog'));
				}
				else {
					$err->merge($cmd->execute());
				}
			}
			$formhandler->finish($err, tr('The instance has been deleted', 'deletedialog'));
		}
		else {
			$this->render_view($page_data, $formhandler, $dao);	
		}
	}
	
	/**
	 * Returns instance passed as coded string
	 *
	 * @param string $model
	 * @param string $id
	 * @return IDataObject
	 */
	protected function get_instance($model, $id) {
		$ret = false;
		$dao = DB::create($model);
		if (!empty($dao)) {
			$arr_id = explode(GYRO_COMMAND_ID_SEP, $id);
			foreach ($dao->get_table_keys() as $key => $field) {
				$dao->$key = array_shift($arr_id);
			}
			
			if ($dao->find(IDataObject::AUTOFETCH) == 1) {
				// Not zero or many items
				$ret = $dao;					
			}
		}
		return $ret;
	}
	
	/**
	 * Create a formhandler
	 *
	 * @return FormHandler
	 */
	protected function create_formhandler() {
		Load::tools('formhandler');
		return new FormHandler('dlgdeleteapprove');		
	}
	
	/**
	 * Create an render approval view
	 *
	 * @param FormHandler $formhandler
	 * @param IDataObject $instance
	 */
	protected function render_view(PageData $page_data, FormHandler $formhandler, $instance) {
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'deletedialog/approve', $page_data);
		$formhandler->prepare_view($view);
		$view->assign('instance', $instance); 
		$view->render();		
	}
}