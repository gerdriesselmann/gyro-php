<?php
/**
 * Table Definition for permanentlogins
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class DAOPermanentlogins extends DataObjectBase {
	public $code;                            // string(50)  not_null primary_key
	public $id_user;                         // int(10)  not_null multiple_key unsigned
	public $expirationdate;                  // datetime(19)  binary
	
	
	protected function create_table_object() {
		return new DBTable(
			'permanentlogins',
			array(
				new DBFieldText('code', 50, null, DBFieldText::NOT_NULL),
				new DBFieldInt('id_user', null, DBFieldInt::NOT_NULL),
				new DBFieldDateTime('expirationdate')
			),
			'code'
		);
	}
		
	
	/**
	 * Loads item, and returns true, if a valid one was fond, false otherwise
	 */
	public function get_valid($code) {
		if ($this->get($code)) {
			return ($this->expirationdate > time());
		}
		return false;
	}
}
