<?php
Load::commands('jcssmanager/base/compress.base');

class JCSSManagerCompressBaseYuiCommand extends JCSSManagerCompressBaseCommand {
	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$sourcefile = $this->concat($in_files, $ret);
			$files_to_unlink[] = $sourcefile;
			
			$ret->merge($this->invoke_yui($sourcefile, $out_file));
		}
		
		return $ret;
	}
	
	/**
	 * Concat the gicen files into one
	 * 
	 * @param array $arr_files
	 * @param Status $err
	 * @return string Created concatenation file
	 */
	protected function concat($arr_files, Status $err) {
		$tmp_file = tempnam('/tmp', 'jcss');
		if ($tmp_file === false) {
			$err->merge('JCSSManager: Could not create tempfile');
		}
		else {
			$handle = fopen($tmp_file, 'w');
			foreach($arr_files as $file) {
				if (substr($file, 0, 1) !== '/') {
					$file = Config::get_value(Config::URL_ABSPATH) . $file;
				}
				fwrite($handle, $this->get_file_contents($file));
			}			
			fclose($handle);
		}
		return $tmp_file;
	}

	/**
	 * Return file content
	 * 
	 * @param string $file
	 * @return string
	 */
	protected function get_file_contents($file) {
		return file_get_contents($file);
	}
	
	/**
	 * Invoke YUICOmpressor. TO be overloaded
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke_yui($in_file, $out_file) {
		$ret = new Status();
		return $ret;
	}
	
	/**
	 * Invoke YUICOmpressor
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @param array $yui_options
	 * @return Status 
	 */
	protected function run_yui($in_file, $out_file, $type) {
		$module_dir = Load::get_module_dir('jcssmanager');
		$yui_cmd = 'java -jar ' . $module_dir . '3rdparty/yuicompressor/yuicompressor.jar';
		
		$yui_options = array();
		$yui_options['--type'] = $type;
		$yui_options['--charset'] = GyroLocale::get_charset();
		$yui_options['--line-break'] = 1000;
		$yui_options['-o'] = $out_file;
		
		$yui_cmd = $yui_cmd . ' ' . Arr::implode(' ', $yui_options, ' ') . ' ' . $in_file;
		
		$output = array();
		$return = 0;
		exec($yui_cmd, $output, $return);

		$ret = new Status();
		if ($return) {
			$ret->append('JCSSManager: Error running yuicompressor.');
			$ret->append(implode(' ', $output));
		}
		
		return $ret;		
	} 
}