<?php
/**
 * Delete queries for MySQL
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSqlBuilderDeleteMysql extends DBSqlBuilderDelete {
	/**
	 * Return SQL fragment
	 *
	 * @return string
	 */
	public function get_sql() {
		$template = $this->get_sql_template();
		$vars = $this->get_substitutes();
		if (empty($vars['%where']) && empty($vars['%order_by']) && empty($vars['%limit'])) {
			$template = 'TRUNCATE %!table';
		}
		return $this->substitute_template($template, $vars);
	}
}
