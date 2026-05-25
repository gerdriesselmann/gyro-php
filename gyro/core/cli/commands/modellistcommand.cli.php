<?php
/**
 * model:list command - discovers and lists all DAO models in the project.
 *
 * Scans core, modules, and contributions for .model.php files,
 * instantiates each DAO, and displays table name, fields, and keys.
 *
 * Usage:
 *   gyro model:list              List all models
 *   gyro model:list --verbose    Show field count and primary key
 *
 * @since 0.8
 * @ingroup CLI
 */
class ModelListCommand extends CLICommand {
	public function get_name(): string {
		return 'model:list';
	}

	public function get_description(): string {
		return 'List all DAO models and their database tables';
	}

	public function get_usage(): string {
		return 'gyro model:list [--verbose]';
	}

	public function execute(array $args): int {
		$verbose = !empty($args['verbose']);

		$models = self::discover_models();

		if (empty($models)) {
			$this->warning('No models found.');
			return 0;
		}

		if ($verbose) {
			$table = new CLITable(array('Class', 'Table', 'Fields', 'Primary Key', 'Relations', 'Source'));
		} else {
			$table = new CLITable(array('Class', 'Table', 'Fields', 'Primary Key'));
		}

		$count = 0;
		foreach ($models as $info) {
			$count++;
			if ($verbose) {
				$table->add_row(array(
					$info['class'],
					$info['table'],
					$info['field_count'],
					$info['primary_key'],
					$info['relation_count'],
					$info['source'],
				));
			} else {
				$table->add_row(array(
					$info['class'],
					$info['table'],
					$info['field_count'],
					$info['primary_key'],
				));
			}
		}

		$table->print();
		$this->writeln("$count model(s) found.");

		return 0;
	}

	/**
	 * Discover all DAO model files and extract schema info.
	 *
	 * @return array Array of model info arrays
	 */
	public static function discover_models(): array {
		$models = array();
		$model_files = self::find_model_files();

		foreach ($model_files as $file) {
			$info = self::load_model_info($file);
			if ($info !== false) {
				$models[] = $info;
			}
		}

		// Sort by table name
		usort($models, function ($a, $b) {
			return strcmp($a['table'], $b['table']);
		});

		return $models;
	}

	/**
	 * Find all .model.php files in core, modules, and contributions
	 *
	 * @return array File paths
	 */
	public static function find_model_files(): array {
		$files = array();
		$search_dirs = array(
			GYRO_CORE_DIR . 'model/classes/',
		);

		// Add module directories
		foreach (Load::get_loaded_modules() as $module) {
			$dir = Load::get_module_dir($module);
			if ($dir !== false) {
				$search_dirs[] = $dir . 'model/classes/';
			}
		}

		foreach ($search_dirs as $dir) {
			if (is_dir($dir)) {
				foreach (glob($dir . '*.model.php') as $file) {
					$files[] = $file;
				}
			}
		}

		return $files;
	}

	/**
	 * Load a model file and extract schema information
	 *
	 * @param string $file Path to .model.php file
	 * @return array|false Model info or false on failure
	 */
	public static function load_model_info(string $file): array|false {
		$filename = basename($file, '.model.php');

		// Derive class name: DAO + CamelCase of filename
		$classname = 'DAO' . Load::filename_to_classname($filename);

		// Load the file
		$classes_before = get_declared_classes();
		try {
			require_once $file;
		} catch (\Exception $e) {
			return false;
		}

		// If derived classname doesn't exist, scan for new DAO* class from this file
		if (!class_exists($classname, false)) {
			$classes_after = get_declared_classes();
			$new_classes = array_diff($classes_after, $classes_before);
			foreach ($new_classes as $cls) {
				if (str_starts_with($cls, 'DAO') && is_subclass_of($cls, 'DataObjectBase')) {
					$classname = $cls;
					break;
				}
			}
		}

		if (!class_exists($classname, false)) {
			return false;
		}

		// Try to instantiate and read schema
		try {
			$dao = new $classname();

			if (!($dao instanceof DataObjectBase)) {
				return false;
			}

			/** @var DataObjectBase $dao */
			$fields = $dao->get_table_fields();
			$keys = $dao->get_table_keys();
			$relations = $dao->get_table_relations();
			$table_name = $dao->get_table_name();

			$pk_names = array();
			foreach ($keys as $name => $field) {
				$pk_names[] = $name;
			}

			// Determine source (core, module, or contribution)
			$source = 'core';
			if (str_contains($file, '/contributions/')) {
				$source = self::extract_module_name($file, 'contributions');
			} elseif (str_contains($file, '/modules/')) {
				$source = self::extract_module_name($file, 'modules');
			}

			return array(
				'class' => $classname,
				'table' => $table_name,
				'field_count' => count($fields),
				'primary_key' => implode(', ', $pk_names),
				'relation_count' => count($relations),
				'fields' => $fields,
				'keys' => $keys,
				'relations' => $relations,
				'source' => $source,
				'file' => $file,
			);
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Extract module name from file path
	 */
	private static function extract_module_name(string $file, string $dir_name): string {
		$pattern = '/' . preg_quote($dir_name, '/') . '\/([^\/]+)\//';
		if (preg_match($pattern, $file, $matches)) {
			return $dir_name . '/' . $matches[1];
		}
		return $dir_name;
	}
}
