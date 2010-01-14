<?php
/**
 * Translate an URL into Sorting
 * 
 * @todo Needs to be filled
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ISorterAdapter {
	/**
	 * Return column key of column to sort after
	 * 
	 * @return string
	 */
	public function get_column();

	/**
	 * Returns sort order ('forward' or 'backward')
	 * 
	 * False if no order was set 
	 * 
	 * @return string
	 */
	public function get_order();

	/**
	 * Return link to invoke sorting on given column with given order
	 *
	 * @param string $column
	 * @param string $order
	 * @return Url
	 */
	public function get_url_for_sort($column, $order);
}