<?php
/**
 * Interface for image informations
 * 
 * Contains information like size, mime-type, extension etc.
 * 
 * @author Gerd Riesselmann
 * @ingroup ImageTools
 */
interface IImageInformation {
	/**
	 * Returns height in pixel
	 * @return int
	 */
	public function get_height();
	/**
	 * Returns width in pixel
	 * @return int
	 */
	public function get_width();
	/**
	 * Returns raw image data
	 * @return string
	 */
	public function get_binary_data();
	
	/**
	 * Saves to given file
	 * @return bool
	 */
	public function save_to_file($file, $add_extension = true);
	
	/**
	 * Returns image mime type
	 * @return string
	 */	
	public function get_mime_type();
	/**
	 * Returns image file extension
	 * @return int
	 */
	public function get_extension();
}