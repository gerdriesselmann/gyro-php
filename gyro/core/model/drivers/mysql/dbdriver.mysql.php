<?php
/**
 * Driver for MySQL
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBDriverMysql implements IDBDriver {
	/**
	 * Primary connection
	 */
	const PRIMARY = 0;
	/**
	 * A connectionj other than default one
	 */
	const SECONDARY = 1;
	
	/**
	 * Connection type: PRIMARY, SECONDARY
	 * @var int
	 */
	protected $type;
	/**
	 * @var mysqli
	 */
	protected $conn = false;
	protected static $transaction_count = 0;
	protected $connect_params;

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
	 * @param array $params 
	 *    Associative array allowing the following keys:
	 *      - type: Connection type
	 */
	public function initialize($dbname, $user = '', $password = '', $host = 'localhost', $params = false) {
		$this->connect_params = array(
			'host' => $host,
			'user' => $user,
			'pwd' => $password,
			'db' => $dbname,			
		);
		$this->type = Arr::get_item($params, 'type', self::PRIMARY);
	}
	
	/**
	 * Connect if not already connceted
	 * 
	 * @return void
	 */
	protected function connect() {
		if ($this->conn === false) {
			$err = new Status();
			$host_arr = explode(':', $this->connect_params['host']);
			$this->conn = new mysqli(
				array_shift($host_arr),
				$this->connect_params['user'],
				$this->connect_params['pwd'],
				'',
				array_shift($host_arr)
			);
			if (mysqli_connect_errno()) {
				$err->merge($this->conn->connect_error);
			}
			if ($err->is_ok()) {
				$this->conn->set_charset(GyroLocale::get_charset());
				if ($this->type == self::PRIMARY) {
					$err->merge($this->make_default());
				}
			}
			if ($err->is_ok()) {
				// We are connected
				if (GyroLocale::get_charset() == 'UTF-8') {
					$this->execute("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
				}
				//$this->execute("SET sql_mode=STRICT_ALL_TABLES");
				$this->execute("SET sql_mode='TRADITIONAL'");
				//$this->execute("SET SESSION sql_mode='STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_AUTO_CREATE_USER,NO_ENGINE_SUBSTITUTION'");
			}
			else {
				$err->append(tr(
					'Could not connect to server %host', 
					'core', 
					array('%host' => $this->connect_params['host'])
				));
			}
			if ($err->is_error()) {
				throw new Exception($err->to_string(Status::OUTPUT_PLAIN));
			}
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
	public function escape_database_entity($obj, $type = self::FIELD) {
		$ret = '';
		if ($type === self::TABLE) {
			$ret .= '`' . $this->get_db_name() . '`.';
		}
		$ret .= '`' . $obj . '`';
		return $ret;
	}
	
	/**
	 * Escape given value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function escape($value) {
		$this->connect();
		return $this->conn->real_escape_string(Cast::string($value));
	}
	
	/**
	 * Return current status
	 * 
	 * @return Status
	 */
	public function get_status() {
		$this->connect();
		$ret = new Status();
		if ($this->conn->errno) {
			$ret->append($this->conn->error);
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
		$this->conn->real_query($sql);
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
		$result = $this->conn->query($sql, MYSQLI_STORE_RESULT);
		$status = $this->get_status();
		return new DBResultSetMysql($result, $status);
	}
	
	/**
	 * Explain the given query
	 * 
	 * @param string $sql
	 * @return IDBResultSet
	 */
	public function explain($sql) {
		$ret = false;
		if (strtolower(substr($sql, 0, 6)) === 'select') {
			$sql = 'EXPLAIN ' . $sql;
			$ret = $this->query($sql);
		}
		return $ret;
	}	
	
	/**
	 * Make this driver the default driver
	 * 
	 * @return Status
	 */
	public function make_default() {
		$ret = new Status();
		$this->connect();
		if (!$this->conn->select_db($this->connect_params['db'])) {
			$ret->append(tr(
				'Could not connect to database %db on server %host', 
				'core', 
				array('%db' => $this->connect_params['db'], '%host' => $this->connect_params['host'])
			));
		}	
		return $ret;	
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
				//$this->conn->autocommit(false);
				$this->conn->query('START TRANSACTION');
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
				$this->conn->commit();
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
				$this->conn->rollback();
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
		return $this->conn->insert_id;
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
