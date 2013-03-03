<?php
/**
 * Sequences DAO class
 */
class DAOSequences extends DataObjectBase {
	public $slot;
	public $current;

	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'sequences',
			array(
				new DBFieldText('slot', 20, null, DBField::NOT_NULL),
				new DBFieldInt('current', 0, DBFieldInt::UNSIGNED | DBField::NOT_NULL)
			),
			'slot'
		);		
	}

	/**
	 * @return Status
	 *
	 * Commit the sequence, that is set the current value and
	 * release the table lock.
	 */
	public function commit() {
		$cmd = $this->create_commit_command();
		return $cmd->execute();
	}

	/**
	 * @return ICommand
	 *
	 * Create Command to commit the sequence, that is set the current value and
	 * release the table lock.
	 */
	public function create_commit_command() {
		$cmd = CommandsFactory::create_command($this, 'update', array());
		return $cmd;
	}
}