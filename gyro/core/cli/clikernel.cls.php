<?php
/**
 * CLI Kernel - routes commands and manages the CLI lifecycle.
 *
 * @since 0.8
 * @ingroup CLI
 */
class CLIKernel {
	const VERSION = '0.8.0';

	/** @var CLICommand[] */
	private array $commands = array();

	/**
	 * Register a command
	 */
	public function register(CLICommand $command): void {
		$this->commands[$command->get_name()] = $command;
	}

	/**
	 * Get all registered commands
	 *
	 * @return CLICommand[]
	 */
	public function get_commands(): array {
		return $this->commands;
	}

	/**
	 * Run the CLI with given argv
	 *
	 * @param array $argv Command-line arguments
	 * @return int Exit code (0 = success)
	 */
	public function run(array $argv): int {
		$script = array_shift($argv); // Remove script name

		if (empty($argv) || in_array($argv[0], array('--help', '-h'))) {
			return $this->run_command('help', array());
		}

		if (in_array($argv[0], array('--version', '-v'))) {
			$this->writeln('Gyro CLI ' . self::VERSION);
			return 0;
		}

		$command_name = array_shift($argv);

		// Parse --flags and positional args
		$args = self::parse_args($argv);

		return $this->run_command($command_name, $args);
	}

	/**
	 * Run a specific command by name
	 */
	private function run_command(string $name, array $args): int {
		if (!isset($this->commands[$name])) {
			$this->error("Unknown command: $name");
			$this->writeln("Run 'gyro help' for a list of commands.");
			return 1;
		}

		try {
			return $this->commands[$name]->execute($args);
		} catch (\Exception $e) {
			$this->error($e->getMessage());
			return 1;
		}
	}

	/**
	 * Parse argv into associative array
	 *
	 * Supports: --key=value, --flag, positional args (numeric keys)
	 *
	 * @return array
	 */
	public static function parse_args(array $argv): array {
		$args = array();
		$positional = 0;

		foreach ($argv as $arg) {
			if (str_starts_with($arg, '--')) {
				$arg = substr($arg, 2);
				if (str_contains($arg, '=')) {
					list($key, $value) = explode('=', $arg, 2);
					$args[$key] = $value;
				} else {
					$args[$arg] = true;
				}
			} else {
				$args[$positional] = $arg;
				$positional++;
			}
		}

		return $args;
	}

	// -----------------------------------------------
	// Output helpers (static for use from commands)
	// -----------------------------------------------

	public function writeln(string $text): void {
		echo $text . PHP_EOL;
	}

	public function error(string $text): void {
		fwrite(STDERR, "\033[31mError:\033[0m $text" . PHP_EOL);
	}

	public function success(string $text): void {
		echo "\033[32m$text\033[0m" . PHP_EOL;
	}

	public function warning(string $text): void {
		echo "\033[33m$text\033[0m" . PHP_EOL;
	}

	public function info(string $text): void {
		echo "\033[36m$text\033[0m" . PHP_EOL;
	}
}
