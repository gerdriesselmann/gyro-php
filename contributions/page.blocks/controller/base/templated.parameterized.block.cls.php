<?php
/**
 * A block that renders itself from a template and can be passed parameters
 * 
 * The block instance is available as $block within the template. So you may change title or 
 * name like this:
 * 
 * @code
 * $block->title = 'New title';
 * $block->name = 'newname';
 * @endcode
 * 
 * @ingroup Blocks
 * @author Gerd Riesselmann
 */
class TemplatedParameterizedBlock extends TemplatedBlock {
	protected $params = array();
	
	/**
	 * Constructor
	 * 
	 * @param string $name The name of this block. Used as class, too
	 * @param string $title The title of the block. Displayed as heading, e.g.
	 * @param string $template The template to render
	 * @param array $params Assoziative array that gets passed to the view
	 * @param integer $index The block's index. A block with lowest index will be displayed first
	 * @param enum $position Where the block is to be displayed. 
	 */
	public function __construct($name, $title, $template, $params, $index = 1000, $position = self::LEFT) {
		$this->params = $params;
		parent::__construct($name, $title, $template, $index, $position);
	}
	
	/**
	 * Configure the view
	 * 
	 * @param IView $view
	 */
	protected function configure_view($view) {
		$view->assign_array($this->params);
	}
}