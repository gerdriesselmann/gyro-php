<?php
Load::models('notifications');

/**
 * Manage norification subcriptions	
 */
class DAONotificationsexceptions extends DataObjectCached {
	public $id;
	public $id_user;
	public $source;
	public $source_id;

	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'notificationsexceptions',
			array(
				new DBFieldInt('id', null, DBFieldInt::PRIMARY_KEY),
				new DBFieldInt('id_user', null, DBFieldInt::FOREIGN_KEY), 
				new DBFieldText('source', 100, Notifications::SOURCE_APP, DBField::NOT_NULL),
				new DBFieldInt('source_id', null, DBFieldInt::UNSIGNED | DBField::NOT_NULL), 
			),
			'id',
			new DBRelation('users',	new DBFieldRelation('id_user', 'id'))
		);		
	}	

	public function get_user() {
		return Users::get($this->id_user);
	}
}