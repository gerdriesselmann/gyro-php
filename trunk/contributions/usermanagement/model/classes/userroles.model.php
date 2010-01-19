<?php
/**
 * Table Definition for userroles
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class DAOUserroles extends DataObjectBase {
	public $id;                              // int(10)  not_null primary_key unsigned auto_increment
	public $name;

	// now define your table structure.
	// key is column name, value is type
	protected function create_table_object() {
		return new DBTable(
			'userroles',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('name', 45, null, DBFieldText::NOT_NULL)
			),
			'id'
		);
	}
}
