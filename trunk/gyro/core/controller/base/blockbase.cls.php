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
class BlockBase implements IBlock  {
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
	 * Get title of block (heading)
	 *
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}
	
	/**
	 * Set title of block (heading)
	 *
	 * @param string
	 */
	public function set_title($title) {
		$this->title = $title;
	}
	
	/**
	 * Get content of block (HTML)
	 *
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}
	
	/**
	 * Set content of block (HTML)
	 *
	 * @param string
	 */
	public function set_content($content) {
		$this->content = $content;
	}
	
	/**
	 * An index to sort blocks
	 *
	 * @return integer
	 */
	public function get_index() {
		return $this->index;
	}
	
	/**
	 * Set index to sort blocks
	 *
	 * @param integer
	 */
	public function set_index($index) {
		$this->index = $index;
	}
	
	/**
	 * Name (used as CSS class)
	 *
	 * @return string
	 */
	public function get_name() {
		return $this->name;
	}
	
	/**
	 * Sets Name (used as CSS class)
	 *
	 * @param string
	 */
	public function set_name($name) {
		$this->name = $name;
	}
	
	/**
	 * One of LEFT, RIGHT, CONTENT etc...
	 *
	 * @return string
	 */
	public function get_position() {
		return $this->position;
	}
	
	/**
	 * Set position
	 *
	 * @param string
	 */
	public function set_position($position) {
		$this->position = $position;
	}
	
	/**
	 * Returns true if this block is valid
	 * 
	 * @return boolean True if this block has content
	 */
	public function is_valid() {
		return ($this->get_content() !== '');
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
	
	/**
	 * Renders what should be rendered
	 *
	 * @param int $policy Defines how to render, meaning depends on implementation
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/block');
		$view->assign('block', $this);
		$view->assign('policy', $policy);
		return $view->render();		
	}	
}
