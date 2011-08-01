<?php
/**
 * Table Definition for form validations
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DAOFormvalidations extends DataObjectBase {
	public $token;                           // string(30)  not_null primary_key
	public $name;                            // string(30)  not_null primary_key
	public $sessionid;
	public $expirationdate;                  // datetime(19)  binary

	protected function create_table_object() {
	    return new DBTable(
	    	'formvalidations',
			array(
				new DBFieldText('token', 35, null, DBField::NOT_NULL),
				new DBFieldText('name', 35, null, DBField::NOT_NULL),
				new DBFieldText('sessionid', 60, '', DBField::NONE),
				new DBFieldDateTime('expirationdate', null, DBField::NOT_NULL),				
			),
			array('token', 'name')
	    );
	}

	public function is_valid_for_at_least($minutes) {
		return $this->expirationdate > (time() + $minutes * 60);
	}
}
