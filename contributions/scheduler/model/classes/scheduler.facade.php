<?php
/**
 * Scheduler facade class
 *
 */
class Scheduler  {
	const STATUS_ACTIVE = 'ACTIVE';
	const STATUS_PROCESSING = 'PROCESSING';
	const STATUS_DISABLED = 'DISABLED';
	const STATUS_ERROR = 'ERROR';
	const STATUS_RESCHEDULED = 'RESCHEDULED';
	
	const RESCHEDULE_TERMINATOR_1 = 'TERMINATOR1';
	const RESCHEDULE_TERMINATOR_2 = 'TERMINATOR2';
	const RESCHEDULE_TERMINATOR_3 = 'TERMINATOR3';
	const RESCHEDULE_DIE_HARD_1 = 'DIEHARD1';
	const RESCHEDULE_DIE_HARD_2 = 'DIEHARD2';
	const RESCHEDULE_DIE_HARD_3 = 'DIEHARD3';
	const RESCHEDULE_24_HOURS = '24HOURS';
	const RESCHEDULE_RUSHHOUR1 = 'RUSHHOUR1';
	const RESCHEDULE_RUSHHOUR2 = 'RUSHHOUR2';
	const RESCHEDULE_RUSHHOUR3 = 'RUSHHOUR3';

	const REMOVE_LIKE = 'LIKE';
	const REMOVE_EXACT = '=';
	
	/**
	 * Create a new task for scheduler
	 *
	 * @param array $params
	 * @param Task created $result
	 * @param If true prior tasks with same action will get deleted $exclusive
	 * @param DAOUsers $user
	 * @return Status
	 */
	public static function create_task($params, &$result, $exclusive = false) {
		$params['exclusive'] = $exclusive;
		$params['runs_error'] = 0;
		$params['runs_success'] = 0;
		$cmd = CommandsFactory::create_command('scheduler', 'create', $params);
		$ret = $cmd->execute();
		$result = $cmd->get_result();
		return $ret;
	}
	
	/**
	 * get all status of the scheduler
	 *
	 * @return array
	 */
	public static function get_statuses()  {
		return array(
			self::STATUS_ACTIVE => tr(self::STATUS_ACTIVE, 'scheduler'),
			self::STATUS_PROCESSING => tr(self::STATUS_PROCESSING, 'scheduler'),
			self::STATUS_DISABLED => tr(self::STATUS_DISABLED, 'scheduler'),
			self::STATUS_ERROR => tr(self::STATUS_ERROR, 'scheduler'),
			self::STATUS_RESCHEDULED => tr(self::STATUS_RESCHEDULED, 'scheduler'),
		);
	}
	
	/**
	 * Returns list of all available reschedule policies  
	 *
	 * @return array
	 */
	public static function get_reschedule_policies() {
		return array(
			self::RESCHEDULE_TERMINATOR_1 => tr(self::RESCHEDULE_TERMINATOR_1, 'scheduler'),
			self::RESCHEDULE_TERMINATOR_2 => tr(self::RESCHEDULE_TERMINATOR_2, 'scheduler'),
			self::RESCHEDULE_TERMINATOR_3 => tr(self::RESCHEDULE_TERMINATOR_3, 'scheduler'),
			self::RESCHEDULE_DIE_HARD_1 => tr(self::RESCHEDULE_DIE_HARD_1, 'scheduler'),
			self::RESCHEDULE_DIE_HARD_2 => tr(self::RESCHEDULE_DIE_HARD_2, 'scheduler'),
			self::RESCHEDULE_DIE_HARD_3 => tr(self::RESCHEDULE_DIE_HARD_3, 'scheduler'),
			self::RESCHEDULE_24_HOURS => tr(self::RESCHEDULE_24_HOURS, 'scheduler'),
			self::RESCHEDULE_RUSHHOUR1 => tr(self::RESCHEDULE_RUSHHOUR1, 'scheduler'),
			self::RESCHEDULE_RUSHHOUR2 => tr(self::RESCHEDULE_RUSHHOUR2, 'scheduler'),
			self::RESCHEDULE_RUSHHOUR3 => tr(self::RESCHEDULE_RUSHHOUR3, 'scheduler'),			
		);
	}

	/**
	 * Create an adfapter for scheduled tasks
	 *
	 * @return DAOScheduler
	 */
	public static function create_adapter() {
		$dao = new DAOScheduler();
		$dao->sort('scheduledate', ISearchAdapter::ASC);
		return $dao;		
	}
	
	/**
	 * Return all Scheduled Actions
	 *
	 * @return Array of DAOScheduler
	 */
	public static function get_all() {
		$dao = self::create_adapter();
		return $dao->find_array();
	}
	
	/**
	 * Return next task to execute
	 * 
	 * @return DAOScheduler
	 */
	public static function get_next_task() {
		DB::start_trans();
		/* @var $dao DAOScheduler */
		$dao = self::create_adapter();
		$dao->add_where('status', DBWhere::OP_IN, array(self::STATUS_ACTIVE, self::STATUS_RESCHEDULED));
		$dao->add_where('scheduledate', '<=', time());
		$dao->limit(0, 1);
		$query = $dao->create_select_query();
		$query->set_policy(DBQuerySelect::FOR_UPDATE);
		
		$ret = false;
		if ($dao->query($query->get_sql(), DAOScheduler::AUTOFETCH)) {
			$dao->status = self::STATUS_PROCESSING;
			$dao->update();
			$ret = clone($dao);
		}
		
		DB::commit();
		return $ret;
	}
	
	/**
	 * Reschedule the given task
	 *
	 * @param DAOScheduler $task
	 * @return Status
	 */
	public static function reschedule(DAOScheduler $task) {
		$cmd = CommandsFactory::create_command($task, 'reschedule', false);
		return $cmd->execute();
	}
	
	/**
	 * Remove tasks for given action
	 *
	 * @param string $action
	 * @param string $op
	 * @return Status
	 */
	public static function remove_tasks($action, $op = self::REMOVE_EXACT) {
		$ret = new Status();

		Load::commands('generics/massdelete');
		$db_op = '=';
		switch ($op) {
			case self::REMOVE_LIKE:
				$action .= '%';
				$db_op = DBWhere::OP_LIKE;
				break;
			default:
				break;
		}
		$where = new DBWhere(new DAOScheduler(), 'action', $db_op, $action);
		$cmd = new MassDeleteCommand('scheduler', array($where));
		$ret->merge($cmd->execute());
		
		return $ret;
	}	
}

