<?php
/**
 * Simple ASCII table renderer for CLI output.
 *
 * @since 0.8
 * @ingroup CLI
 */
class CLITable {
	/** @var string[] */
	private array $headers;

	/** @var array[] */
	private array $rows = array();

	/**
	 * @param string[] $headers Column headers
	 */
	public function __construct(array $headers) {
		$this->headers = $headers;
	}

	/**
	 * Add a row of data
	 *
	 * @param array $row Values in same order as headers
	 */
	public function add_row(array $row): void {
		$this->rows[] = array_values($row);
	}

	/**
	 * Render the table to string
	 */
	public function render(): string {
		$col_count = count($this->headers);
		$widths = array();

		// Calculate column widths
		for ($i = 0; $i < $col_count; $i++) {
			$widths[$i] = mb_strlen($this->headers[$i]);
		}
		foreach ($this->rows as $row) {
			for ($i = 0; $i < $col_count; $i++) {
				$val = $row[$i] ?? '';
				$widths[$i] = max($widths[$i], mb_strlen((string)$val));
			}
		}

		$lines = array();

		// Separator
		$sep = '+';
		for ($i = 0; $i < $col_count; $i++) {
			$sep .= str_repeat('-', $widths[$i] + 2) . '+';
		}

		$lines[] = $sep;

		// Header row
		$line = '|';
		for ($i = 0; $i < $col_count; $i++) {
			$line .= ' ' . str_pad($this->headers[$i], $widths[$i]) . ' |';
		}
		$lines[] = $line;
		$lines[] = $sep;

		// Data rows
		foreach ($this->rows as $row) {
			$line = '|';
			for ($i = 0; $i < $col_count; $i++) {
				$val = (string)($row[$i] ?? '');
				$line .= ' ' . str_pad($val, $widths[$i]) . ' |';
			}
			$lines[] = $line;
		}

		$lines[] = $sep;

		return implode(PHP_EOL, $lines);
	}

	/**
	 * Print the table to stdout
	 */
	public function print(): void {
		echo $this->render() . PHP_EOL;
	}
}
