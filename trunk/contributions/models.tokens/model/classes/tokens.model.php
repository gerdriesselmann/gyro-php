<?php
/**
 * Tokens DAO class
 */
class DAOTokens extends DataObjectBase {
	public $token;
	public $expirationdate;
	public $creationdate;

	/**
	 * Create the table object describing this dataobejcts table
	 */
	protected function create_table_object() {
		return new DBTable(
			'tokens',
			array(
				new DBFieldText('token', 40, null, DBField::NOT_NULL),
				new DBFieldDateTime('expirationdate', null, DBField::NOT_NULL),
				new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL)
			),
			'token'
		);		
	}
	
}