<?php
/**
 * Renders page as Ajax result
 * 
 * In case of an error, the 'error' member gets set in return data.
 * Else member 'result' is set to PageData::ajax_data  
 * 
 * @author Gerd Riesselmann
 * @ingroup Ajax
 */
class AjaxView extends PageViewBase {
	/**
	 * Contructor takes the page data
	 */	
	public function __construct($page_data) {
		parent::__construct($page_data, '');
	} 
	
	/**
	 * Sets content
	 * 
	 * @param mixed $rendered_content The content rendered
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_content(&$rendered_content, $policy) {
		$data = array();
		$is_error = true;
		switch ($this->page_data->status_code) {
			case ControllerBase::ACCESS_DENIED:
				$data['error'] = tr('Access denied', 'core');
				break;
			case ControllerBase::NOT_FOUND:
				$data['error'] = tr('Page not found', 'core');
				break;
			case ControllerBase::INTERNAL_ERROR:
				$data['error'] = tr('Server error', 'core');
				break;
			default:
				$is_error = false;
				$data['result'] = $this->page_data->ajax_data;
				break;
		} 
		$date['is_error'] = $is_error;
		$rendered_content = ConverterFactory::encode($data, CONVERTER_JSON);
	}

	protected function after_render(&$rendered_content, $policy) {
		// Do nothing here
	}

	/**
	 * Assign variables
	 * 
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 */
	protected function assign_default_vars($policy) {
		// Do nothing here	
	}
	
	/**
	 * Returns true, if cache should be used
	 *
	 * @return bool
	 */
	protected function should_cache() {
		return false;
	}	
	
	/**
	 * Called after content is rendered, always
	 * 
	 * @param mixed $rendered_content The content rendered
	 * @paramint $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_postprocess(&$rendered_content, $policy) {
		$this->send_status();
		
		if (!Common::flag_is_set($policy, self::CONTENT_ONLY)) {
			header('Cache-Control: maxage=3600'); //Fix for IE in SSL 
			header('Pragma: public');
			header('Content-Length: ') . strlen($rendered_content);			
			header('Content-Type: application/json');
		}
	}	
}
?>