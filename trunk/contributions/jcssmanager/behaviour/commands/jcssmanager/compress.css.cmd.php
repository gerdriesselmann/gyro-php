<?php
/**
 * Compress CSS 
 * 
 * Delegates to algorithm defines as ConfigJCSSManager::CSS_COMPRESSOR
 * 
 * @author Gerd Riesselmann
 * @ingroup JCSSManager
 */
class JCSSManagerCompressCSSCommand extends CommandDelegate {
	
	/**
	 * COnstructor
	 *  
	 * @param $in_files array
	 * @param $out_file string
	 * @param $type CSS type (CSS, CSS_IE6 etc)
	 * @return void
	 */
	public function __construct($in_files, $out_file, $type) {
		$compressor = strtolower(Config::get_value(ConfigJCSSManager::CSS_COMPRESSOR));
		Load::commands("jcssmanager/$compressor/compress.css");
		$cls = 'JCSSManagerCompressCSS' . Load::filename_to_classname($compressor, 'Command'); 
		parent::__construct(new $cls($in_files, $out_file, $type));
	}	
}