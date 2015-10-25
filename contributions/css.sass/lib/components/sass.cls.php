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
		$in_dir = APP_INCLUDE_ABSPATH . 'view/sass/';
		$file = str_replace($in_dir, '', $file);
		$in_file = $in_dir . $file;

		$out_file = APP_INCLUDE_ABSPATH . Config::get_value(ConfigSASS::OUTPUT_DIR);
		$out_base_file = str_replace('.scss', '.css', $file);
		$out_base_file = str_replace('.sass', '.css', $out_base_file);
		if (Config::has_feature(ConfigSASS::KEEP_DIRECTORY_STRUCTURE)) {
			$out_file .= $out_base_file;
		} else {
			$out_file .= basename($out_base_file);
		}
		return self::run_cli_with($in_file, $out_file);
	}

	/**
	 * Compile all files in default directory
	 *
	 * @return Status
	 */
	public static function compile_all() {
		$in_file = APP_INCLUDE_ABSPATH . 'view/sass/';
		if (Config::has_feature(ConfigSASS::KEEP_DIRECTORY_STRUCTURE)) {
			$out_file = APP_INCLUDE_ABSPATH . Config::get_value(ConfigSASS::OUTPUT_DIR);
			return self::run_cli_with($in_file, $out_file);
		} else {
			return self::compile_dir($in_file);
		}
	}

	private static function compile_dir($dir_path) {
		$err = new Status();
		$it = new DirectoryIterator($dir_path);
		foreach($it as $file_info) {
			if (!$file_info->isDot()) {
				if ($file_info->isDir()) {
					$err->merge(self::compile_dir($file_info->getPathname()));
				} else {
					switch ($file_info->getExtension()) {
						case 'scss':
						case 'sass':
							$err->merge(self::compile_file($file_info->getPathname()));
							break;
					}
				}
			}
		}
		return $err;
	}

	/**
	 * Invoke command line with extra params
	 * @param string $in_file Input file with full absolute path
	 * @param string $out_file Output file with full absolute path
	 * @return Status
	 */
	private static function run_cli_with($in_file, $out_file) {
		$elems = array('sass', '-f', '--update', escapeshellarg($in_file . ':' . $out_file));
		$cli = implode(' ', $elems);

		Load::commands('generics/execute.shell');
		$cmd = new ExecuteShellCommand($cli);
		return $cmd->execute();
	}
}