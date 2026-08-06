<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/gyro/core/cli/clitable.cls.php';

class CLITableTest extends TestCase {
	public function test_render_basic_table() {
		$table = new CLITable(array('Name', 'Age'));
		$table->add_row(array('Alice', '30'));
		$table->add_row(array('Bob', '25'));

		$output = $table->render();

		$this->assertStringContainsString('Name', $output);
		$this->assertStringContainsString('Age', $output);
		$this->assertStringContainsString('Alice', $output);
		$this->assertStringContainsString('30', $output);
		$this->assertStringContainsString('Bob', $output);
		$this->assertStringContainsString('25', $output);
	}

	public function test_render_adjusts_column_widths() {
		$table = new CLITable(array('X', 'Y'));
		$table->add_row(array('LongValue', 'A'));

		$output = $table->render();
		$lines = explode(PHP_EOL, $output);

		// Separator line should accommodate the longest value
		$this->assertStringContainsString('-----------', $lines[0]);
	}

	public function test_render_empty_table() {
		$table = new CLITable(array('Col1', 'Col2'));
		$output = $table->render();

		// Should still have headers and separators
		$this->assertStringContainsString('Col1', $output);
		$this->assertStringContainsString('Col2', $output);
	}

	public function test_render_single_column() {
		$table = new CLITable(array('Items'));
		$table->add_row(array('one'));
		$table->add_row(array('two'));

		$output = $table->render();
		$this->assertStringContainsString('one', $output);
		$this->assertStringContainsString('two', $output);
	}
}
