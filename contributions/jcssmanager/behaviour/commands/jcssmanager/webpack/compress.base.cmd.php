<?php
Load::commands('jcssmanager/base/compress.base');

class JCSSManagerCompressBaseWebpackCommand extends JCSSManagerCompressBaseCommand {
	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$ret->merge($this->invoke_webpack($in_files, $out_file));
		}

		return $ret;
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
	 * @param string[] $in_files
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke_webpack($in_files, $out_file) {
		$ret = new Status();

		$webpack_cmd = 'node_modules/.bin/webpack';
		$out_file = JCSSManager::make_relativ($out_file);

		$webpack_options = array();
		$webpack_options['--optimize-minimize'] = '';
		$webpack_options['--env.production'] = '';
		$webpack_options['--output-path'] = JCSSManager::root_dir();
		$webpack_options['--output-filename'] = $out_file;

		$possible_config = Config::get_value(ConfigJCSSManager::WEBPACK_CONFIG_FILE);
		if ($possible_config) {
			$webpack_options['--config'] = $possible_config;
		}

		$in_files = array_map(function($f) {
			return  JCSSManager::make_absolute($f);
		}, $in_files);

		$webpack_cmd =
			$webpack_cmd . ' ' .
			Arr::implode(' ', $webpack_options, ' ') . ' ' .
			implode(' ', $in_files);

		$output = array();
		$return = 0;
		//echo $webpack_cmd . "\n";
		exec($webpack_cmd, $output, $return);

		if ($return) {
			$ret->append('JCSSManager: Error running webpack.');
			$ret->append(implode("\n", $output));
		}

		return $ret;		
	}
}