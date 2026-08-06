<?php
/**
 * model:show command - shows detailed schema for a specific model.
 *
 * Displays all fields with their types, defaults, constraints,
 * primary keys, and relations.
 *
 * Usage:
 *   gyro model:show users
 *   gyro model:show studentstest
 *
 * @since 0.8
 * @ingroup CLI
 */
class ModelShowCommand extends CLICommand {
	public function get_name(): string {
		return 'model:show';
	}

	public function get_description(): string {
		return 'Show detailed schema for a specific model';
	}

	public function get_usage(): string {
		return 'gyro model:show <table-name>';
	}

	public function execute(array $args): int {
		if (!isset($args[0])) {
			$this->error('Please specify a table name.');
			$this->writeln('Usage: ' . $this->get_usage());
			return 1;
		}

		$table_name = $args[0];

		// Find the model
		$models = ModelListCommand::discover_models();
		$found = null;
		foreach ($models as $info) {
			if ($info['table'] === $table_name || strtolower($info['class']) === 'dao' . strtolower($table_name)) {
				$found = $info;
				break;
			}
		}

		if ($found === null) {
			$this->error("Model '$table_name' not found.");
			$this->writeln('Run "gyro model:list" to see available models.');
			return 1;
		}

		$this->writeln('');
		$this->info($found['class'] . '  ->  ' . $found['table']);
		$this->writeln('Source: ' . $found['file']);
		$this->writeln('');

		// Fields table
		$this->writeln('Fields:');
		$table = new CLITable(array('Name', 'Type', 'Nullable', 'Default', 'Flags'));

		foreach ($found['fields'] as $name => $field) {
			$type = self::get_field_type_label($field);
			$nullable = $field->get_null_allowed() ? 'YES' : 'NO';
			$default = self::get_default_label($field);
			$flags = self::get_flags_label($field);
			$table->add_row(array($name, $type, $nullable, $default, $flags));
		}
		$table->print();

		// Primary Key
		if (!empty($found['keys'])) {
			$pk_names = array_keys($found['keys']);
			$this->writeln('Primary Key: ' . implode(', ', $pk_names));
		}

		// Relations
		if (!empty($found['relations'])) {
			$this->writeln('');
			$this->writeln('Relations:');
			$rel_table = new CLITable(array('Target Table', 'Type', 'Fields'));
			foreach ($found['relations'] as $relation) {
				$type_label = self::get_relation_type_label($relation->get_type());
				$field_pairs = array();
				foreach ($relation->get_fields() as $field_rel) {
					$field_pairs[] = $field_rel->get_source_field_name() . ' -> ' . $field_rel->get_target_field_name();
				}
				$rel_table->add_row(array(
					$relation->get_target_table_name(),
					$type_label,
					implode(', ', $field_pairs),
				));
			}
			$rel_table->print();
		}

		// SQL CREATE TABLE suggestion
		$this->writeln('');
		$this->writeln('SQL (CREATE TABLE):');
		$this->writeln(self::generate_create_sql($found));

		return 0;
	}

	/**
	 * Get a human-readable type label for a field
	 */
	public static function get_field_type_label(IDBField $field): string {
		$class = get_class($field);
		$type = match ($class) {
			'DBFieldInt' => 'INT',
			'DBFieldText' => 'VARCHAR(' . ($field instanceof DBFieldText ? $field->get_length() : 255) . ')',
			'DBFieldTextEmail' => 'VARCHAR (email)',
			'DBFieldFloat' => 'FLOAT',
			'DBFieldBool' => 'BOOL',
			'DBFieldDate' => 'DATE',
			'DBFieldDateTime' => 'DATETIME',
			'DBFieldTime' => 'TIME',
			'DBFieldBlob' => 'BLOB',
			'DBFieldEnum' => 'ENUM',
			'DBFieldSet' => 'SET',
			'DBFieldSerialized' => 'TEXT (serialized)',
			default => $class,
		};

		// Add UNSIGNED for int/float
		if (($field instanceof DBFieldInt || $field instanceof DBFieldFloat) && $field->has_policy(DBFieldInt::UNSIGNED)) {
			$type .= ' UNSIGNED';
		}

		// Add AUTO_INCREMENT
		if ($field instanceof DBFieldInt && $field->has_policy(DBFieldInt::AUTOINCREMENT)) {
			$type .= ' AUTO_INCREMENT';
		}

		// Add TIMESTAMP
		if ($field instanceof DBFieldDateTime && $field->has_policy(DBFieldDateTime::TIMESTAMP)) {
			$type = 'TIMESTAMP';
		}

		return $type;
	}

