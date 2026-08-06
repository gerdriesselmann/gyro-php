<?php
/**
 * Interface for stepping through DB result set
 *
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBResultSet {
	/**
	 * Closes internal cursor
	 */
	public function close(): void;

	/**
	 * Returns number of columns in result set
	 */
	public function get_column_count(): int;

	/**
	 * Returns number of rows in result set
	 */
	public function get_row_count(): int;

	/**
	 * Returns row as associative array
	 *
	 * @return array|false False if no more data is available
	 */
	public function fetch(): array|false;

	/**
	 * Returns status
	 */
	public function get_status(): Status;
}
