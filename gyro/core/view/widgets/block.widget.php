<?php
/**
 * A widget printing a named block upon actions retrieved from given item
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetBlock implements IWidget {
	/**
	 * Position to print blocks for (LEFT, RIGHT, CONTENT, or empty to print all blocks)
	 *
	 * @var string
	 */
	public $position;
	/**
	 * Page Data
	 * 
	 * @var PageData
	 */
	public $page_data;

	/**
	 * Retrieves named block and stores it in page data
	 *
	 * @param string $name
	 * @param PageData $page_data
	 * @param string $route_id
	 * @return void
	 */
	public static function retrieve($name, $page_data, $route_id = '', $position = false, $weight = false, $more_params = array()) {
		$params = array(
			'name' => $name,
			'route_id' => $route_id
		);
		$params = array_merge($more_params, $params);
		
		$result = array();
		EventSource::Instance()->invoke_event('block', $params, $result);
		
		if ($page_data) {
			foreach($result as $block) {
				$page_data->add_block($block, $position, $weight);	
			}
		}
		return $result;
	}

	public static function render_blocks($arr_blocks) {
		$out = '';
		foreach ($arr_blocks as $block) {
			$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/block');
			$view->assign('block', $block);
			$out .= $view->render();
		}
		return $out;	
	}
	
	/**
	 * Output Blocks
	 *
	 * @param PageData $page_data
	 * @param string $position
	 * @return string
	 */
	public static function output($page_data, $position = '') {
		$w = new WidgetBlock($page_data, $position);
		return $w->render();
	}
	
	public function __construct($page_data, $position) {
		$this->position = $position;
		$this->page_data = $page_data;
	}
	
	public function render($policy = self::NONE) {
		$this->page_data->sort_blocks();
		$arr_blocks = array();
		foreach ($this->page_data->blocks as $block) {
			if ($this->position && $this->position != $block->position) {
				continue;
			}
			$arr_blocks[] = $block;
		}
		return self::render_blocks($arr_blocks);	
	}
}
