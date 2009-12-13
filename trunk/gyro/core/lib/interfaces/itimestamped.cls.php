<?php
/**
 * Interface for timestamped items
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ITimeStamped {
	/**
	 * Return creation date and time
	 *
	 * @return timestamp
	 */
	public function get_creation_date();

	/**
	 * Return modification date and time
	 *
	 * @return timestamp
	 */
	public function get_modification_date();
}
