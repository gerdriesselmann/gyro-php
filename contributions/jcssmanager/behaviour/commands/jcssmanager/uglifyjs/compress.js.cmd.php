<?php
Load::commands('jcssmanager/base/compress.base');

class JCSSManagerCompressJSUglifyjsCommand extends JCSSManagerCompressBaseCommand {
	/**
	 * COmpress given files
	 *
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$ret->merge($this->invoke_uglifyjs($in_files, $out_file));
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
	protected function invoke_uglifyjs($in_files, $out_file) {
		$ret = new Status();

		$uglifyjs_cmd = 'node_modules/.bin/uglifyjs';

		$uglifyjs_options = array();
		$uglifyjs_options['--compress'] = Config::get_value(ConfigJCSSManager::UGLIFY_COMPRESS_OPTIONS);
		$uglifyjs_options['--mangle'] = '';
		$uglifyjs_options['--output'] = $out_file;

		$in_files = array_map(function($f) {
			return  JCSSManager::make_absolute($f);
		}, $in_files);

		$uglifyjs_cmd =
			$uglifyjs_cmd . ' ' .
			implode(' ', $in_files) . ' ' .
			Arr::implode(' ', $uglifyjs_options, ' ');

		$output = array();
		$return = 0;
		echo $uglifyjs_cmd . "\n";
		exec($uglifyjs_cmd, $output, $return);

		if ($return) {
			$ret->append('JCSSManager: Error running uglifyjs.');
			$ret->append(implode("\n", $output));
		}

		return $ret;
	}


	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return JCSSManager::TYPE_JS;
	}
}