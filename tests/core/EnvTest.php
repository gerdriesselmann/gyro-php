<?php
use PHPUnit\Framework\TestCase;

class EnvTest extends TestCase {
	private $tmp_file;

	protected function setUp(): void {
		$this->tmp_file = sys_get_temp_dir() . '/gyro_test_' . uniqid() . '.env';
		Env::reset();
	}

	protected function tearDown(): void {
		if (file_exists($this->tmp_file)) {
			unlink($this->tmp_file);
		}
		Env::reset();
	}

	public function test_load_simple_values() {
		file_put_contents($this->tmp_file, "TEST_KEY=hello\nTEST_NUM=42\n");
		$result = Env::load($this->tmp_file, false);

		$this->assertTrue($result);
		$this->assertTrue(Env::is_loaded());
		$this->assertEquals('hello', Env::get('TEST_KEY'));
		$this->assertEquals('42', Env::get('TEST_NUM'));
	}

	public function test_load_quoted_values() {
		file_put_contents($this->tmp_file, "DQ=\"double quoted\"\nSQ='single quoted'\n");
		Env::load($this->tmp_file, false);

		$this->assertEquals('double quoted', Env::get('DQ'));
		$this->assertEquals('single quoted', Env::get('SQ'));
	}

	public function test_skip_comments_and_empty_lines() {
		file_put_contents($this->tmp_file, "# This is a comment\n\nKEY=value\n# Another comment\n");
		Env::load($this->tmp_file, false);

		$this->assertNull(Env::get('# This is a comment'));
		$this->assertEquals('value', Env::get('KEY'));
	}

	public function test_default_value() {
		$this->assertEquals('fallback', Env::get('NONEXISTENT', 'fallback'));
		$this->assertNull(Env::get('NONEXISTENT'));
	}

	public function test_file_not_found() {
		$result = Env::load('/nonexistent/path/.env');
		$this->assertFalse($result);
		$this->assertFalse(Env::is_loaded());
	}

	public function test_get_all() {
		file_put_contents($this->tmp_file, "A=1\nB=2\n");
		Env::load($this->tmp_file, false);

		$all = Env::get_all();
		$this->assertEquals('1', $all['A']);
		$this->assertEquals('2', $all['B']);
	}

	public function test_reset() {
		file_put_contents($this->tmp_file, "ENVTEST_RESET_KEY=value\n");
		Env::load($this->tmp_file, false);
		$this->assertTrue(Env::is_loaded());

		Env::reset();
		$this->assertFalse(Env::is_loaded());
		$this->assertEmpty(Env::get_all());

		// Clean up env var
		putenv('ENVTEST_RESET_KEY');
	}

	public function test_cast_boolean_values() {
		// Test via define_constants=true with a unique constant name
		$unique = 'APP_ENVTEST_BOOL_' . strtoupper(uniqid());
		file_put_contents($this->tmp_file, "{$unique}=true\n");
		Env::load($this->tmp_file, true);

		// The constant should be boolean true
		$this->assertTrue(defined($unique));
		$this->assertSame(true, constant($unique));
	}

	public function test_cast_integer_values() {
		$unique = 'APP_ENVTEST_INT_' . strtoupper(uniqid());
		file_put_contents($this->tmp_file, "{$unique}=42\n");
		Env::load($this->tmp_file, true);

		$this->assertTrue(defined($unique));
		$this->assertSame(42, constant($unique));
	}

	public function test_value_with_equals_sign() {
		file_put_contents($this->tmp_file, "DSN=mysql:host=localhost;dbname=test\n");
		Env::load($this->tmp_file, false);

		$this->assertEquals('mysql:host=localhost;dbname=test', Env::get('DSN'));
	}

	public function test_no_overwrite_existing_constants() {
		// APP_TESTMODE is already defined in bootstrap
		file_put_contents($this->tmp_file, "APP_TESTMODE=false\n");
		$before = APP_TESTMODE;
		Env::load($this->tmp_file, true);

		// Should NOT have been overwritten
		$this->assertSame($before, APP_TESTMODE);
	}
}
