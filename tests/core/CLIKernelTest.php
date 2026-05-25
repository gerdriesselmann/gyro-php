<?php
use PHPUnit\Framework\TestCase;

require_once dirname(__DIR__, 2) . '/gyro/core/cli/clikernel.cls.php';
require_once dirname(__DIR__, 2) . '/gyro/core/cli/clicommand.cls.php';

class CLIKernelTest extends TestCase {
	public function test_parse_args_flags() {
		$result = CLIKernel::parse_args(array('--verbose', '--debug'));

		$this->assertTrue($result['verbose']);
		$this->assertTrue($result['debug']);
	}

	public function test_parse_args_key_value() {
		$result = CLIKernel::parse_args(array('--table=users', '--format=json'));

		$this->assertEquals('users', $result['table']);
		$this->assertEquals('json', $result['format']);
	}

	public function test_parse_args_positional() {
		$result = CLIKernel::parse_args(array('model:list', 'extra'));

		$this->assertEquals('model:list', $result[0]);
		$this->assertEquals('extra', $result[1]);
	}

	public function test_parse_args_mixed() {
		$result = CLIKernel::parse_args(array('users', '--verbose', '--format=table'));

		$this->assertEquals('users', $result[0]);
		$this->assertTrue($result['verbose']);
		$this->assertEquals('table', $result['format']);
	}

	public function test_parse_args_empty() {
		$result = CLIKernel::parse_args(array());

		$this->assertEmpty($result);
	}

	public function test_parse_args_equals_in_value() {
		$result = CLIKernel::parse_args(array('--query=a=b'));

		$this->assertEquals('a=b', $result['query']);
	}

	public function test_register_and_get_commands() {
		$kernel = new CLIKernel();
		$command = new class extends CLICommand {
			public function get_name(): string { return 'test:cmd'; }
			public function get_description(): string { return 'A test'; }
			public function execute(array $args): int { return 0; }
		};

		$kernel->register($command);
		$commands = $kernel->get_commands();

		$this->assertArrayHasKey('test:cmd', $commands);
		$this->assertSame($command, $commands['test:cmd']);
	}

	public function test_run_version() {
		$kernel = new CLIKernel();

		ob_start();
		$code = $kernel->run(array('gyro', '--version'));
		$output = ob_get_clean();

		$this->assertEquals(0, $code);
		$this->assertStringContainsString('Gyro CLI', $output);
	}

	public function test_run_unknown_command() {
		$kernel = new CLIKernel();

		ob_start();
		$code = $kernel->run(array('gyro', 'nonexistent'));
		ob_end_clean();

		$this->assertEquals(1, $code);
	}
}
