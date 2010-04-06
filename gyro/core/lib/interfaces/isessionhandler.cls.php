<?php
/**
 * A class to handle session storage and retrieval
 */
interface ISessionHandler {
	/**
	 * Open a session
	 */ 
	public function open($save_path, $session_name);
	
	/**
	 * Close a session
	 */
	public function close();
	
	/**
	 * Load session data 
	 */
	public function read($key);
	
	/**
	 * Write session data to DB
	 */
	public function write($key, $value);
	
	/**
	 * Delete a session
	 */
	public function destroy($key);
	
	/**
	 * Delete outdated sessions
	 */
	public function gc($lifetime);	
}