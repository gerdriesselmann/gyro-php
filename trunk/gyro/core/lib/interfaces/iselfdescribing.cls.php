<?php
/**
 * Interface for classes that can descrioe themselves
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ISelfDescribing {
	/**
	 * Get title for this class
	 * 
	 * @return string
	 */
	public function get_title();

	/**
	 * Get description for this instance
	 *  
	 * @return string 
	 */
	public function get_description();
}