	/**
	 * Get default value label
	 */
	public static function get_default_label(IDBField $field): string {
		if ($field instanceof DBFieldInt && $field->has_policy(DBFieldInt::AUTOINCREMENT)) {
			return '(auto)';
		}

		if ($field instanceof DBFieldDateTime && $field->has_policy(DBFieldDateTime::TIMESTAMP)) {
			return 'CURRENT_TIMESTAMP';
		}

		$default = $field->get_field_default();
		if (is_null($default)) {
			return 'NULL';
		}
		if (is_bool($default)) {
			return $default ? 'TRUE' : 'FALSE';
		}
		return (string)$default;
	}

	/**
	 * Get flags label
	 */
	public static function get_flags_label(IDBField $field): string {
		$flags = array();

		if ($field->has_policy(DBField::INTERNAL)) {
			$flags[] = 'INTERNAL';
		}

		return implode(', ', $flags);
	}

	/**
	 * Get relation type label
	 */
	private static function get_relation_type_label(int $type): string {
		return match ($type) {
			DBRelation::ONE_TO_ONE => '1:1',
			DBRelation::ONE_TO_MANY => '1:N',
			DBRelation::MANY_TO_MANY => 'N:M',
			default => '?',
		};
	}

	/**
	 * Generate a CREATE TABLE SQL statement from model info
	 */
	public static function generate_create_sql(array $model_info): string {
		$table_name = $model_info['table'];
		$lines = array();

		foreach ($model_info['fields'] as $name => $field) {
			$lines[] = '  ' . self::field_to_sql($name, $field);
		}

		// Primary key
		if (!empty($model_info['keys'])) {
			$pk_names = array_keys($model_info['keys']);
			$lines[] = '  PRIMARY KEY (' . implode(', ', $pk_names) . ')';
		}

		$sql = "CREATE TABLE `$table_name` (\n";
		$sql .= implode(",\n", $lines);
		$sql .= "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";

		return $sql;
	}

	/**
	 * Convert a single field to SQL column definition
	 */
	public static function field_to_sql(string $name, IDBField $field): string {
		$class = get_class($field);

		$type = match ($class) {
			'DBFieldInt' => 'INT',
			'DBFieldText', 'DBFieldTextEmail' => 'VARCHAR(' . ($field instanceof DBFieldText ? $field->get_length() : 255) . ')',
			'DBFieldFloat' => 'DOUBLE',
			'DBFieldBool' => "ENUM('TRUE','FALSE')",
			'DBFieldDate' => 'DATE',
			'DBFieldDateTime' => 'DATETIME',
			'DBFieldTime' => 'TIME',
			'DBFieldBlob' => 'LONGBLOB',
			'DBFieldEnum' => 'ENUM',  // placeholder, handled below
			'DBFieldSet' => 'SET',    // placeholder, handled below
			'DBFieldSerialized' => 'TEXT',
			default => 'VARCHAR(255)',
		};

		// Handle text lengths for BLOB-like text
		if ($field instanceof DBFieldText) {
			$length = $field->get_length();
			if ($length > 65535) {
				$type = ($length > 16777215) ? 'LONGTEXT' : 'MEDIUMTEXT';
			} elseif ($length > 255) {
				$type = 'TEXT';
			}
		}

		// UNSIGNED for int/float
		if (($field instanceof DBFieldInt || $field instanceof DBFieldFloat) && $field->has_policy(DBFieldInt::UNSIGNED)) {
			$type .= ' UNSIGNED';
		}

		// NOT NULL
		$null = $field->get_null_allowed() ? 'NULL' : 'NOT NULL';

		// AUTO_INCREMENT
		$extra = '';
		if ($field instanceof DBFieldInt && $field->has_policy(DBFieldInt::AUTOINCREMENT)) {
			$extra = ' AUTO_INCREMENT';
		}

		// TIMESTAMP
		if ($field instanceof DBFieldDateTime && $field->has_policy(DBFieldDateTime::TIMESTAMP)) {
			$type = 'TIMESTAMP';
			$extra = ' DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP';
		}

		// Default
		$default_str = '';
		if (empty($extra)) {
			$default = $field->get_field_default();
			if ($default !== null && !($field instanceof DBFieldInt && $field->has_policy(DBFieldInt::AUTOINCREMENT))) {
				if (is_bool($default)) {
					$default_str = " DEFAULT '" . ($default ? 'TRUE' : 'FALSE') . "'";
				} elseif (is_int($default) || is_float($default)) {
					$default_str = " DEFAULT $default";
				} else {
					$default_str = " DEFAULT '" . addslashes((string)$default) . "'";
				}
			} elseif ($field->get_null_allowed() && $default === null) {
				$default_str = ' DEFAULT NULL';
			}
		}

		return "`$name` $type $null$default_str$extra";
	}
}
