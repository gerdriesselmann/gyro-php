<?php
use PHPUnit\Framework\TestCase;

class DBTest extends TestCase {
	private string $file;

	protected function setUp(): void {
		$this->file = sys_get_temp_dir() . '/gyro_dbtest.sql';
	}

	protected function tearDown(): void {
		if (file_exists($this->file)) {
			unlink($this->file);
		}
	}

	public function test_extract_next_sql_statement() {
		$content = "
			SELECT * FROM test;
			-- A comment line;
			Select '--', /* A comment */ \"#\" FROM test;
			/*
			This should be ignored;
			 */
			SELECT '\\'' FROM TEST;
			INSERT ME
		";
		file_put_contents($this->file, $content);
		$handle = fopen($this->file, 'r');

		$this->assertEquals('SELECT * FROM test;', DB::extract_next_sql_statement($handle));
		$this->assertEquals("Select '--',  \"#\" FROM test;", DB::extract_next_sql_statement($handle));
		$this->assertEquals("SELECT '\\'' FROM TEST;", DB::extract_next_sql_statement($handle));
		$this->assertEquals('INSERT ME', DB::extract_next_sql_statement($handle));

		fclose($handle);
	}
}
