<?php
/**
 * Driver for MySQL
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBDriverMysql implements IDBDriver {
	private $db_handle = false;
	private static $transaction_count = 0;
	private $connect_params;

	/**
	 * Return name of driver, e.g "mysql". Lowercase!
	 * @return string
	 */
	public function get_driver_name() {
		return 'mysql';
	}

	/**
	 * Returns host name of database
	 * 
	 * @return string
	 */
	public function get_host() {
		return Arr::get_item($this->connect_params, 'host', '');
	}
	
	/**
	 * Returns name of DB
	 * 
	 * @return string
	 */
	public function get_db_name() {
		return Arr::get_item($this->connect_params, 'db', '');
	}
	
	/**
	 * Connect to DB
	 *
	 * @param string $dbname Name of DB 
	 * @param string $user Username
	 * @param string $password Password
	 * @param string $host Host
	 */
	public function initialize($dbname, $user = '', $password = '', $host = 'localhost') {
		$this->connect_params = array(
			'host' => $host,
			'user' => $user,
			'pwd' => $password,
			'db' => $dbname,
			
		);
	}
	
	/**
	 * Connect if not already connceted
	 * 
	 * @return void
	 */
	private function connect() {
		if ($this->db_handle === false) {
			$this->db_handle = mysql_connect(
				$this->connect_params['host'], 
				$this->connect_params['user'],
				$this->connect_params['pwd']
			);
			if ($this->db_handle) {
				if (mysql_select_db($this->connect_params['db'], $this->db_handle)) {
					// We are connected
					if (GyroLocale::get_charset() == 'UTF-8') {
 						$this->execute("SET NAMES 'utf8' COLLATE 'utf8_general_ci'" );
 					}
 					//$this->execute("SET sql_mode=STRICT_ALL_TABLES");
 					$this->connect_params = false;
					return;				
				}
			}
		
			throw new Exception(tr(
				'Could not connect to database %db on server %host', 
				'core', 
				array('%db' => $this->connect_params['db'], '%host' => $this->connect_params['host'])
			));
		}
	}
	
	/**
	 * Quote given value
	 *
	 * @param string $value
	 */
	public function quote($value) {
		return "'" . $this->escape($value) . "'";
	}

	/**
	 * Quote given database object, liek table, field etc
	 *
	 * @param string $obj
	 */
	public function escape_database_entity($obj) {
		return "`" . $obj . "`";
	}
	
	/**
	 * Escape given value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function escape($value) {
		$this->connect();
		return mysql_real_escape_string(Cast::string($value), $this->db_handle);
	}
	
	/**
	 * Return current status
	 * 
	 * @return Status
	 */
	public function get_status() {
		$this->connect();
		$ret = new Status();
		if (mysql_errno($this->db_handle)) {
			$ret->append(mysql_error($this->db_handle));
		}
		return $ret;
	}
	
	/**
	 * Execute an SQL command (Insert, Update...) 
	 *
	 * @param string $sql
	 * @return Status
	 */
	public function execute($sql) {
		$this->connect();
		mysql_query($sql, $this->db_handle);
		return $this->get_status();		
	}
	
	/**
	 * Execute a Select statement
	 *
	 * @param string $sql
	 * @return IDBResultSet
	 */
	public function query($sql) {
		$this->connect();
		$handle = mysql_query($sql, $this->db_handle);
		$status = $this->get_status();
		return new DBResultSetMysql($handle, $status);
	}
	
	/**
	 * Start transaction
	 */
	public function trans_start() {
		if (self::$transaction_count >= 0) {
			if (self::$transaction_count == 0) {
				// We support nested transaction, while MySQL doesn't
				// "Beginning a transaction causes any pending transaction to be committed.", http://dev.mysql.com/doc/refman/5.0/en/commit.html
				$this->connect();
				mysql_query('START TRANSACTION', $this->db_handle);	
			}
			self::$transaction_count++;
		}		
	}

	/**
	 * Commit transaction
	 */
	public function trans_commit() {
		if (self::$transaction_count > 0) {
			self::$transaction_count--;
			if (self::$transaction_count == 0) {
				mysql_query('COMMIT', $this->db_handle);
			}
		}
	}

	/**
	 * Rollback transaction
	 */
	public function trans_rollback() {
		if (self::$transaction_count > 0) {
			self::$transaction_count--;
			if (self::$transaction_count == 0) {
				// Rollback anything up to now
				mysql_query('ROLLBACK', $this->db_handle);
			}
		}
		// Start a transaction that won't get committed
		//mysql_query('START TRANSACTION', $this->db_handle);	
		//self::$transaction_count = -1; // No commits will be issued
	}

	/**
	 * Get last insert ID
	 */
	public function last_insert_id() {
		return mysql_insert_id($this->db_handle);
	}
	
	/**
	 * Returns true, if a given feature is supported
	 * 
	 * @param string feature
	 * @return bool 
	 */
	public function has_feature($feature) {
		switch ($feature) {
			case self::FEATURE_REPLACE:
				return true;
			default:
				return false;
		}
	}	
}
