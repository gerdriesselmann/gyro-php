<?php
use PHPUnit\Framework\TestCase;

// CLI classes
require_once dirname(__DIR__, 2) . '/gyro/core/cli/clicommand.cls.php';
require_once dirname(__DIR__, 2) . '/gyro/core/cli/clitable.cls.php';
require_once dirname(__DIR__, 2) . '/gyro/core/cli/commands/modellistcommand.cli.php';
require_once dirname(__DIR__, 2) . '/gyro/core/cli/commands/modelshowcommand.cli.php';

class ModelShowCommandTest extends TestCase {
	public function test_field_type_label_int() {
		$field = new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL);
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertStringContainsString('INT', $label);
		$this->assertStringContainsString('UNSIGNED', $label);
		$this->assertStringContainsString('AUTO_INCREMENT', $label);
	}

	public function test_field_type_label_varchar() {
		$field = new DBFieldText('name', 100, null, DBField::NOT_NULL);
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertEquals('VARCHAR(100)', $label);
	}

	public function test_field_type_label_text() {
		$field = new DBFieldText('body', DBFieldText::BLOB_LENGTH_SMALL, null);
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertEquals('VARCHAR(65535)', $label);
	}

	public function test_field_type_label_bool() {
		$field = new DBFieldBool('active', false);
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertEquals('BOOL', $label);
	}

	public function test_field_type_label_datetime() {
		$field = new DBFieldDateTime('created');
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertEquals('DATETIME', $label);
	}

	public function test_field_type_label_timestamp() {
		$field = new DBFieldDateTime('modified', null, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL);
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertEquals('TIMESTAMP', $label);
	}

	public function test_field_type_label_float_unsigned() {
		$field = new DBFieldFloat('price', 0, DBFieldFloat::UNSIGNED | DBField::NOT_NULL);
		$label = ModelShowCommand::get_field_type_label($field);

		$this->assertStringContainsString('FLOAT', $label);
		$this->assertStringContainsString('UNSIGNED', $label);
	}

	public function test_default_label_auto_increment() {
		$field = new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT);
		$label = ModelShowCommand::get_default_label($field);

		$this->assertEquals('(auto)', $label);
	}

	public function test_default_label_string() {
		$field = new DBFieldText('status', 20, 'active', DBField::NOT_NULL);
		$label = ModelShowCommand::get_default_label($field);

		$this->assertEquals('active', $label);
	}

	public function test_default_label_null() {
		$field = new DBFieldText('note', 255, null);
		$label = ModelShowCommand::get_default_label($field);

		$this->assertEquals('NULL', $label);
	}

	public function test_default_label_bool() {
		$field = new DBFieldBool('active', true);
		$label = ModelShowCommand::get_default_label($field);

		$this->assertEquals('TRUE', $label);
	}

	public function test_field_to_sql_int_primary_key() {
		$field = new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL);
		$sql = ModelShowCommand::field_to_sql('id', $field);

		$this->assertStringContainsString('`id`', $sql);
		$this->assertStringContainsString('INT', $sql);
		$this->assertStringContainsString('UNSIGNED', $sql);
		$this->assertStringContainsString('NOT NULL', $sql);
		$this->assertStringContainsString('AUTO_INCREMENT', $sql);
	}

	public function test_field_to_sql_varchar() {
		$field = new DBFieldText('email', 100, null, DBField::NOT_NULL);
		$sql = ModelShowCommand::field_to_sql('email', $field);

		$this->assertStringContainsString('`email`', $sql);
		$this->assertStringContainsString('VARCHAR(100)', $sql);
		$this->assertStringContainsString('NOT NULL', $sql);
	}

	public function test_field_to_sql_nullable_with_default() {
		$field = new DBFieldText('note', 255, null, DBField::NONE);
		$sql = ModelShowCommand::field_to_sql('note', $field);

		$this->assertStringContainsString('NULL', $sql);
		$this->assertStringContainsString('DEFAULT NULL', $sql);
	}

	public function test_field_to_sql_timestamp() {
		$field = new DBFieldDateTime('modificationdate', null, DBFieldDateTime::TIMESTAMP | DBField::NOT_NULL);
		$sql = ModelShowCommand::field_to_sql('modificationdate', $field);

		$this->assertStringContainsString('TIMESTAMP', $sql);
		$this->assertStringContainsString('CURRENT_TIMESTAMP', $sql);
	}

	public function test_generate_create_sql() {
		$fields = array(
			'id' => new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBField::NOT_NULL),
			'name' => new DBFieldText('name', 40, null, DBField::NOT_NULL),
		);
		$keys = array('id' => $fields['id']);

		$model_info = array(
			'table' => 'test_table',
			'fields' => $fields,
			'keys' => $keys,
		);

		$sql = ModelShowCommand::generate_create_sql($model_info);

		$this->assertStringContainsString('CREATE TABLE `test_table`', $sql);
		$this->assertStringContainsString('`id`', $sql);
		$this->assertStringContainsString('`name`', $sql);
		$this->assertStringContainsString('PRIMARY KEY (id)', $sql);
		$this->assertStringContainsString('ENGINE=InnoDB', $sql);
	}

	public function test_flags_label_internal() {
		$field = new DBFieldText('hash_type', 5, 'bcryp', DBField::NOT_NULL | DBField::INTERNAL);
		$label = ModelShowCommand::get_flags_label($field);

		$this->assertEquals('INTERNAL', $label);
	}

	public function test_flags_label_empty() {
		$field = new DBFieldText('name', 100, null, DBField::NOT_NULL);
		$label = ModelShowCommand::get_flags_label($field);

		$this->assertEquals('', $label);
	}

	public function test_discover_models_finds_core_models() {
		// The test bootstrap loads core model classes (cache, formvalidations, sessions)
		$models = ModelListCommand::discover_models();

		$this->assertNotEmpty($models, 'Should find at least one model');

		// All discovered models must have required keys
		foreach ($models as $model) {
			$this->assertArrayHasKey('class', $model);
			$this->assertArrayHasKey('table', $model);
			$this->assertArrayHasKey('field_count', $model);
			$this->assertArrayHasKey('primary_key', $model);
			$this->assertGreaterThan(0, $model['field_count']);
		}
	}

	public function test_load_model_info_from_file() {
		$file = GYRO_ROOT_DIR . 'modules/simpletest/model/classes/studentstest.model.php';
		$info = ModelListCommand::load_model_info($file);

		$this->assertNotFalse($info);
		// Class name may be DAOStudentstest (derived) or DAOStudentsTest (actual)
		$this->assertStringStartsWith('daostudentstest', strtolower($info['class']));
		$this->assertEquals('studentstest', $info['table']);
		$this->assertEquals(3, $info['field_count']);
		$this->assertEquals('id', $info['primary_key']);
	}
}
