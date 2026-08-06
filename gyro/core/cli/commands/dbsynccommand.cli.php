<?php
/**
 * db:sync command - compares model schema with actual database and generates ALTER TABLE SQL.
 *
 * Reads schema from create_table_object() in each DAO, queries
 * INFORMATION_SCHEMA to get the current DB state, and outputs
 * the SQL statements needed to bring the DB in sync.
 *
 * Usage:
 *   gyro db:sync                Show diff and SQL for all models
 *   gyro db:sync --dry-run      Show SQL without executing (default)
 *   gyro db:sync --execute      Actually run the ALTER TABLE statements
 *   gyro db:sync --table=users  Only check a specific table
 *
 * @since 0.8
 * @ingroup CLI
 */
class DBSyncCommand extends CLICommand {
	public function get_name(): string {
		return 'db:sync';
	}

	public function get_description(): string {
		return 'Compare model schema with database and generate ALTER TABLE SQL';
	}

	public function get_usage(): string {
		return "gyro db:sync [--dry-run] [--execute] [--table=<name>]";
	}

	public function execute(array $args): int {
		$execute = !empty($args['execute']);
		$target_table = $args['table'] ?? null;

		// Check DB connection
		try {
			$driver = DB::get_connection(DB::DEFAULT_CONNECTION);
		} catch (\Exception $e) {
			$this->error('No database connection available.');
			$this->writeln('Configure APP_DB_* constants in .env or your config.');
			return 1;
		}

		$models = ModelListCommand::discover_models();

		if (empty($models)) {
			$this->warning('No models found.');
			return 0;
		}

		// Filter to specific table if requested
		if ($target_table !== null) {
			$models = array_filter($models, function ($m) use ($target_table) {
				return $m['table'] === $target_table;
			});
			if (empty($models)) {
				$this->error("Table '$target_table' not found in models.");
				return 1;
			}
		}

		$total_statements = array();
		$new_tables = array();
		$altered_tables = array();

		foreach ($models as $model) {
			$table_name = $model['table'];
			$db_columns = $this->get_db_columns($table_name);

			if ($db_columns === false) {
				// Table doesn't exist — generate CREATE TABLE
				$sql = ModelShowCommand::generate_create_sql($model);
				$new_tables[] = $table_name;
				$total_statements[] = $sql;
				continue;
			}

			// Table exists — compare columns
			$alter_parts = $this->compare_table($model, $db_columns);

			if (!empty($alter_parts)) {
				$altered_tables[] = $table_name;
				foreach ($alter_parts as $part) {
					$total_statements[] = "ALTER TABLE `$table_name` $part;";
				}
			}
		}

		if (empty($total_statements)) {
			$this->success('Database is in sync with models. No changes needed.');
			return 0;
		}

		// Report
		$this->writeln('');
		if (!empty($new_tables)) {
			$this->info('New tables: ' . implode(', ', $new_tables));
		}
		if (!empty($altered_tables)) {
			$this->info('Tables to alter: ' . implode(', ', $altered_tables));
		}
		$this->writeln('');

		$this->writeln('Generated SQL:');
		$this->writeln(str_repeat('-', 60));
		foreach ($total_statements as $sql) {
			$this->writeln($sql);
			$this->writeln('');
		}
		$this->writeln(str_repeat('-', 60));
		$this->writeln(count($total_statements) . ' statement(s).');

		if ($execute) {
			$this->writeln('');
			$this->warning('Executing...');
			$errors = 0;
			foreach ($total_statements as $sql) {
				$status = DB::execute($sql);
				if ($status->is_error()) {
					$this->error('Failed: ' . $status->to_string(Status::OUTPUT_PLAIN));
					$this->writeln("  SQL: $sql");
					$errors++;
				}
			}
			if ($errors === 0) {
				$this->success('All statements executed successfully.');
			} else {
				$this->error("$errors statement(s) failed.");
				return 1;
			}
		} else {
			$this->writeln('');
			$this->writeln('This is a dry run. Use --execute to apply changes.');
		}

		return 0;
	}

