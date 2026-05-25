<?php
/**
 * Help command - shows available commands or help for a specific command.
 *
 * Usage:
 *   gyro help              List all commands
 *   gyro help model:list   Show help for model:list
 *
 * @since 0.8
 * @ingroup CLI
 */
class HelpCommand extends CLICommand {
	private CLIKernel $kernel;

	public function __construct(CLIKernel $kernel) {
		$this->kernel = $kernel;
	}

	public function get_name(): string {
		return 'help';
	}

	public function get_description(): string {
		return 'Show available commands or help for a specific command';
	}

	public function get_usage(): string {
		return 'gyro help [command]';
	}

	public function execute(array $args): int {
		// Help for a specific command?
		if (isset($args[0])) {
			return $this->show_command_help($args[0]);
		}

		$this->writeln('');
		$this->info('Gyro CLI ' . CLIKernel::VERSION);
		$this->writeln('');
		$this->writeln('Usage: gyro <command> [options]');
		$this->writeln('');
		$this->writeln('Available commands:');
		$this->writeln('');

		$table = new CLITable(array('Command', 'Description'));
		foreach ($this->kernel->get_commands() as $command) {
			$table->add_row(array($command->get_name(), $command->get_description()));
		}
		$table->print();

		$this->writeln('');
		$this->writeln("Run 'gyro help <command>' for more details.");
		$this->writeln('');

		return 0;
	}

	private function show_command_help(string $name): int {
		$commands = $this->kernel->get_commands();

		if (!isset($commands[$name])) {
			$this->error("Unknown command: $name");
			return 1;
		}

		$command = $commands[$name];
		$this->writeln('');
		$this->info($command->get_name());
		$this->writeln('  ' . $command->get_description());
		$this->writeln('');
		$this->writeln('Usage:');
		$this->writeln('  ' . $command->get_usage());
		$this->writeln('');

		return 0;
	}
}
