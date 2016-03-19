<?php
/**
 * A MySql fulltext where implementation
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBWhereFulltext extends DBWhere {
	/**
	 * The match construct for resuse e.g. in ordering
	 *
	 * @var string
	 */
	protected $match;
	
	/**
	 * Constructor
	 * 
	 * @param IDBTable $table Table that contains column
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param mixed $value Value(s) to use
	 * @mode string Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 */
	public function __construct(IDBTable $table, $column, $value, $threshold = 0, $mode = IDBWhere::LOGIC_AND) {
		if (!is_array($value)) {
			$value = GyroString::explode_terms($value);
		}
		parent::__construct(
			$table, 
			$this->build_fulltext_sql($table, $column, $value, $threshold, $mode),
			null,
			null,
			$mode
		);
		$this->match = $this->build_fulltext_match($table, $column, $value, $mode);
	}
	
	/**
	 * Returns matchc clause that can be used for relevance ordering
	 *
	 * @return string
	 */
	public function get_match() {
		return $this->match;
	}
	
	/**
	 * Build the sql fragement
	 *
	 * @param IDBTable $table Table that contains column
	 * @param string $column Column to query upon, or a full sql where statement
	 * @param array $value Value(s) to use
	 * @param float $threshold Minimum relevance (ignored if 0)
	 * @param string $mode Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
	 * @return string
	 */
	protected function build_fulltext_sql(IDBTable $table, $column, $value, $threshold, $mode) {
		$threshold = Cast::float($threshold);
		$addition = 'IN BOOLEAN MODE';
		$ret = '';
		if ($threshold > 0) {
			$addition = '';
			$ret = ' > ' . $threshold;
		}
		$ret = $this->build_fulltext_match($table, $column, $value, $mode, $addition) . $ret;
		return $ret;
	}

 	/**
 	 * Builds a full text match statement
 	 * 
	 * @param IDBTable $table Table that contains column
	 * @param string $column Column to query upon, or a full sql where statement
 	 * @param array $value Array of Tokens
 	 * @param enum $mode Either IDBWhere::LOGIC_AND or IDBWhere::LOGIC_OR
 	 * @param string $additions  Additional commads like "IN BOOLEAN MODE"
 	 * @return string
 	 */
	public function build_fulltext_match(IDBTable $table, $column, $value, $mode, $additions = '') {
		$where = "";
		$fieldName = $this->prefix_table_name($column, $table);
		foreach($value as $token) {
			if (substr($token, 0, 1) !== "-" && $mode == self::LOGIC_AND)
				$where .= "+";
				
			if (substr($token, -1) != "\"")
				$token .= "*";
				
			$where .= $token . " ";
		}
		
		return "(MATCH (" . $fieldName . ") AGAINST ('" . $table->get_table_driver()->escape($where) . "' " . $table->get_table_driver()->escape($additions) . "))";		
	}	
	
}
