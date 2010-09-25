<?php
/**
 * DAO class for Binaries data
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class DAOBinariesdata extends DataObjectBase {
	public $id_binary;
	public $data;
	
	/**
	 * Create table description
	 */
	protected function create_table_object() {
		return new DBTable(
			'binariesdata',
			array(
				new DBFieldInt('id_binary', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),				
				new DBFieldBlob('data', DBFieldBlob::BLOB_LENGTH_LARGE, null, DBFieldBlob::NOT_NULL),				 
			),
			'id_binary'
		);
	}
}