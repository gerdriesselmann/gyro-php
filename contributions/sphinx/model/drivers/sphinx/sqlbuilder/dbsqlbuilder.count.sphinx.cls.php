<?php
require_once dirname(__FILE__) . '/dbsqlbuilder.select.sphinx.cls.php';

/**
 * Count Query Builder for Sphinx
 * 
 * @author Gerd Riesselmann
 * @ingroup Sphinx
 */
class DBSqlBuilderCountSphinx extends DBSqlBuilderSelectSphinx {
	/**
	 * Return limit as string
	 */
	protected function get_limit($arr_limit) {
		return '0;1';
	}
	
	protected function get_features($query) {
		$ret = parent::get_features($query);
		$ret['count'] = true;
		return $ret;
	}	
}