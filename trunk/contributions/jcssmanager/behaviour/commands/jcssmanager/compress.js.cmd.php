<?php
/**
 * Compress JS 
 * 
 * Delegates to algorithm defines as ConfigJCSSManager::JS_COMPRESSOR
 * 
 * @author Gerd Riesselmann
 * @ingroup JCSSManager
 */
class JCSSManagerCompressJSCommand extends CommandDelegate {
	
	/**
	 * COnstructor
	 *  
	 * @param $in_files array
	 * @param $out_file string
	 * @return void
	 */
	public function __construct($in_files, $out_file) {
		$compressor = strtolower(Config::get_value(ConfigJCSSManager::JS_COMPRESSOR));
		Load::commands("jcssmanager/$compressor/compress.js");
		$cls = 'JCSSManagerCompressJS' . Load::filename_to_classname($compressor, 'Command'); 
		parent::__construct(new $cls($in_files, $out_file));
	}	
}