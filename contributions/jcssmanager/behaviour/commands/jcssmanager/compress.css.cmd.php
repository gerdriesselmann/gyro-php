<?php
Load::commands('jcssmanager/compress.base');

class JCSSManagerCompressCSSCommand extends JCSSManagerCompressBaseCommand {
	protected $charset = '';
	protected $type;

	/**
	 * COnstructor
	 *  
	 * @param $in_files array
	 * @param $out_file string
	 * @return void
	 */
	public function __construct($in_files, $out_file, $type) {
		$this->type = $type;
		parent::__construct($in_files, $out_file);
	}	
	
	/**
	 * Return file content
	 * 
	 * @param string $file
	 * @return string
	 */
	protected function get_file_contents($file) {
		$ret = '';
		$handle = fopen($file, 'r');
		$regex = '@(url\s*\(\s*"?)([\w.][^"\)]*)@';
		$rel_path = str_replace(Config::get_value(Config::URL_ABSPATH), '/', realpath($file));
		$replace = '$1' .  dirname($rel_path) . '/$2'; 
		while(($line = fgets($handle)) !== false) {
			// Works around a bug in WebKit, which dislikes two charset declarations in one file
			$line = trim($line);
			$token = substr($line, 0, 7);
			if ($token === '@charse') {
				continue;
			}
			// Resolve imports			
			if ($token === '@import') {
				$start = strpos($line, '(', 7);
				if ($start !== false) {
					$end = strpos($line, ')', $start);
					if ($end !== false) {
						$start++;
						$file_to_include = trim(substr($line, $start, $end - $start), "'\" \t");
						if (substr($file_to_include, 0, 1) !== '/') {
							$file_to_include = dirname($file) . '/' . $file_to_include;
						}
						else {
							$file_to_include = JCSSManager::make_absolute($file_to_include);
						}
						$line = $this->get_file_contents($file_to_include);
					}
				}
			}
			// Set all url(..) stuff absolute
			$line = preg_replace($regex, $replace, $line);
			$ret .= $line;
		}

		return $ret;
	}
	
	/**
	 * Invoke YUICOmpressor
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke_yui($in_file, $out_file) {
		return $this->run_yui($in_file, $out_file, 'css');
	}
	
	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return $this->type;
	}	
}