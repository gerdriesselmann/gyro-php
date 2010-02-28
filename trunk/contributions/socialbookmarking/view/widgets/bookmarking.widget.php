<?php
/**
 * Prints social bookmarking list
 *
 * @author Gerd Riesselmann
 * @ingroup SocialBookmarking
 */
class WidgetBookmarking implements IWidget {
	/**
	 * Pagedata to hold Head Information
	 *
	 * @var PageData
	 */
	public $page_data;
	/**
	 * @var array
	 */
	public $services;

	/**
	 * Output social bookmarking lsit
	 *
	 * @param PageData $page_data
	 * @param string|array $set_or_array Set name or array of services
	 * @param int $policy 
	 * @return string
	 */
	public static function output($page_data, $set_or_array, $policy = self::NONE) {
		$w = new WidgetBookmarking($page_data, $set_or_array);
		return $w->render($policy);
	}
	
	public function __construct($page_data, $set_or_array) {
		$this->page_data = $page_data;
		$this->services = is_array($set_or_array) ? $set_or_array : SocialBookmarking::create_set($set_or_array);
	}
	
	/**
	 * Renders
	 *
	 * @param int $policy
	 * @return string
	 */
	public function render($policy = self::NONE) {
		$arr_services = array();
		foreach($this->services as $service) {
			$tmp = SocialBookmarking::get_service($service);
			if ($tmp) {
				$arr_services[] = $tmp;
			} 		
		}
		
		$this->page_data->head->add_css_file('css/socialbookmarking.css', true);
		
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/bookmarking', false);
		$view->assign('page_data', $this->page_data);
		$view->assign('services', $arr_services);
		return $view->render();
	}	
}