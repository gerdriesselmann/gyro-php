<?php
/**
 * Creates builder for given query type and driver
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderFactory {
	const SELECT = 1;
	const INSERT = 2;
	const UPDATE = 3;
	const DELETE = 4;
	const COUNT  = 5;
	const WHERE  = 6;
	const WHEREGROUP = 7;
	const REPLACE = 8;
	
	private static $builders = array();
	
	/**
	 * Create appropiate SQL builder
	 *
	 * @param int $type Type of builder to create. Any of the constants DBSqlBuilderFactory::SELECT, DBSqlBuilderFactory::UPDATE etc. 
	 * @param IDBQuery|IDBWhere $query The query to build SQL for
	 * @param array $params Associative array dependend on builder type 
	 * @return IDBSqlBuilder
	 */
	public static function create_builder($type, $query, $params = null) {
		$driver = DB::get_connection($query->get_table()->get_table_driver());
		$db = $driver->get_driver_name();
		$key = $db . '%%' . $type;
		if (!isset(self::$builders[$key])) {
			$part = false;
			switch ($type) {
				case self::SELECT:
					$part ='Select';
					break;
				case self::INSERT:
					$part = 'Insert';
					break;
				case self::UPDATE:
					$part = 'Update';
					break;
				case self::DELETE:
					$part = 'Delete';
					break;
				case self::COUNT:
					$part = 'Count';
					break;
				case self::WHERE:
					$part = 'Where';
					break;
				case self::WHEREGROUP:
					$part = 'WhereGroup';
					break;
				case self::REPLACE:
					$part = 'Replace';
					break;
			}
			if ($part === false) {
				throw new Exception(tr('Unknown SQL Builder Type: %s', 'core', array('%s' => $type)));
			}
		
			$lower_part = strtolower($part);
			$file = "model/drivers/$db/sqlbuilder/dbsqlbuilder.$lower_part.$db.cls.php";
			$base_file = "model/base/sqlbuilder/dbsqlbuilder.$lower_part.cls.php";
			$cls = 'DBSqlBuilder' . $part . ucfirst($db); // $db is ASCII
			Load::first_file($base_file, $file);
			self::$builders[$key] = $cls;
		}
		$cls = self::$builders[$key];
		return new $cls($query, $params);
	}
}
