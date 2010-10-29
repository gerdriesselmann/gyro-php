<?php
/**
 * A block that renders itself from a template
 * 
 * The block instance is available as $block within the template. So you may change title or 
 * name like this:
 * 
 * @code
 * $block->set_title('New title');
 * $block->set_name('newname');
 * @endcode
 * 
 * @ingroup Blocks
 * @author Gerd Riesselmann
 */
class TemplatedBlock extends BlockBase {
	protected $view_is_rendered = false;
	protected $template = ''; 
	
	/**
	 * Constructor
	 * 
	 * @param string $name The name of this block. Used as class, too
	 * @param string $title The title of the block. Displayed as heading, e.g.
	 * @param string $template The template to render
	 * @param integer $index The block's index. A block with lowest index will be displayed first
	 * @param enum $position Where the block is to be displayed. 
	 */
	public function __construct($name, $title, $template, $index = 1000, $position = self::LEFT) {
		parent::__construct($name, $title, '', $index, $position);
		$this->template = $template;
	}

	/**
	 * Create and configure the view
	 * 
	 * @param string $template The template
	 * @return IView
	 */
	protected function create_view($template) {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, $template, $page_data);
		$view->assign('block', $this);
		$this->configure_view($view);					
		return $view;
	}
	
	/**
	 * Configure the view
	 * 
	 * @param IView $view
	 */
	protected function configure_view($view) {
		// to be overloaded
	}
	
	/**
	 * Get content of block (HTML)
	 *
	 * @return string
	 */
	public function get_content() {
		if (!$this->view_is_rendered) {
			$this->view_is_rendered = true;
			$view = $this->create_view($this->template);
			$this->set_content($view->render());
		}
		return parent::get_content();
	}
}