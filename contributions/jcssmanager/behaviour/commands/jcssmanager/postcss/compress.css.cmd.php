<?php
Load::commands('jcssmanager/base/compress.base');

class JCSSManagerCompressCSSPostcssCommand extends JCSSManagerCompressBaseCommand {
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
	 * Returns type of compressed file
	 *
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return $this->type;
	}

	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$combined = JCSSManager::concat_css_files($in_files);
			$tmp_file = Common::create_temp_file($combined);
			$files_to_unlink[] = $tmp_file;

			$ret->merge($this->invoke($tmp_file, $out_file));
		}

		return $ret;
	}

	/**
	 * Invoke commands
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke($in_file, $out_file) {
		$ret = new Status();

		$bin_cmd = 'node_modules/.bin/postcss';
		$out_file = JCSSManager::make_relativ($out_file);

		$webpack_options = array();
		$webpack_options['--dir'] = JCSSManager::root_dir();
		$webpack_options['--output'] = $out_file;

		$possible_config = Config::get_value(ConfigJCSSManager::POSTCSS_CONFIG_FILE);
		if ($possible_config) {
			$webpack_options['--config'] = $possible_config;
		}

		$bin_cmd =
			$bin_cmd . ' ' . $in_file . ' ' .
 			Arr::implode(' ', $webpack_options, ' ');

		$output = array();
		$return = 0;
		exec($bin_cmd, $output, $return);

		if ($return) {
			$ret->append('JCSSManager: Error running PostCSS.');
			$ret->append(implode("\n", $output));
		}

		return $ret;		
	}
}