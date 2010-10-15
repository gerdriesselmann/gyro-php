<?php
require_once dirname(__FILE__) . '/irenderer.cls.php';
/**
 * Interface for Blcoks 
 * 
 * If you are familiar with Drupal (http://drupal.org), the concept of blocks is 
 * not new to you. A block represents a snipppet of HTML that is grouped along 
 * with other blocks and placed in a location (left, right, too..) on a page.
 * 
 * Most common use is to build dynamic site bars or footers.
 *  
 * @author Gerd Riesselmann
 * @ingroup Interface
 */
interface IBlock extends IRenderer {
	const LEFT = 'LEFT';
	const RIGHT = 'RIGHT';
	const TOP = 'TOP';
	const BOTTOM = 'BOTTOM';
	const CONTENT = 'CONTENT';
	
	/**
	 * Get title of block (heading)
	 *
	 * @return string
	 */
	public function get_title();
	/**
	 * Set title of block (heading)
	 *
	 * @param string
	 */
	public function set_title($title);
	
	/**
	 * Get content of block (HTML)
	 *
	 * @return string
	 */
	public function get_content();
	/**
	 * Set content of block (HTML)
	 *
	 * @param string
	 */
	public function set_content($content);
	
	/**
	 * An index to sort blocks
	 *
	 * @return integer
	 */
	public function get_index();
	/**
	 * Set index to sort blocks
	 *
	 * @param integer
	 */
	public function set_index($index);
	
	/**
	 * Name (used as CSS class)
	 *
	 * @return string
	 */
	public function get_name();
	/**
	 * Sets Name (used as CSS class)
	 *
	 * @param string
	 */
	public function set_name($name);
	
	/**
	 * One of LEFT, RIGHT, CONTENT etc...
	 *
	 * @return string
	 */
	public function get_position();
	/**
	 * Set position
	 *
	 * @param string
	 */
	public function set_position($position);
	
	/**
	 * Returns true if this block is valid
	 * 
	 * @return boolean True if this block has content
	 */
	public function is_valid();
	
	/**
	 * Compare this block to another
	 * 
	 * @return int 0 if index of this is equal to other's index, -1 if this index is less than and +1 if it is more than other's index 
	 */
	public function compare($other);
}

/**
 * Callback function for sorting blocks. Invokes $item_1->compare($item_2);  
 */
function gyro_block_sort(&$item_1, &$item_2) {
	return $item_1->compare($item_2);
}
	