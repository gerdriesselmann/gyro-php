<?php
/**
 * DAO class for Binaries
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class DAOBinaries extends DataObjectBase {
	public $id;
	public $name;
	public $mimetype;
	public $data;
	public $creationdate;
	public $modificationdate; 
	
	/**
	 * Create table description
	 */
	protected function create_table_object() {
		return new DBTable(
			'binaries',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),				
				new DBFieldText('name', 200, null, DBFieldText::NOT_NULL),
				new DBFieldText('mimetype', 100, null, DBFieldText::NOT_NULL),
				new DBFieldBlob('data', DBFieldBlob::BLOB_LENGTH_LARGE, null, DBFieldBlob::NOT_NULL),				 
				new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::NOT_NULL),
				new DBFieldDateTime('modificationdate', DBFieldDateTime::NOW, DBFieldDateTime::TIMESTAMP | DBFieldDateTime::NOT_NULL),
			),
			'id'
		);
	}
	
	/**
 	 * Insert data. Autoincrement IDs will be automatically set.
 	 * 
 	 * @return Status
 	 */
 	public function insert() {
 		$this->modificationdate = time();
 		$this->creationdate = time();
 		return parent::insert();
 	}

 	/**
 	 * Update current item
 	 * 
 	 * @param int $policy If DBDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 */
 	public function update($policy = self::NORMAL) {
 		$this->modificationdate = time();
 		return parent::update($policy);	
 	}	
 	
 	/**
 	 * Returns the base type of this binary's mime type e.g. "text" for text/html
 	 *
 	 * @return string
 	 */
 	public function get_mime_base_type() {
 		$tmp = explode('/', $this->mimetype);
 		return $tmp[0];
 	}
}