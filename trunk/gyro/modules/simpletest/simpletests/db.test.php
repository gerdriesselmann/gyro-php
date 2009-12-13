<?php
class DBTest extends GyroUnitTestCase  {
	private $file;
	
	public function setUp() {
		$this->file = Config::get_value(Config::TEMP_DIR) . 'dbtest.sql';	
	}
	
	public function tearDown() {
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
		
		$this->assertEqual('SELECT * FROM test;', DB::extract_next_sql_statement($handle));
		$this->assertEqual("Select '--',  \"#\" FROM test;", DB::extract_next_sql_statement($handle));
		$this->assertEqual("SELECT '\\'' FROM TEST;", DB::extract_next_sql_statement($handle));
		$this->assertEqual('INSERT ME', DB::extract_next_sql_statement($handle));
		
		fclose($handle);
	}
}