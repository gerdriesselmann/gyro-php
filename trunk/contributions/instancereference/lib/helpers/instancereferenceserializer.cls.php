<?php
/**
 * Helper to turn an isntance into an array or string and vice versa
 */
class InstanceReferenceSerializier {
	/**
	 * Convert an instance to an array (containing type and keys)
	 *
	 * @param IDataObject $inst
	 * @return array
	 */
	public static function instance_to_array(IDataObject $inst) {
		$ret = array($inst->get_table_name());
		foreach($inst->get_table_keys() as $key => $field) {
			/* @var $field DBField */
			$ret[] = $inst->$key;
		}
		return $ret;		
	}
	
	/**
	 * Convert an an array (containing type and keys) to an instance
	 *
	 * @return IDataObject
	 * @param array $arr_inst
	 */
	public static function array_to_instance($arr_inst) {
		$table = array_shift($arr_inst);
		if (empty($table)) {
			return false;
		}
		$dao = DB::create($table);
		$params = array();
		if ($dao) {
			foreach($dao->get_table_keys() as $key => $field) {
				/* @var $field DBField */
				$params[$key] = array_shift($arr_inst);
			}
		}
		return DB::get_item_multi($table, $params);
	}

	/**
	 * Convert an instance to a string (containing type and keys, concated by '/')
	 *
	 * @param IDataObject $inst
	 * @return string
	 */
	public static function instance_to_string(IDataObject $inst) {
		$ret = '';
		$arr = self::instance_to_array($inst);
		if (is_array($arr)) {
			$ret = implode('/', $arr);
		}
		return $ret;		
	}
	
	/**
	 * Convert a string (containing type and keys, concated by '/') to an instance 
	 *
	 * @param string $s
	 * @return IDataObject $inst
	 */
	public static function string_to_instance($s) {
		$arr = explode('/', $s);
		return self::array_to_instance($arr);
	}
}
