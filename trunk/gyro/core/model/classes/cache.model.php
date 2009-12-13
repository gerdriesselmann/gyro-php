<?php
/**
 * Table Definition for cache entries
 * 
 * The cache stores the cached content gzipped to save database space 
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DAOCache extends DataObjectBase {
	public $id;                              // int(10)  not_null primary_key unsigned auto_increment
	public $key0;                            // string(250)
	public $key1;                            // string(250)
	public $key2;                            // string(250)	
	public $content_gzip;					// LONGBLOB
	public $data;
	public $creationdate; 					// TIMESTAMP
	public $expirationdate;                  // DATETIME
	
	/**
	 * Set cache keys
	 * 
	 * @param Array Array of keys that are set as key0, key1 etc
	 * @param Boolean If TRUE a phrase keyX like 'valueX%' etc is added to where clause for each key
	 */
	public function set_keys($keys, $force_where = false) {
		$c = count($keys);
		if ($c > 3) {
			$c = 3;
		}
		for ($i = 0; $i < $c; $i++) {
			$name = 'key' . $i;
			if ($force_where) {
				$this->add_where($name, '=', $keys[$i]);
			}
			else {
				$val = $keys[$i];
				if (!empty($val)) {
					$this->$name = $val;
				}
				else {
					$this->add_where($name, DBWhere::OP_IS_NULL);
				}
			}
		}
	}
	
	public function get_data() {
		return $this->data;
	}
	
	public function get_content_plain() {
		$ret = $this->content_gzip;
		if ($ret && function_exists('gzuncompress')) {
			$ret = gzuncompress($ret);
		}
		return $ret;
	}
	
	public function get_content_compressed() {
		return $this->content_gzip;
	}

	public function set_content_plain($content) {
		if (function_exists('gzcompress')) {
			$content = gzcompress($content, 9);
		}
		$this->content_gzip = $content;		
	}
	
	public function set_content_compressed($content) {
		$this->content_gzip = $content;
	}
	
	// now define your table structure.
	// key is column name, value is type
	protected function create_table_object() {
	    return new DBTable(
	    	'cache',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('key0', 255), 
				new DBFieldText('key1', 255),
				new DBFieldText('key2', 255),
				new DBFieldBlob('content_gzip', DBFieldText::BLOB_LENGTH_LARGE),
				new DBFieldDateTime('creationdate', null, DBFieldDateTime::TIMESTAMP),
				new DBFieldDateTime('expirationdate', null, DBFieldDateTime::NOT_NULL),
				new DBFieldSerialized('data', DBFieldText::BLOB_LENGTH_SMALL)
			),
			'id'			
	    );
	}
}
