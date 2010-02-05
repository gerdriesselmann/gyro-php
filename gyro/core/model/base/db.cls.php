<?php
require_once dirname(__FILE__) . '/dataobjectbase.cls.php';
 
/**
 * Factory class for DAO classes
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */ 
class DB {
	const DEFAULT_CONNNECTION = 'default';
	
	/**
	 * A single dataobject to perform queries, escape values, etc
	 *
	 * @var array Array of IDBDriver
	 */
	private static $db;
	
	private static $connections = array();
	public static $query_log = array();
	public static $queries_total_time = 0;
	public static $db_connect_time= 0;

	/**
	 * Returns connection with given name
	 * 
	 * @param string|IDBDriver $name
	 * @return IDBDriver
	 */
	public static function get_connection($name_or_object = self::DEFAULT_CONNECTION) {
		if ($name_or_object instanceof IDBDriver) {
			return $name_or_object;
		}
		else if (isset(self::$connections[$name_or_object])) {
			return self::$connections[$name_or_object];
		}
		throw new Exception("Connection $name_or_object not found");
	}
	
	/**
	 * Create a connction with given name and paramteres
	 * 
	 * @param string $connection_name
	 * @param string $driver
	 * @param string $db_name
	 * @param string $db_user
	 * @param string $db_pwd
	 * @param string $db_host
	 * @return IDBDriver
	 */
	public static function create_connection($connection_name, $driver, $db_name, $db_user, $db_pwd, $db_host) {
		//define database configuration values
		Load::directories('model/drivers/' . $driver);
		//Load::directories('model/drivers/' . $driver . '/sqlbuilder');
		$drivername = 'DBDriver' . ucfirst($driver); // Driver must be ASCII
		$db = new $drivername();
		$db->initialize($db_name, $db_user, $db_pwd, $db_host);
		self::$connections[$connection_name] = $db;

		return $db;
	}
	
	/**
	 * Creates DAO class for given table 
	 * 
	 * @return IDataObject
	 */
	public static function create($model) {
		Load::models($model); // throws on error
		$classname = 'DAO' . ucfirst($model); // Model must be ASCII
		if (class_exists($classname)) {
			return new $classname();
		}
		
		throw new Exception(tr('Model %s not found', 'core', array('%s' => $model)));
	}
	
	/**
	 * Returns (and caches) instance for given table, with haven given value on given colum
	 * 
	 * @return mixed Object or false 
	 */
	public static function get_item($table, $key, $value) {
		$ret = false;
		if (!empty($value)) {
			$ret = self::get_item_multi($table, array($key => $value));
		}
		return $ret;			
	}
	
	/**
	 * Returns (and caches) instance for given table, with haven given values
	 * 
	 * @param string $table
	 * @param array $values Associative array with column => value
	 * @return IDataObject  False if not found
	 */
	public static function get_item_multi($table, $arr_values) {
		ksort($arr_values);
		$keys = array('db', $table, strtr(http_build_query($arr_values), '[]', '__'));
		$ret = RuntimeCache::get($keys, null);
		if (is_null($ret)) {
			$ret = false;
			$dao = self::create($table);
			foreach($arr_values as $col => $value) {
				$dao->$col = $value;
			}
			if ($dao->find(IDataObject::AUTOFETCH)) {
				$ret = $dao;
			}
			RuntimeCache::set($keys, $ret);
		}
		return $ret;
	}
	
	/**
	 * Initialize whole DB System
	 */
	public static function initialize() {
		//define database configuration values
		self::$db = self::create_connection(self::DEFAULT_CONNNECTION, APP_DB_TYPE, APP_DB_NAME, APP_DB_USER, APP_DB_PWD, APP_DB_HOST);
	}

	/**
	 * Escape given value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public static function escape($value, $connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		return $conn->escape($value);
	}

	/**
	 * Escape given database object, like table, field etc
	 *
	 * @param string $obj
	 */
	public static function escape_database_entity($obj, $connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		return $conn->escape_database_entity($obj);
	}
		
 	/**
 	 * Quote and escape a string
 	 * 
 	 * @param string $val 
 	 * @return string
 	 */
 	public static function quote($val, $connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		return $conn->quote($val); 
 	}

 	/**
 	 * Formats value. If no table is given or column does not exists, it will return DB::quote($value)
 	 *
 	 * @param mixed $value Value to format
 	 * @param IDBTable $table The source table
 	 * @param string $column The column on table
 	 */
 	public static function format($value, $table = null, $column = '') {
 		$field = self::find_field($table, $column);
 		return $field->format($value);
 	}
 	
	/**
 	 * Formats value for WHERE clause. If no table is given or column does not exists, it will return DB::quote($value)
 	 *
 	 * @param mixed $value Value to format
 	 * @param IDBTable $table The source table
 	 * @param string $column The column on table
 	 */
 	public static function format_where($value, $table = null, $column = '') {
 		$field = self::find_field($table, $column);
 		return $field->format_where($value);
 	}	
 	
 	/**
	 * Try to identify field and return ainstance of IDBField
 	 */
 	private static function find_field($table, $column) {
 		$field = null;
 		if ($table) {
 			$field = $table->get_table_field($column);
 		}
 		if (empty($field)) {
 			// Maybe it forces a table?
 			$table_and_field = explode('.', $column);
 			if (count($table_and_field) == 2) {
 				$forced_table = self::create($table_and_field[0]);
 				if ($forced_table) {
 					$field = $forced_table->get_table_field($table_and_field[1]);
 				}
 			}
 		}
 		if (empty($field)) {
 			$field = new DBField($column);
 			if ($table) {
 				$field->set_connection($table->get_table_driver());
 			}
 		} 		
 		return $field;
 	}
 	
