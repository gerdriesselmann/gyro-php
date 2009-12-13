<?php
/**
 * Converts plain HTML to tidyed HTML
 * 
 * Use either directly or through ConverterFactory like this:
 * 
 * @code
 * $tidy = ConverterFactory::encode($html, CONVERTER_TIDY);
 * @endcode
 * 
 * @author Gerd Riesselmann
 * @ingroup Tidy
 */
class ConverterHtmlTidy implements IConverter {
	/**
	 * Tidy up $value
	 * 
	 * @attention 
	 *   If $value starts with a <script> tag, the value is returned untouched.
	 *   This is because tidy will strip it, even if in mode 'show-body-only'
	 * 
	 * @param $value The original html
	 * @param $params Associative array containing tidy config parameters  
	 * @return string
	 */
	public function encode($value, $params = false) {
		//TODO this is a hotfix to keep tidy from striping <script>-Only-Content
		if (String::starts_with(trim(String::to_lower($value)), '<script')) {
			return $value;
		}
		$predefined_params = array(
            'bare' => true,
            'clean' => true,		
            'drop-empty-paras' => true,
            'drop-font-tags' => true,
            'drop-proprietary-attributes' => true,
            'enclose-block-text' => true,
  			'enclose-text' => true,
  			'indent' => true,
            'join-classes' => false,
            'join-styles' => false,
            'logical-emphasis' => true,
            'output-xhtml' => true,
			'doctype' => 'loose',
            'show-body-only' => (strpos($value, '<html') === false),
			'merge-divs' => false,
			//'merge-spans' => false, // Not widely supported on Debian system
			'hide-comments' => true,
			'lower-literals' => true,
			'char-encoding' => String::plain_ascii(GyroLocale::get_charset(), ''),
            'wrap' => 0
		);
		$params = array_merge($predefined_params, Arr::force($params, false));
		$tidy = tidy_parse_string($value, $params, String::plain_ascii(GyroLocale::get_charset(), ''));
		$tidy->cleanRepair();
		return tidy_get_output($tidy);
	}

	/**
 	 * Implemented for compatability, just returns the value passed in
	 */
	public function decode($value, $params = false) {
		return $value;		
	} 	
} 
