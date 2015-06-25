<?php
/**
 * PostCSS invocation
 *
 * First enable extensions, then call process for either string, file, or directory
 *
 * @code
 * PostCSS::extension('autoprefixer', array('browsers' => '> 5%'));
 * $err = PostCSS::process_file('in.css', 'out.css');
 * @endcode
 *
 * @author Gerd Riesselmann
 * @ingroup PostCSS
 */
class PostCSS {
	private static $extensions = array();

	/**
	 * @param string $name Name of extension
	 * @param array $conf Associative array containing extension configuration
	 */
	public static function extension($name, $conf = array()) {
		self::$extensions[$name] = Arr::force($conf, false);
	}

	/**
	 * Process file $in and write output to $out
	 *
	 * @param string $in
	 * @param string $out
	 * @return Status
	 */
	public static function process_file($in, $out) {
		return self::run_cli_with(array(
			'--out', escapeshellarg($out), escapeshellarg($in)
		));
	}

	/**
	 * Process all files in directory $in and write output to
	 * directory $out
	 *
	 * @param string $in
	 * @param string $out
	 * @return Status
	 */
	public static function process_directory($in, $out) {
		return self::run_cli_with(array(
			'--out', escapeshellarg($out), escapeshellarg($in)
		));
	}

	/**
	 * Process given CSS content and return as $out
	 *
	 * @param string $in
	 * @param string $out
	 * @return Status
	 */
	public static function process_content($in, &$out) {
		$temp_in = Common::create_temp_file($in);
		$temp_out = Common::create_temp_file('');
		$ret = self::process_file($temp_in, $temp_out);
		if ($ret->is_ok()) {
			$out = file_get_contents($temp_out);
		}

		self::remove_temp_files(array(
			$temp_in, $temp_out
		));

		return $ret;
	}

	/**
	 * Invoke command line with extra params
	 *
	 * @param array $params
	 * @return Status
	 */
	private static function run_cli_with($params) {
		$temp_files_created = array();
		$cli = self::build_basic_command($temp_files_created);
		$cli .= ' ' . implode(' ', $params);

		Load::commands('generic/executeshell');
		$cmd = new ExecuteShellCommand($cli);
		$ret = $cmd->execute();

		self::remove_temp_files($temp_files_created);
		return $ret;
	}

	/**
	 * Build postcss command including extension, but without input or output
	 *
	 * @param array $temp_files_created
	 * @return string
	 */
	private static function build_basic_command(&$temp_files_created) {
		$ext = array_map(
			function($n) {
				return '--use ' . escapeshellarg($n);
			},
			array_keys(self::$extensions)
		);
		$conf = ConverterFactory::encode(self::$extensions, CONVERTER_JSON);
		$conf_file = Common::create_temp_file($conf);
		$temp_files_created[] = $conf_file;

		$ret = 'postcss ';
		$ret .= implode(' ', $ext);
		$ret .= ' --config=' . escapeshellarg($conf_file);
		return $ret;
	}

	/**
	 * Remove created temp files
	 *
	 * @param array $temp_files
	 */
	private function remove_temp_files($temp_files) {
		foreach($temp_files as $f) {
			@unlink($f);
		}
	}
}