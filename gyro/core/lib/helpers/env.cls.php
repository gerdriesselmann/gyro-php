<?php
/**
 * Simple .env file loader for Gyro-PHP
 *
 * Loads environment variables from a .env file and optionally defines
 * them as PHP constants (for backwards compatibility with APP_* constants).
 *
 * Usage:
 *   Env::load(APP_INCLUDE_ABSPATH . '.env');
 *   $value = Env::get('DB_HOST', '127.0.0.1');
 *
 * The .env file format supports:
 *   - KEY=value
 *   - KEY="quoted value"
 *   - KEY='single quoted value'
 *   - # comments
 *   - Empty lines (ignored)
 *   - APP_* keys are auto-defined as PHP constants
 *
 * @author Gyro-PHP
 * @ingroup Helpers
 */
class Env {
	/**
	 * Loaded environment values
	 *
	 * @var array
	 */
	private static $values = array();

	/**
	 * Whether a .env file has been loaded
	 *
	 * @var bool
	 */
	private static $loaded = false;

	/**
	 * Load a .env file
	 *
	 * Values are stored internally and optionally exported as PHP constants.
	 * Existing constants and environment variables are NOT overwritten.
	 *
	 * @param string $path Path to .env file
	 * @param bool $define_constants If true, APP_* keys are defined as PHP constants
	 * @return bool True if file was loaded, false if file not found
	 */
	public static function load($path, $define_constants = true) {
		if (!is_file($path) || !is_readable($path)) {
			return false;
		}

		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		if ($lines === false) {
			return false;
		}

		foreach ($lines as $line) {
			$line = trim($line);

			// Skip comments and empty lines
			if ($line === '' || $line[0] === '#') {
				continue;
			}

			// Parse KEY=VALUE
			$pos = strpos($line, '=');
			if ($pos === false) {
				continue;
			}

			$key = trim(substr($line, 0, $pos));
			$value = trim(substr($line, $pos + 1));

			if ($key === '') {
				continue;
			}

			// Remove quotes
			$value = self::unquote($value);

			// Store internally
			self::$values[$key] = $value;

			// Set as environment variable (don't overwrite existing)
			if (getenv($key) === false) {
				putenv($key . '=' . $value);
			}

			// Define as PHP constant if it starts with APP_ (don't overwrite existing)
			if ($define_constants && strpos($key, 'APP_') === 0 && !defined($key)) {
				define($key, self::cast_value($value));
			}
		}

		self::$loaded = true;
		return true;
	}

	/**
	 * Get an environment value
	 *
	 * Checks in order: loaded .env values, environment variables, default.
	 *
	 * @param string $key
	 * @param mixed $default
	 * @return mixed
	 */
	public static function get($key, $default = null) {
		// Check loaded values first
		if (isset(self::$values[$key])) {
			return self::$values[$key];
		}

		// Check environment variables
		$env = getenv($key);
		if ($env !== false) {
			return $env;
		}

		return $default;
	}

	/**
	 * Check if .env file has been loaded
	 *
	 * @return bool
	 */
	public static function is_loaded() {
		return self::$loaded;
	}

	/**
	 * Get all loaded values
	 *
	 * @return array
	 */
	public static function get_all() {
		return self::$values;
	}

	/**
	 * Reset state (for testing)
	 */
	public static function reset() {
		self::$values = array();
		self::$loaded = false;
	}

	/**
	 * Remove surrounding quotes from a value
	 *
	 * @param string $value
	 * @return string
	 */
	private static function unquote($value) {
		$len = strlen($value);
		if ($len >= 2) {
			$first = $value[0];
			$last = $value[$len - 1];
			if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
				return substr($value, 1, $len - 2);
			}
		}
		return $value;
	}

	/**
	 * Cast string value to appropriate PHP type
	 *
	 * Handles: true, false, null, integers, floats
	 *
	 * @param string $value
	 * @return mixed
	 */
	private static function cast_value($value) {
		$lower = strtolower($value);

		if ($lower === 'true') {
			return true;
		}
		if ($lower === 'false') {
			return false;
		}
		if ($lower === 'null' || $lower === '') {
			return '';
		}

		// Integer
		if (ctype_digit($value) || ($value !== '' && $value[0] === '-' && ctype_digit(substr($value, 1)))) {
			return (int)$value;
		}

		// Float
		if (is_numeric($value) && strpos($value, '.') !== false) {
			return (float)$value;
		}

		return $value;
	}
}
