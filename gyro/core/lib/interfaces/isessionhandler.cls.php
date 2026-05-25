<?php
/**
 * A class to handle session storage and retrieval
 */
interface ISessionHandler {
	/**
	 * Open a session
	 */
	public function open(string $save_path, string $session_name): bool;

	/**
	 * Close a session
	 */
	public function close(): bool;

	/**
	 * Load session data
	 */
	public function read(string $key): string|false;

	/**
	 * Write session data
	 */
	public function write(string $key, string $value): bool;

	/**
	 * Delete a session
	 */
	public function destroy(string $key): bool;

	/**
	 * Delete outdated sessions
	 */
	public function gc(int $lifetime): int|false;
}
