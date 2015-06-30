<?php
/**
 * Invokes SASS command line
 *
 * @ingroup SASS
 */
class SASS {
	/**
	 * Compile File
	 *
	 * @code
	 * Sass::compile('main.sass');
	 * @endcode
	 *
	 * @param string $file Name of file relative to /view/sass/
	 * @return Status
	 */
	public static function compile_file($file) {
		$in_file = APP_INCLUDE_ABSPATH . 'view/sass/' . $file;
		$out_file = APP_INCLUDE_ABSPATH . Config::get_value(ConfigSASS::OUTPUT_DIR) . $file;
		return self::run_cli_with($in_file, $out_file);
	}

	/**
	 * Compile all files in default directory
	 *
	 * @return Status
	 */
	public static function compile_all() {
		$in_file = APP_INCLUDE_ABSPATH . 'view/sass/';
		$out_file = APP_INCLUDE_ABSPATH . Config::get_value(ConfigSASS::OUTPUT_DIR);
		return self::run_cli_with($in_file, $out_file);
	}

	/**
	 * Invoke command line with extra params
	 * @param string $in_file Input file with full absolute path
	 * @param string $out_file Output file with full absolute path
	 * @return Status
	 */
	private static function run_cli_with($in_file, $out_file) {
		$elems = array('sass', '--update', escapeshellarg($in_file . ':' . $out_file));
		$cli = implode(' ', $elems);

		Load::commands('generics/execute.shell');
		$cmd = new ExecuteShellCommand($cli);
		return $cmd->execute();
	}
}