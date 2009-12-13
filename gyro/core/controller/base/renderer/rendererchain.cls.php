<?php
/**
 * Delegates rendering to a chain of IRenderDecorators
 *
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class RendererChain implements IRenderer {
	/**
	 * Page data 
	 *
	 * @var PageData
	 */
	protected $page_data = null; 
	
	/**
	 * Root of Chain of IRenderDecorators
	 *
	 * @var IRenderDecorator
	 */
	protected $chain_root = null;
	
	/**
	 * Constuctor  
	 *
	 * @param PageData $page_data
	 * @param array $arr_decorators Array of IRenderDecorators
	 */	
	public function __construct($page_data, $arr_decorators) {
		$this->page_data = $page_data;
		$this->chain_root = new RenderDecoratorBase(); // Ensure there is at least somethin
		foreach($arr_decorators as $decorator) {
			if ($decorator instanceof IRenderDecorator) {
				$this->chain_root->append($decorator);
			}	
		}
	}
	
	/**
	 * Renders what should be rendered
	 *
	 * @param int $policy 
	 * @return string The rendered content
	 */
	public function render($policy = self::NONE) {
		$this->chain_root->initialize($this->page_data);
		return $this->chain_root->render_page($this->page_data, $this->chain_root, $policy);
	}
}
