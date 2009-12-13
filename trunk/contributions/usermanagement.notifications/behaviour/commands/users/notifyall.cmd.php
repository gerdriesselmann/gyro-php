<?php
/**
 * Send a notification to ALL users
 */
class NotifyallUsersCommand extends CommandTransactional {
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'notifyall';
	}

	/**
	 * Do it
	 * 
	 * @see core/behaviour/base/CommandTransactional#do_execute()
	 */
	protected function do_execute() {
		$ret = new Status();
		Load::models('notifications');
		
		$params = $this->get_params();
		$ret->merge($this->validate($params));
		if ($ret->is_ok()) {		
			$title = Arr::get_item($params, 'title', '');
			$message = Arr::get_item($params, 'message', '');
			
			$ret->merge($this->mass_insert($title, $message));
		}
		return $ret;
	}

	/**
	 * Create the insert query an run it
	 */
	protected function mass_insert($title, $message) {
		$user = new DAOUsers();
		$notification = new DAONotifications();
		
		$select = $user->create_select_query();
		$select->set_fields(array(
			'id' => 'id_user',
			$notification->quote($title) => 'title',
			$notification->quote($message) => 'message',
		));
		
		$insert = $notification->create_insert_query();
		$insert->set_fields(array(
			'id_user', 'title', 'message'
		)); 
		$insert->set_select($select);
		return DB::execute($insert->get_sql(), $notification->get_table_driver());
	}
	
	/**
	 * Check if there are 
	 */
	protected function validate($params) {
		// Prepare a valid user id;
		$user = new DAOUsers();
		$user->limit(0, 1);
		$user->find(DAOUsers::AUTOFETCH);
		$params['id_user'] = $user->id;
		
		$test = new DAONotifications();
		$test->read_from_array($params);
		return $test->validate();		
	} 
}