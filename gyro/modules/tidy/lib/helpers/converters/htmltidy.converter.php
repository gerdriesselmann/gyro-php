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
	private $predefined_params;

	public function __construct($global_params = array()) {
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
			'show-body-only' => false,
			'merge-divs' => false,
			//'merge-spans' => false, // Not widely supported on Debian system
			'hide-comments' => true,
			'lower-literals' => true,
			'char-encoding' => GyroString::plain_ascii(GyroLocale::get_charset(), ''),
			'wrap' => 0
		);
		$this->predefined_params = array_merge($predefined_params, $global_params);
	}

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
		if (GyroString::starts_with(trim(GyroString::to_lower($value)), '<script')) {
			return $value;
		}
		if (function_exists('tidy_parse_string')) {
			$is_partial_doc = (strpos($value, '<html') === false);
			$predefined_params = $this->predefined_params;
			$predefined_params['clean'] = !$is_partial_doc;
			$predefined_params['show-body-only'] = $is_partial_doc;
			$params = array_merge($predefined_params, Arr::force($params, false));

			$tidy = tidy_parse_string($value, $params, GyroString::plain_ascii(GyroLocale::get_charset(), ''));
			$tidy->cleanRepair();
			return tidy_get_output($tidy);
		} elseif (GYRO_TIDY_IGNORE_NOT_INSTALLED) {
			return $value;
		} else {
			throw new Exception('Tidy is not install, could not encode value');
		}
	}

	/**
 	 * Implemented for compatability, just returns the value passed in
	 */
	public function decode($value, $params = false) {
		return $value;		
	} 	
} 
