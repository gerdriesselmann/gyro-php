<?php
/**
 * A simple data structure representing a rectangle
 *
 * @ingroup ImageTools
 */
class ImageToolsRect {
	/**
	 * Position Left
	 *  
	 * @var int
	 */
	public $x;
	/**
	 * Position top
	 *  
	 * @var int
	 */
	public $y;
	/**
	 * Width
	 *  
	 * @var int
	 */
	public $width;
	/**
	 * Height
	 *  
	 * @var int
	 */
	public $height;
	
	/**
	 * Constructor
	 * 
	 * @param int $width
	 * @param int $height
	 * @param int $x
	 * @param int $y
	 */
	public function __construct($width, $height, $x = 0, $y = 0) {
		$this->x = $x;
		$this->y = $y;
		$this->width = $width;
		$this->height = $height; 
	}
}