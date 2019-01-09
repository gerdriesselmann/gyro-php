<?php
require_once dirname(__FILE__) . '/contentviewbase.cls.php';

/**
 * Create XML content 
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class XmlViewBase extends ContentViewBase {
	/**
	 * Called before content is rendered, always
	 * 
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_preprocess($policy) {
		$this->page_data->in_history = false;
		$this->page_data->page_template = 'emptypage';
	}
	
	/**
	 * Called after content is rendered, but not if content is taken from cache
	 * 
	 * @param $rendered_content The content rendered
	 * @param $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function after_render(&$rendered_content, $policy) {
		$xml_header = '<?xml version="1.0" encoding="' . GyroLocale::get_charset() . '"?>';
		$rendered_content = $xml_header . $rendered_content;
		parent::after_render($rendered_content, $policy);
		if (!Common::flag_is_set($policy, self::CONTENT_ONLY)) {
			GyroHeaders::set('Content-Type', 'application/xml; charset=' . GyroLocale::get_charset(), true);
			$this->page_data->head->robot_headers();
		}
	}	
}
