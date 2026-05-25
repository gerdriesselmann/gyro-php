<?php
/**
 * Base class for CLI commands.
 *
 * @since 0.8
 * @ingroup CLI
 */
abstract class CLICommand {
	/**
	 * Return the command name (e.g. "model:list")
	 */
	abstract public function get_name(): string;

	/**
	 * Return a short description for the help listing
	 */
	abstract public function get_description(): string;

	/**
	 * Return detailed usage information
	 */
	public function get_usage(): string {
		return 'gyro ' . $this->get_name();
	}

	/**
	 * Execute the command
	 *
	 * @param array $args Parsed arguments
	 * @return int Exit code (0 = success)
	 */
	abstract public function execute(array $args): int;

	// -----------------------------------------------
	// Output helpers
	// -----------------------------------------------

	protected function writeln(string $text): void {
		echo $text . PHP_EOL;
	}

	protected function error(string $text): void {
		fwrite(STDERR, "\033[31mError:\033[0m $text" . PHP_EOL);
	}

	protected function success(string $text): void {
		echo "\033[32m$text\033[0m" . PHP_EOL;
	}

	protected function warning(string $text): void {
		echo "\033[33m$text\033[0m" . PHP_EOL;
	}

	protected function info(string $text): void {
		echo "\033[36m$text\033[0m" . PHP_EOL;
	}
}
