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
				if (substr($file, 0, 1) !== '/' && strpos($file, '://') == false) {
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
		$ret = new Status();
		
		$yui_path = false;
		$ret->merge(self::get_yui_jar($yui_path));
		if ($ret->is_ok()) {
			$yui_cmd = 'java -jar ' . $yui_path;

			$yui_options = array();
			$yui_options['--type'] = $type;
			$yui_options['--charset'] = GyroLocale::get_charset();
			$yui_options['--line-break'] = 1000;
			$yui_options['-o'] = $out_file;
		
			$yui_cmd = $yui_cmd . ' ' . Arr::implode(' ', $yui_options, ' ') . ' ' . $in_file;
		
			$output = array();
			$return = 0;
			exec($yui_cmd, $output, $return);

			if ($return) {
				$ret->append('JCSSManager: Error running yuicompressor.');
				$ret->append(implode(' ', $output));
			}
		}
		
		return $ret;		
	}

	/**
	 * Get path to yuicompressor.jar
	 *
	 * @return Status
	 */
	protected function get_yui_jar(&$path) {
		$ret = new Status();
		
		$yui_version = Config::get_value(ConfigJCSSManager::YUI_VERSION);
		if ($yui_version == 'latest') {
			$yui_version = ConfigJCSSManager::YUI_VERSION_LATEST;
		}
		$yui_jar	 = 'yuicompressor/' . $yui_version . '/yuicompressor.jar';

		// test APP_3RDPARTY_DIR first ...
		$app_thirdparty = Config::get_value(Config::THIRDPARTY_DIR);
		if (!empty($app_thirdparty)) {
			if (substr($app_thirdparty, -1) != '/') {
				$app_thirdparty .= '/';
			}
			if (file_exists($app_thirdparty . $yui_jar)) {
				$path = $app_thirdparty . $yui_jar;
				return $ret;
			}
		}
			
		$module_3rdparty  = Load::get_module_dir('jcssmanager') . '3rdparty/';
		if (file_exists($module_3rdparty . $yui_jar)) {
			$path = $module_3rdparty . $yui_jar;
			return $ret;
		}

		$ret->append('yuicompressor.jar not found at configured YUI_VERSION');
		
		return $ret;
	}
}