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
		$page_data->in_history = false;
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
				if ($this->page_data->status instanceof Status && $this->page_data->status->is_error()) {
					$is_error = true;
					$data['error'] = $this->page_data->status->to_string(Status::OUTPUT_PLAIN);
				}	
				else {
					$is_error = false;
					$data['result'] = $this->page_data->ajax_data;
				}
				break;
		} 
		$data['is_error'] = $is_error;
		$rendered_content = ConverterFactory::encode($data, CONVERTER_JSON);

		if (Common::flag_is_set($policy, self::POLICY_GZIP)) {
			$rendered_content = gzdeflate($rendered_content, 9);
		}

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
		if (!Common::flag_is_set($policy, self::CONTENT_ONLY)) {
			header('Content-Type: application/json');
		}
		parent::render_postprocess($rendered_content, $policy);
	}	
}
?>