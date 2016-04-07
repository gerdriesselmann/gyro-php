<?php
/**
 * Logs stuffs
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Logger {
	/**
	 * Log $data to $file
	 * 
	 * @param string $file
	 * @param array $data
	 */
	public static function log($file, $data) {
		$file_name = Config::get_value(Config::LOG_FILE_NAME_PATTERN);
		$file_name = str_replace('%date%', date('Y-m-d', time()), $file_name);
		$file_name = str_replace('%name%', $file, $file_name);
		$file_path = Config::get_value(Config::LOG_DIR) . $file_name;
		$handle = @fopen($file_path, 'a');
		if ($handle) 	{
			$log = array_merge(array(date('Y/m/d, H:i:s', time()),	Url::current()->build()), Arr::force($data));
			@fputcsv($handle, $log, ';');
			@fclose($handle);
		}		
	}
}