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
		$cls = false;
		switch ($type) {
			case self::SELECT:
				$cls ='DBSqlBuilderSelect';
				break;
			case self::INSERT:
				$cls = 'DBSqlBuilderInsert';
				break;
			case self::UPDATE:
				$cls = 'DBSqlBuilderUpdate';
				break;
			case self::DELETE:
				$cls = 'DBSqlBuilderDelete';
				break;
			case self::COUNT:
				$cls = 'DBSqlBuilderCount';
				break;
			case self::WHERE:
				$cls = 'DBSqlBuilderWhere';
				break;
			case self::WHEREGROUP:
				$cls = 'DBSqlBuilderWhereGroup';
				break;
			case self::REPLACE:
				$cls = 'DbSqlBuilderReplace';
				break;
		}
		if ($cls === false) {
			throw new Exception(tr('Unknown SQL Builder Type: %s', 'core', array('%s' => $type)));
		}
		
		$cls .= String::to_upper($db, 1);
		return new $cls($query, $params);
	}
}
