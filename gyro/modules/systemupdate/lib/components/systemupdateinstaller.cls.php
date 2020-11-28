<?php
/**
 * Class to help installing stuff 
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class SystemUpdateInstaller {
	const COPY_NO_REPLACE = 'no_replace';
	const COPY_OVERWRITE = 'overwrite';
	
	const HTACCESS_OPTIONS = 'OPTIONS';
	const HTACCESS_REWRITE = 'REWRITE';
	
	/**
	 * Copy given files to the web root directory
	 * 
	 * @param string $dir Source directory
	 * @param array $files array of file names relatice to $dir
	 * @param string $policy Either SystemUpdateInstaller::COPY_NO_REPLACE or SystemUpdateInstaller::COPY_OVERWRITE
	 * 
	 * @return Status
	 */
	public static function copy_to_webroot($dir, $files, $policy = self::COPY_OVERWRITE) {
		$ret = new Status();
		$webroot = Config::get_value(Config::URL_ABSPATH);
		
		return self::copy_to_dir($webroot, $dir, $files, $policy);
	}

	/**
	 * Copy given files to the given directory benath /app
	 * 
	 * @param string $target_dir Target directory (relative to /app)
	 * @param string $source_dir Source directory (absolute path)
	 * @param array $files array of file names relative to $dir
	 * @param string $policy Either SystemUpdateInstaller::COPY_NO_REPLACE or SystemUpdateInstaller::COPY_OVERWRITE
	 * 
	 * @return Status
	 */
	public static function copy_to_app($target_dir, $source_dir, $files, $policy = self::COPY_OVERWRITE) {
		$ret = new Status();
		$app = APP_INCLUDE_ABSPATH;
		
		return self::copy_to_dir($app . $target_dir, $source_dir, $files, $policy);
	}
	
	/**
	 * Copy a file to app
	 * 
	 * For example this copies the file test.php.example to the www subdirectory
	 * 
	 * @code
	 * SystemUpdateInstaller::copy_file_to_app('/var/backup/test.php.example', 'www/test.php');
	 * @endcode
	 * 
	 * @param string $source_file Absolute path to source file
	 * @param string $target_file path to target file, relative to /app
	 * @param string $policy Either SystemUpdateInstaller::COPY_NO_REPLACE or SystemUpdateInstaller::COPY_OVERWRITE
	 * 
	 * @return Status
	 */
	public static function copy_file_to_app($source_file, $target_file, $policy = self::COPY_OVERWRITE) {
		$ret = new Status();
		$target = APP_INCLUDE_ABSPATH . ltrim($target_file, '/');
		if ($policy == self::COPY_OVERWRITE || !file_exists($target)) {
			if (!copy($source_file, $target)) {
				$ret->merge(tr(
					'Could not create %target from %source. Please do it manually', 
					'systemupdate', 
					array('%target' => $target, '%source' => $source_file)
				));
			}
		}
		return $ret;				
	}
		
	/**
	 * Copy given files to the given directory
	 * 
	 * @param string $target_dir Target directory (absolute path)
	 * @param string $source_dir Source directory (absolute path)
	 * @param array $files array of file names relative to $dir
	 * @param string $policy Either SystemUpdateInstaller::COPY_NO_REPLACE or SystemUpdateInstaller::COPY_OVERWRITE
	 * 
	 * @return Status
	 */
	private static function copy_to_dir($target_dir, $source_dir, $files, $policy) {
		$ret = new Status();

		foreach(Arr::force($files, false) as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			
			$target = $target_dir . $file;
			$source = $source_dir . $file;

			if (is_dir($source)) {
				$ret->merge(self::copy_to_dir($target_dir, $source_dir, self::read_dir($source_dir, $file), $policy));
			}
			else {
				if ($policy == self::COPY_NO_REPLACE) {
					if (file_exists($target)) {
						continue;
					}
				}
				$target_base_dir = dirname($target);
				if (!file_exists($target_base_dir)) {
					mkdir($target_base_dir, 0775, true);
				}
				if (!copy($source, $target)) {
					$ret->append(tr('Could not copy %s to %d', 'systemupdate', array('%s' => $source, '%d' => $target)));
				} 
			}
		}
		
		return $ret;		
	}
	
	/**
	 * Inserts $content in .htaccess
	 * 
	 * @param string $module Module requesting changes
	 * @param string $section Section in .htaccess
	 * @param string|array $content
	 * 
	 * @return Status
	 */
	public static function modify_htaccess($module, $section, $content) {
		$ret = new Status();
		if (is_array($content)) {
			$content = implode("\n", $content);
		}

		$start = '## start ' .  $module . ' ' . $section;
		$end = '## end ' . $module . ' ' . $section;
		$content = "$start\n$content\n$end";

		$htaccess_path = Config::get_value(Config::URL_ABSPATH) . '.htaccess';
		$htaccess = @file_get_contents($htaccess_path);
		// Remove old
		$pattern = preg_quote($start, '|') . '.*' . preg_quote($end,'|');
		$htaccess = preg_replace('|' . $pattern . '|s', '', $htaccess);
		// Insert new
		$section_string = "### BEGIN $section ###";
		$section_start = strpos($htaccess, $section_string);
		if ($section_start !== false) {
			$htaccess = str_replace($section_string, $section_string . "\n\n" . $content, $htaccess);
			while(strpos($htaccess, "\n\n\n") !== false) {
				$htaccess = str_replace("\n\n\n", "\n\n", $htaccess);
			}
			if (file_put_contents($htaccess_path, $htaccess) === false) {
				$ret->append('Could not write .htaccess');
			}			
		}   
		else {
			$ret->append("Your .htaccess is not ready to be automatically modified - or it misses the section $section");
		}
		return $ret;
	} 

	/**
	 * Reaad a directory
	 */
	private static function read_dir($root, $dir) {
		if (substr($dir, -1) != '/') {
			$dir .= '/';
		}
		
		$source = $root . $dir;
		$arr_tmp = scandir($source);
		$ret = array();
		foreach($arr_tmp as $file) {
			if (substr($file, 0, 1) != '.') {
				$ret[] = $dir . $file;
			}
		}
		
		return $ret;
	}
}