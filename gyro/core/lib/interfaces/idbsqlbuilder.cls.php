<?php
/**
 * Something that creates an SQL string
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBSqlBuilder {
	/**
	 * Return SQL fragment
	 * 
	 * @return string
	 */
	public function get_sql();
}
