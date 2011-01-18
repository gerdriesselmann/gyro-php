<?php
/**
 * Contains current page data
 * 
 * @author Gerd Riesselmann
 * @ingroup StaticPageData 
 */
class StaticPageData {
	private static $page_data = null;
	
	/**
	 * Return TRUE if data is set
	 * @return bool
	 */
	public static function data_is_set() {
		return self::$page_data instanceof PageData;
	}
	
	/**
	 * Get page data
	 * 
	 * @return PageData
	 */
	public static function data() {
		return self::$page_data;
	}
	
	/**
	 * Set page data
	 */
	public static function set_data(PageData $data) {
		self::$page_data = $data;
	}	
}