<?php
/**
 * Build a replace query
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderReplace extends DBSqlBuilderBase {
	/**
	 * Return SQL fragment
	 *
	 * @return string
	 */
	public function get_sql() {
		throw new Exception('Replace is not supported for this kind of database');
	}
}
