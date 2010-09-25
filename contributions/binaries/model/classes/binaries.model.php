<?php
/**
 * DAO class for Binaries
 * 
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class DAOBinaries extends DataObjectTimestampedCached {
	public $id;
	public $name;
	public $mimetype;
	
	/**
	 * Create table description
	 */
	protected function create_table_object() {
		return new DBTable(
			'binaries',
			array_merge(array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),				
				new DBFieldText('name', 200, null, DBFieldText::NOT_NULL),
				new DBFieldText('mimetype', 100, null, DBFieldText::NOT_NULL),
				), $this->get_timestamp_field_declarations()
			),
			'id'
		);
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
 	
 	/**
 	 * Return data - that is the bytes forming this binary
 	 * 
 	 * @return string
 	 */
 	public function get_data() {
 		$data = DB::get_item('binariesdata', 'id_binary', $this->id);
 		return $data ? $data->data : '';
 	}
 	
 	/**
	 * Keep compatability to older version, where data was a member, not outsourced to binariesdata
 	 */
 	public function __get($name) {
 		if ($name == 'data') {
 			return $this->get_data();
 		}
 		return null;
 	}
}