<?php
/**
 * DAO Class for Scheduler
 *
 */
class DAOScheduler extends DataObjectBase implements IStatusHolder {
	public $id;
	public $scheduledate;
	public $reschedule_error;
	public $reschedule_success;
	public $runs_error;
	public $runs_success;
	public $action;
	public $name; 
	public $error_message;
	public $status;
	
	/**
	 * Create table description
	 *
	 * @return DBTable Object
	 */
	protected function create_table_object() {
		return new DBTable(
			'scheduler',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::NOT_NULL | DBFieldInt::UNSIGNED),
				new DBFieldDateTime('scheduledate', DBFieldDateTime::NOW, DBFieldDateTime::NOT_NULL),
				new DBFieldEnum('reschedule_error', array_keys(Scheduler::get_reschedule_policies()), Scheduler::RESCHEDULE_TERMINATOR_1, DBFieldEnum::NOT_NULL),
				new DBFieldEnum('reschedule_success', array_keys(Scheduler::get_reschedule_policies()), Scheduler::RESCHEDULE_TERMINATOR_1, DBFieldEnum::NOT_NULL),
				new DBFieldInt('runs_error', 0, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInt('runs_success', 0, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('action', 255, null, DBFieldText::NOT_NULL ),
				new DBFieldText('name', 255, null, DBFieldText::NOT_NULL ),
				new DBFieldText('error_message', DBFieldText::BLOB_LENGTH_SMALL, null, DBFieldText::NONE),
				new DBFieldEnum('status', array_keys(Scheduler::get_statuses()), Scheduler::STATUS_ACTIVE, DBFieldEnum::NOT_NULL )
			),
			'id'
		);
	}

 	/**
 	 * Validate this object
 	 * 
 	 * @return Status Error
 	 */
 	public function validate() {
 		if (empty($this->name)) {
 			$this->name = $this->action;
 		}
 		return parent::validate();
 	}
	
	//---------------
	// IStatusHolder
	//---------------
	
	/**
	 * set status
	 */	
	public function set_status($status) {
		$this->status = $status;
	}
	
	/**
	 * get status
	 */
	public function get_status() {
		return $this->status;
	}
	
	/**
	 * check if status active
	 */
	public function is_active() {
		return 
		$this->status == Scheduler::STATUS_ACTIVE 
		||
		$this->status == Scheduler::STATUS_RESCHEDULED;
	}
	
	/**
	 * check if status is disabled
	 */
	public function is_disabled() {
		return $this->status == Scheduler::STATUS_DISABLED;
	}
	
	/**
	 * Check if status is unconfirmed
	 *
	 * @return boolean
	 */
	public function is_unconfirmed() {
		return false;
	}
	
	/**
	 * Check if status is deleted
	 *
	 * @return boolean
	 */
	public function is_deleted() {
		return false;
	}
	
	//-------------------
	// Data Object Base
	//-------------------
	
	/**
	 * get actions for context
	 *
	 * @param string $context
	 * @param DAOUser $user
	 * @param string $params
	 * @return array of actions
	 */
	protected function get_actions_for_context($context, $user, $params){
		$ret = array();
		if ($context == 'list') {
			foreach(Scheduler::get_statuses() as $key => $descr) {
				if ($key === Scheduler::STATUS_ACTIVE || $key === Scheduler::STATUS_DISABLED) {
					$cmd = 'status['. $key .']';
					$descr = tr('Set'. $descr, 'scheduler');
					$ret[$cmd] = $descr;
				}
			}
		}
		return $ret;
	}

	/**
	 * Return array of user status filters. Array has filter as key and a readable description as value  
	 */
	public function get_filters() {
		$arr_stat = array();
		foreach(Scheduler::get_statuses() as $k => $d) {
			$arr_stat[strtolower($k)] = new DBFilterColumn(
				'scheduler.status', $k, $d
			); 
		}
		return array(
			new DBFilterGroup(
				'status',
				tr('Status'),
				$arr_stat
			),
		);
	}
	
	/**
	 * Return array of sortable columns. Array has column name as key and some sort of sort-column-object or an array as values  
	 */
	public function get_sortable_columns() {
		return array(
			'scheduledate' => new DBSortColumn('scheduledate', tr('Execution time', 'scheduler'), DBSortColumn::TYPE_DATE, DBSortColumn::ORDER_FORWARD, true),
		);
	}

	/**
	 * Get the column to sort by default
	 */
	public function get_sort_default_column() {
		return 'scheduledate';
	}
	
}