<?php
/**
 * Created on 26.11.2006
 *
 * @author Gerd Riesselmann
 */

/**
 * Table Definition for session
 */
class DAOHijackaccountsavedsessions extends DataObjectBase {
	public $id;                   
	public $id_user;        	 
	public $data;                            
	public $expirationdate;                  

	// now define your table structure.
	// key is column name, value is type
	protected function create_table_object() {
	    return new DBTable(
	    	'hijackaccountsavedsessions',
			array(
				new DBFieldText('id', 40, null, DBField::NOT_NULL),
				new DBFieldInt('id_user', null, DBFieldInt::UNSIGNED | DBField::NOT_NULL),
				new DBFieldSerialized('data', DBFieldText::BLOB_LENGTH_LARGE),
				new DBFieldDateTime('expirationdate', null, DBField::NOT_NULL),				
			),
			'id',
			new DBRelation(
				'users',
				new DBFieldRelation('id_user', 'id')
			)
	    );
	}
	
	/**
	 * Returns the user that hijakced
	 * 
	 * @return DAOUsers
	 */
	public function get_user() {
		return Users::get($this->id_user);
	}
}