 	/**
 	 * Execute a SELECT query
 	 *
 	 * @param string $query
 	 * @return IDBResultSet
 	 */
 	public static function query($query, $connection = self::DEFAULT_CONNNECTION) {
 		$timer = new Timer();
 		$conn = self::get_connection($connection);
 		$ret = $conn->query($query);
 		self::log_query($query, $timer->seconds_elapsed(), $ret->get_status());
 		return $ret;
 	}
 	
	/**
	 * Execute an query. Do not use with SELECT!
	 * 
	 * @param string $query
	 * @return Status
	 */
	public static function execute($query, $connection = self::DEFAULT_CONNNECTION) {
		$timer = new Timer();
		$conn = self::get_connection($connection);
		$ret = $conn->execute($query);
		self::log_query($query, $timer->seconds_elapsed(), $ret);
		return $ret;
	}
	
	/**
	 * Get last insert ID
	 */
	public static function last_insert_id($connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		return $conn->last_insert_id();
	}
	
	/**
	 * Execute all statements within an sql file
	 *
	 * @param string $file
	 * @return Status
	 */
	public static function execute_script($file, $connection = self::DEFAULT_CONNNECTION) {
		$status = new Status();
		$conn = self::get_connection($connection);
		if (file_exists($file)) {
			$handle = fopen($file, 'r');
			$dao = self::create('cache');
			while($query = self::extract_next_sql_statement($handle)) {
				if ($query != ';') {
					$status->merge($conn->execute($query));
				}
			}
			fclose($handle);
		}
		else {
			$status->append(tr('File %file not found', 'core', array('%file' => $file)));
		}
		return $status;
	}
	
	public static function extract_next_sql_statement($handle) {
		$ret = '';
		$last = '';
		$continue = true;
		while ($continue) {
			$char = self::read_next($handle, $ret);
			if ($char === false) {
				break;
			}

			switch($char) {
				case ';':
					$continue = false;
					break;
				case "'":
				case '"':
					$ret .= self::extract_until($handle, $char);
					break;
				case '#':
					// Command till end of line
					$ret = substr($ret, 0, -1);
					self::extract_until($handle, "\n");
					break;
				case '-':
					// handle -- comments (end at end of line)
					if ($last == '-') {
						$ret = substr($ret, 0, -2);
						self::extract_until($handle, "\n");
					}
					break;
				case '*':
					// Handle /* .. */ comments
					if ($last == '/') {
						$ret = substr($ret, 0, -2);
						self::extract_until($handle, "*/");
					} 
					break;
			}
			$last = $char;
		}
		
		$ret = str_replace("\n", ' ', $ret);
		$ret = str_replace("\r", ' ', $ret);
		$ret = trim($ret);
		return $ret;
	}

	private static function extract_until($handle, $chars) {
		$ret = '';
		$s = strlen($chars);
		$char_compare = ($s == 1) ? $chars : substr($chars, -1);  
		$last_compare = ($s == 1) ? false : substr($chars, 0, 1);
		$last = '';
		
		do {
			$c = self::read_next($handle, $ret);
			if ($c === $char_compare) {
				if ($last_compare === false) {
					break;
				}
				else {
					if ($last === $last_compare) {
						break;
					}
				}
			}
			$last = $c;
		} while ($c !== false);
		
		return $ret;
	}
	
	private static function read_next($handle, &$result) {
		$c = fgetc($handle);
		if ($c !== false) {
			$result .= $c;
			if ($c == "\\") {
				// Escape character. Read next character
				$c .= self::read_next($handle, $result);
			}			
		}
		return $c;
	}
	
	public static function start_trans($connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		$conn->trans_start();
	}
	
	public static function end_trans($status, $connection = self::DEFAULT_CONNNECTION) {
		if ($status->is_ok()) {
			self::commit($connection); 
		}
		else {
			self::rollback($connection);
		}
	}

	public static function commit($connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		$conn->trans_commit();
	}
	
	public static function rollback($connection = self::DEFAULT_CONNNECTION) {
		$conn = self::get_connection($connection);
		$conn->trans_rollback();
	}
	
	/**
	 * Log a query
	 *
	 * @param string $query
	 * @param float $seconds
	 * @param Status $status
	 */
	public static function log_query($query, $seconds, $status) {
		if ($status->is_error()) {
			if (Config::has_feature(Config::LOG_FAILED_QUERIES)) {
				$log = array(
					'query' => $query,
					'message' => $status->message
				);
				Load::components('logger');
				Logger::log('failed_queries', $log);
			}
			if (Config::has_feature(Config::THROW_ON_DB_ERROR)) {
				$text = $status->message . " [$query]";
				throw new Exception($text);
			}
		}
		
		if (Config::has_feature(Config::LOG_QUERIES)) {
			$log = array(
				'query' => $query,
				'seconds' => $seconds,
				'success' => $status->is_ok(),
				'message' => $status->message
			);
			self::$query_log[] = $log;
			self::$queries_total_time += $seconds;
			Load::components('logger');
			Logger::log('queries', $log); 
		}
		
		if (Config::has_feature(Config::LOG_SLOW_QUERIES)) {
			if ($seconds > Config::get_value(Config::DB_SLOW_QUERY_THRESHOLD, false, 0.0100)) {
				$log = array(
					'query' => $query,
					'seconds' => $seconds,
				);
				Load::components('logger');
				Logger::log('slow_queries', $log); 
			}
		}		
	}
}