<?php
/**
 * Represents a block
 * 
 * if you are familiar with Drupal (http://drupal.org), the concept of blocks is 
 * not new to you. A block represents a snipppet of HTML that is grouped along 
 * with other blocks and placed in a location (left, right, too..) on a page.
 * 
 * Most common use is to build dynamic site bars or footers.
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class BlockBase  {
	const LEFT = 'LEFT';
	const RIGHT = 'RIGHT';
	const TOP = 'TOP';
	const BOTTOM = 'BOTTOM';
	const CONTENT = 'CONTENT';
	
	/**
	 * Title of block (heading)
	 *
	 * @var string
	 */
	public $title;
	/**
	 * Content of block (HTML)
	 *
	 * @var string
	 */
	public $content;
	/**
	 * An index to sort blocks
	 *
	 * @var integer
	 */
	public $index;
	/**
	 * Name (used as CSS class)
	 *
	 * @var string
	 */
	public $name;
	/**
	 * One of LEFT, RIGHT, or CONTENT
	 *
	 * @var string
	 */
	public $position;
	
	/**
	 * Constructor
	 * 
	 * @param string The name of this block. Used as class, too
	 * @param string The title of the block. Displayed as heading, e.g.
	 * @param string The block's content
	 * @param integer The block's index. A block with lowest index will be displayed first
	 * @param enum Where the block is to be dispalyed. 
	 */
	public function __construct($name, $title, $content, $index = 1000, $position = self::LEFT) {
		$this->title = trim($title);
		$this->content = trim($content);
		$this->index = $index;
		$this->name = $name;
		$this->position = $position;
	}
	
	/**
	 * Returns true if this block is valid
	 * 
	 * @return boolean True if this block has content
	 */
	public function is_valid() {
		return (!empty($this->content));
	}
	
	/**
	 * Compare this block to another
	 * 
	 * @return int 0 if index of this is equal to other's index, -1 if this index is less than and +1 if it is more than other's index 
	 */
	public function compare($other) {
		if ($this->index == $other->index) {
			return 0;
		}
	
		return ($this->index < $other->index) ? -1 : 1;
	}
}

/**
 * Callback function for sorting blocks. Invokes $item_1->compare($item_2);  
 */
function gyro_block_sort(&$item_1, &$item_2) {
	return $item_1->compare($item_2);
}
