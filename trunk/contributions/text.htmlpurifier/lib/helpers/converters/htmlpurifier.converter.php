<?php
/**
 * Convert HTML to purified HTML 
 * 
 * @author Gerd Riesselmann
 * @ingroup HtmlPurifier
 */
class ConverterHtmlPurifier implements IConverter {
	/**
	 * Purify HTML
	 * 
	 * @param string $value
	 * @param array
	 */
	public function encode($value, $params = false) {
		require_once Load::get_module_dir('text.htmlpurifier') . '3rdparty/htmlpurifier-4/HTMLPurifier.standalone.php';
		
		$config = HTMLPurifier_Config::createDefault();
		$config->set('Core.Encoding', GyroLocale::get_charset());
		$config->set('Cache.SerializerPath', Config::get_value(Config::TEMP_DIR) . 'htmlpurifier');
		
		$purifier = new HTMLPurifier($config);
    	$value = $purifier->purify($value);
			
		return $value;
	}
	
	/**
	 * This function does nothing! Especially it does NOT purify HTML! 
	 */
	public function decode($value, $params = false) {
		return $value;		
	} 	
} 
