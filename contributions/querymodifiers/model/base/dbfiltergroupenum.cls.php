<?php
/**
 * Convenience class for creating a filter on an enum value
 * 
 * @ingroup QueryModifiers
 * @author Gerd Riesselmann
 */
class DBFilterGroupEnum extends DBFilterGroup {
	/**
	 * Constructor
	 * 
	 * @param string $fieldname Name of field
	 * @param string $name Name of filter as shown to the user
	 * @param array $enums Associative array of enum values with DBValue as key, and dispaly value as value
	 * @param string $default Optional default value 
	 */
	public function __construct($fieldname, $name, $enums, $default = '') {
		$columns = array();
		foreach($enums as $ekey => $evalue) {
			$columns[String::plain_ascii($ekey)] = new DBFilterColumn(
				$fieldname,
				$ekey,
				$evalue
			);
		}
		parent::__construct($fieldname, $name, $columns, String::plain_ascii($default));
	}
}
