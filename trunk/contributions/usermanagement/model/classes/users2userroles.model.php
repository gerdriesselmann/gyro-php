<?php
/**
 * Table Definition for users
 */

class DAOUsers2userroles extends DataObjectBase {
	public $id_user;
	public $id_role;

	// now define your table structure.
	// key is column name, value is type
	function create_table_object() {
		return new DBTable(
			'users2userroles',
			array(
				new DBFieldInt('id_user', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInt('id_role', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
			),
			array(
				'id_user',
				'id_role'
			),
			array(
				new DBRelation(
					'users',
					new DBFieldRelation('id_user', 'id')
				),
				new DBRelation(
					'userroles',
					new DBFieldRelation('id_role', 'id')
				)
			)
		);
	}
}
