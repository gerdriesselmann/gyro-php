<?php
/**
 * Structured Logger with PSR-3 compatible log levels
 *
 * Supports log levels: emergency, alert, critical, error, warning, notice, info, debug.
 * Context interpolation replaces {key} placeholders in messages.
 * Output format: CSV (backwards compatible) or JSON (structured).
 *
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Logger {
	const EMERGENCY = 'emergency';
	const ALERT     = 'alert';
	const CRITICAL  = 'critical';
	const ERROR     = 'error';
	const WARNING   = 'warning';
	const NOTICE    = 'notice';
	const INFO      = 'info';
	const DEBUG     = 'debug';

	/**
	 * Ordered log levels (highest severity first)
	 */
	private static $levels = array(
		self::EMERGENCY => 0,
		self::ALERT     => 1,
		self::CRITICAL  => 2,
		self::ERROR     => 3,
		self::WARNING   => 4,
		self::NOTICE    => 5,
		self::INFO      => 6,
		self::DEBUG     => 7,
	);

	/**
	 * Minimum log level. Messages below this level are discarded.
	 * Default: DEBUG (log everything)
	 *
	 * @var string
	 */
	private static $min_level = self::DEBUG;

	/**
	 * Set minimum log level
	 *
	 * @param string $level One of the Logger level constants
	 */
	public static function set_min_level(string $level): void {
		if (isset(self::$levels[$level])) {
			self::$min_level = $level;
		}
	}

	/**
	 * Legacy log method - backwards compatible
	 *
	 * @param string $file Log file name
	 * @param array $data Data to log
	 */
	public static function log(string $file, $data): void {
		$file_name = Config::get_value(Config::LOG_FILE_NAME_PATTERN);
		$file_name = str_replace('%date%', date('Y-m-d', time()), $file_name);
		$file_name = str_replace('%name%', $file, $file_name);
		$file_path = Config::get_value(Config::LOG_DIR) . $file_name;
		$handle = @fopen($file_path, 'a');
		if ($handle) {
			$log = array_merge(array(date('Y/m/d, H:i:s', time()), Url::current()->build()), Arr::force($data));
			@fputcsv($handle, $log, ';');
			@fclose($handle);
		}
	}

	/**
	 * Log with an arbitrary level
	 *
	 * @param string $level Log level
	 * @param string $message Message with optional {placeholder} tokens
	 * @param array $context Key-value pairs to interpolate into message and attach as metadata
	 */
	public static function log_level(string $level, string $message, array $context = array()): void {
		if (!self::should_log($level)) {
			return;
		}

		$interpolated = self::interpolate($message, $context);

		$entry = array(
			'timestamp' => date('c'),
			'level'     => $level,
			'message'   => $interpolated,
		);

		if (!empty($context)) {
			// Add exception info if present
			if (isset($context['exception']) && $context['exception'] instanceof \Throwable) {
				$ex = $context['exception'];
				$entry['exception'] = array(
					'class'   => get_class($ex),
					'message' => $ex->getMessage(),
					'code'    => $ex->getCode(),
					'file'    => $ex->getFile(),
					'line'    => $ex->getLine(),
					'trace'   => $ex->getTraceAsString(),
				);
				unset($context['exception']);
			}
			if (!empty($context)) {
				$entry['context'] = $context;
			}
		}

		self::write_entry($level, $entry);
	}

	public static function emergency(string $message, array $context = array()): void {
		self::log_level(self::EMERGENCY, $message, $context);
	}

	public static function alert(string $message, array $context = array()): void {
		self::log_level(self::ALERT, $message, $context);
	}

	public static function critical(string $message, array $context = array()): void {
		self::log_level(self::CRITICAL, $message, $context);
	}

	public static function error(string $message, array $context = array()): void {
		self::log_level(self::ERROR, $message, $context);
	}

	public static function warning(string $message, array $context = array()): void {
		self::log_level(self::WARNING, $message, $context);
	}

	public static function notice(string $message, array $context = array()): void {
		self::log_level(self::NOTICE, $message, $context);
	}

	public static function info(string $message, array $context = array()): void {
		self::log_level(self::INFO, $message, $context);
	}

	public static function debug(string $message, array $context = array()): void {
		self::log_level(self::DEBUG, $message, $context);
	}

	/**
	 * Check if a message at the given level should be logged
	 */
	private static function should_log(string $level): bool {
		$level_value = isset(self::$levels[$level]) ? self::$levels[$level] : self::$levels[self::DEBUG];
		$min_value = self::$levels[self::$min_level];
		return $level_value <= $min_value;
	}

	/**
	 * Interpolate {placeholder} tokens in message with context values
	 */
	private static function interpolate(string $message, array $context): string {
		$replace = array();
		foreach ($context as $key => $val) {
			if ($key === 'exception') {
				continue;
			}
			if (is_string($val) || is_numeric($val) || (is_object($val) && method_exists($val, '__toString'))) {
				$replace['{' . $key . '}'] = (string)$val;
			}
		}
		return strtr($message, $replace);
	}

	/**
	 * Write a structured log entry as JSON
	 */
	private static function write_entry(string $level, array $entry): void {
		$file_name = Config::get_value(Config::LOG_FILE_NAME_PATTERN);
		$file_name = str_replace('%date%', date('Y-m-d', time()), $file_name);
		$file_name = str_replace('%name%', $level, $file_name);
		$file_path = Config::get_value(Config::LOG_DIR) . $file_name;
		$handle = @fopen($file_path, 'a');
		if ($handle) {
			@fwrite($handle, json_encode($entry, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n");
			@fclose($handle);
		}
	}
}
