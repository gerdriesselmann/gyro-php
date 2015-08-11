<?php
/**
 * Bootstrap helper class
 *
 * @author Gerd Riesselmann
 * @ingroup Bootstrap3
 */
class Bootstrap3 {
	/**
	 * Enable Bootstrap by setting CSS and JS on PageData
	 *
	 * @param PageData $page_data
	 */
	public static function enable(PageData $page_data) {
		$page_data->head->add_css_file('css/bootstrap.css');
		$page_data->head->add_js_file('js/bootstrap.js');
	}
}
