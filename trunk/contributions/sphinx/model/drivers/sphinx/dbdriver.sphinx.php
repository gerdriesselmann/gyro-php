<?php
/**
 * DB Driver for Sphinx full text index
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBDriverSphinx implements IDBDriver {
	const DEFAULT_CONNECTION_NAME = 'sphinx';
	const SPHINX_ATTRIBUTE = 16384;
	
	/**
	 * Set weights like so:
	 * 
	 * $query->sphinx_features[DBDriverSphinx::FEATURE_WEIGHTS] = array('column' => weight);
	 */
	const FEATURE_WEIGHTS = 'weights';
	/**
	 * Set to strip operators:
	 * 
	 * $query->sphinx_features[DBDriverSphinx::FEATURE_STRIP_OPERATORS] = true;
	 */
	const FEATURE_STRIP_OPERATORS = 'strip_operators';
	/**
	 * Set matching mode
	 * 
	 * $query->sphinx_features[DBDriverSphinx::FEATURE_MATCH_MODE] = DBDriverSphinx::MATCH_OR;
	 */
	const FEATURE_MATCH_MODE = 'match_mode';
	/**
	 * Query more than one index
	 * 
	 * $query->sphinx_features[DBDriverSphinx::FEATURE_SUBINDEXES] = array('main', 'delta');
	 * $query->sphinx_features[DBDriverSphinx::FEATURE_SUBINDEXES] = array('main' => 1, 'delta' => 2);
	 * 
	 * If set, the given indexes are search. Note that naming rules still apply, that is the index
	 * name is build using APP_SPHINX_DB_NAME + table name + sub index name.
	 * 
	 * E.g. with a DB Name of "app_", a table named "seachindex" and two subindexes "main" and "delta", the 
	 * indexes queried must be named "app_searchindexmain" and "app_searchindexdelta".
	 * 
	 * If an associative array is passed, the key is interpreted as subindex name and the value as weight. The 
	 * data gets passed to Sphinx SetIndexWeights function 
	 */
	const FEATURE_SUBINDEXES = 'subindexes';
	
	/**
	 * BOOLEAN match mode
	 */
	const MATCH_BOOL = 'bool';
	/**
	 * Extended mode, this is the default
	 */
	const MATCH_EX = 'ex';
	/**
	 * OR all terms
	 */
	const MATCH_OR = 'or';
	/**
	 * AND all terms
	 */
	const MATCH_AND = 'and';
	/**
	 * Match as phrase
	 */
	const MATCH_PHRASE = 'phrase';
	
	private $client = false;
	private $connect_params;
	
	/**
	 * Return name of driver, e.g "mysql". Lowercase!
	 * @return string
	 */
	public function get_driver_name() {
		return 'sphinx';
	}

	/**
	 * Returns host name of database
	 * 
	 * @return string
	 */
	public function get_host() {
		$tmp = array(
			Arr::get_item($this->connect_params, 'host', ''),
			Arr::get_item($this->connect_params, 'port', '')
		);
		return trim(implode($tmp), ':');
	}
	
	/**
	 * Returns name of DB
	 * 
	 * @return string
	 */
	public function get_db_name() {
		return Arr::get_item($this->connect_params, 'dbname', '');
	}
		
	/**
	 * Connect to DB
	 *
	 * @param string $dbname Ignored
	 * @param string $user Ignored
	 * @param string $password Ignored
	 * @param string $host Host
	 */
	public function initialize($dbname, $user = '', $password = '', $host = 'localhost', $params = false) {
		$host_and_port = explode(':', $host);
		if (count($host_and_port) < 2) {
			$host_and_port[] = 9312; // Sphinx default port
		}
		$this->connect_params = array(
			'host' => $host_and_port[0],
			'port' => Cast::int($host_and_port[1]),
			'dbname' => $dbname 
		);
	}
	
	/**
	 * Connect if not already connceted
	 * 
	 * @return void
	 */
	private function connect() {
		if ($this->client === false) {
			$this->client = new SphinxClient();
			$this->client->SetServer($this->connect_params['host'], $this->connect_params['port']); 
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
		return $obj;
	}
	
	/**
	 * Escape given value
	 *
	 * @param mixed $value
	 * @return string
	 */
	public function escape($value) {
		// Handcraft escaping for extended query syntax
		// TODO Maybe we should even leave it untouched within double quotes?
		$from = array ( '\\', '@', '&', '/');
		$to   = array ( '\\\\', '\@', '\&', '\/');
		$ret = str_replace($from, $to, $value);
		
		// This code escapes extended search operators like !, -, | etc.
		// That's definitely too much! 
		//$this->connect();
		//$ret = $this->client->EscapeString(Cast::string($value));
		
		return $ret; 
	}
	
	/**
	 * Return current status
	 * 
	 * @return Status
	 */
	public function get_status() {
		$this->connect();
		$ret = new Status();
		$err = $this->client->GetLastError();
		if ($err) {
			$ret->append($err);
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
	}
	
	/**
	 * Execute a Select statement
	 *
	 * @param string $sql
	 * @return IDBResultSet
	 */
	public function query($query) {
		$this->connect();
		
		$arr_query = unserialize($query);
		$features = Arr::get_item($arr_query, 'features', false);
		
		$terms = Arr::get_item_recursive($arr_query, 'conditions[query]', '');
		if (Arr::get_item($features, self::FEATURE_STRIP_OPERATORS, false)) {
			$terms = self::strip_operators($terms);
		}
		
		// Set options
		$this->client->SetSelect($arr_query['fields']);
		$this->client->SetArrayResult(true);
		if ($arr_query['order']) {
			$this->client->SetSortMode(SPH_SORT_EXTENDED, $arr_query['order']);
		}
		$limit = $arr_query['limit'];
		if ($limit) {
			$arr_limit = explode(';', $limit);
			$arr_limit = array_map('intval', $arr_limit);
			$this->client->SetLimits($arr_limit[0], $arr_limit[1]);
		}

		// Filter
		foreach(Arr::get_item_recursive($arr_query, 'conditions[filter]', array()) as $filter) {
			$this->client->SetFilter(
				$filter['attribute'],
				$filter['values'],
				$filter['exclude']
			);
		}
		
		// Field Weights
		$this->client->SetFieldWeights(Arr::get_item($features, self::FEATURE_WEIGHTS, array()));
		
		// query
		$matchmode = '';
		switch(Arr::get_item($features, self::FEATURE_MATCH_MODE, self::MATCH_EX)) {
			case self::MATCH_BOOL:
				$matchmode = SPH_MATCH_BOOLEAN;
				break;
			case self::MATCH_OR:
				$matchmode = SPH_MATCH_ANY;
				break;
			case self::MATCH_AND:
				$matchmode = SPH_MATCH_ALL;
				break;
			case self::MATCH_PHRASE:
				$matchmode = SPH_MATCH_PHRASE;
				break;
			case self::MATCH_EX:
			default:
				$matchmode = SPH_MATCH_EXTENDED2;
				break;				
		}
		$this->client->SetMatchMode($matchmode);
		
		// Fetch indexes
		$index_base_name = $this->connect_params['dbname'] . $arr_query['from'];
		$index_names = array();
		$index_weight = array();
		foreach(Arr::get_item($features, self::FEATURE_SUBINDEXES, array()) as $key => $value) {
			if (is_numeric($key)) {
				$subindex_name = $index_base_name . $value;
			}
			else {
				$subindex_name = $index_base_name . $key;
				$index_weight[$subindex_name] = $value;
			}
			$index_names[] = $subindex_name; 
		}
		$index_name = count($index_names) ? implode(',', $index_names) : $index_base_name;
		if (count($index_weight)) {
			$this->client->SetIndexWeights($index_weight);
		}
		$result = $this->client->Query($terms, $index_name);
		
		$ret = false;
		if (isset($features['count'])) {
			$ret = new DBResultSetCountSphinx($result, $this->get_status());
		}
		else {
			$ret = new DBResultSetSphinx($result, $this->get_status());
		}
		$this->client = false;
		return $ret;
	}
	
	/**
	 * Explain the given query
	 * 
	 * @param string $sql
	 * @return IDBResultSet False if quey cant be explain or driver does not support it
	 */
	public function explain($sql) {
		return false;
	}	
	
	/**
	 * Make this driver the default driver
	 * 
	 * @return Status
	 */
	public function make_default() {
		return new Status();
	}	
	
	/**
	 * Start transaction
	 */
	public function trans_start() {
	}

	/**
	 * Commit transaction
	 */
	public function trans_commit() {
	}

	/**
	 * Rollback transaction
	 */
	public function trans_rollback() {
	}

	/**
	 * Get last insert ID
	 */
	public function last_insert_id() {
		return 0;
	}

	/**
	 * Returns true, if a given feature is supported
	 * 
	 * @param string feature
	 * @return bool 
	 */
	public function has_feature($feature) {
		return false;
	}
	
	/**
	 * Strip operators form searrch term
	 */
	public static function strip_operators($terms) {
		$ops = '|-!@[]()*"<=^$~/';
		$replace = str_repeat(' ', strlen($ops));
		return strtr($terms, $ops, $replace);			
	} 
}
