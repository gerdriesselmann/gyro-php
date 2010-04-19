<?php
/**
 * Table Definition for confirmations
 * 
 * @author Gerd Riesselmann
 * @ingroup Confirmations
 */
class DAOConfirmations extends DataObjectBase 
{
	public $id;
	public $id_item;
	public $code;
	public $data;   
	public $action;
	public $expirationdate;
		
	protected function create_table_object() {
	    return new DBTable(
	    	'confirmations',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInt('id_item', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('code', 50, null, DBFieldText::NOT_NULL),
				new DBFieldText('data', DBFieldText::BLOB_LENGTH_SMALL),
				new DBFieldText('action', 20, null, DBFieldText::NOT_NULL),
				new DBFieldDateTime('expirationdate', null, DBFieldDateTime::NOT_NULL),
			),
			'id'			
	    );
	}
	
	/**
	 * Creates a handler for this confirmation
	 *
	 * @return IConfirmationHandler
	 */
	public function create_handler() {
		Load::classes_in_directory('behaviour/confirmationhandlers', $this->action, 'confirmationhandler', false);
		$cls = Load::filename_to_classname($this->action, 'ConfirmationHandler');
		if (class_exists($cls)) {
			return new $cls($this);
		}
		// Default implementation handles missing confirmation...
		return new ConfirmationHandlerBase(false);				
	}
}
