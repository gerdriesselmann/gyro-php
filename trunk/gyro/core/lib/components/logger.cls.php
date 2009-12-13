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
		$file = Config::get_value(Config::TEMP_DIR) . 'log/' . date('Y-m-d', time()) . '_' . $file . '.log';
		$handle = @fopen($file, 'a');
		if ($handle) 	{
	 		$log = array_merge(array(date('Y/m/d, H:i:s', time()),	Url::current()->build()), Arr::force($data));
			@fwrite($handle, '"' . implode('";"', $log) . "\"\n");
			@fclose($handle);
		}		
	}
}