	/**
	 * Get columns from the actual database for a table
	 *
	 * @return array|false Array of column info, or false if table doesn't exist
	 */
	private function get_db_columns(string $table_name): array|false {
		try {
			$result = DB::query("SHOW COLUMNS FROM `$table_name`");
			if ($result->get_status()->is_error()) {
				return false;
			}

			$columns = array();
			while ($row = $result->fetch()) {
				$columns[$row['Field']] = array(
					'type' => $row['Type'],
					'null' => $row['Null'] === 'YES',
					'default' => $row['Default'],
					'extra' => $row['Extra'] ?? '',
					'key' => $row['Key'] ?? '',
				);
			}

			return empty($columns) ? false : $columns;
		} catch (\Exception $e) {
			return false;
		}
	}

	/**
	 * Compare model fields with DB columns and return ALTER TABLE parts
	 *
	 * @return array Array of ALTER TABLE clause strings (without the ALTER TABLE prefix)
	 */
	private function compare_table(array $model, array $db_columns): array {
		$alter_parts = array();
		$prev_column = null;

		foreach ($model['fields'] as $name => $field) {
			$col_sql = ModelShowCommand::field_to_sql($name, $field);

			if (!isset($db_columns[$name])) {
				// Column missing — ADD
				$position = ($prev_column !== null) ? " AFTER `$prev_column`" : ' FIRST';
				$alter_parts[] = "ADD COLUMN $col_sql$position";
			} else {
				// Column exists — check if it differs
				$diff = $this->column_differs($field, $db_columns[$name]);
				if ($diff) {
					$alter_parts[] = "MODIFY COLUMN $col_sql";
				}
			}
			$prev_column = $name;
		}

		// Check for columns in DB that are NOT in the model
		foreach ($db_columns as $col_name => $col_info) {
			if (!isset($model['fields'][$col_name])) {
				// Don't auto-drop — too dangerous. Just warn.
				$alter_parts[] = "-- WARNING: Column `$col_name` exists in DB but not in model (not auto-dropped)";
			}
		}

		return $alter_parts;
	}

	/**
	 * Check if a model field definition differs from the DB column
	 */
	private function column_differs(IDBField $field, array $db_col): bool {
		$db_type = strtoupper($db_col['type']);
		$db_null = $db_col['null'];
		$db_extra = strtoupper($db_col['extra'] ?? '');

		// Check nullability
		$model_null = $field->get_null_allowed();
		if ($model_null !== $db_null) {
			return true;
		}

		// Check auto_increment
		if ($field instanceof DBFieldInt && $field->has_policy(DBFieldInt::AUTOINCREMENT)) {
			if (!str_contains($db_extra, 'AUTO_INCREMENT')) {
				return true;
			}
		}

		// Check unsigned
		if (($field instanceof DBFieldInt || $field instanceof DBFieldFloat) && $field->has_policy(DBFieldInt::UNSIGNED)) {
			if (!str_contains($db_type, 'UNSIGNED')) {
				return true;
			}
		}

		// Basic type matching (simplified — exact type comparison is complex)
		$expected_base = $this->get_expected_base_type($field);
		if ($expected_base !== null && !str_contains($db_type, $expected_base)) {
			return true;
		}

		return false;
	}

	/**
	 * Get expected base SQL type for a field
	 */
	private function get_expected_base_type(IDBField $field): ?string {
		$class = get_class($field);

		if ($field instanceof DBFieldDateTime && $field->has_policy(DBFieldDateTime::TIMESTAMP)) {
			return 'TIMESTAMP';
		}

		return match ($class) {
			'DBFieldInt' => 'INT',
			'DBFieldFloat' => 'DOUBLE',
			'DBFieldBool' => 'ENUM',
			'DBFieldDate' => 'DATE',
			'DBFieldDateTime' => 'DATETIME',
			'DBFieldTime' => 'TIME',
			'DBFieldBlob' => 'BLOB',
			'DBFieldSerialized' => 'TEXT',
			default => null,  // Text/Enum/Set — too many variations
		};
	}
}
