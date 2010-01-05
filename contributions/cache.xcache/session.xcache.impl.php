<?php
/**
 * Redirect session to write to XCache
 * 
 * @author Gerd Riesselmann
 * @ingroup XCache
 */
class XCacheSession {
	/**
	 * Switch session handling to this instance
	 */	
	public function __construct() {
		session_set_save_handler(
			array($this, 'open'), 
			array($this, 'close'), 
			array($this, 'read'), 
			array($this, 'write'), 
			array($this, 'destroy'), 
			array($this, 'gc')
		);		
	} 

	/**
	 * Open a session
	 */ 
	function open($save_path, $session_name) {
		return true;
	}
	
	/**
	 * Close a session
	 */
	function close() {
		//Note that for security reasons the Debian and Ubuntu distributions of 
		//php do not call _gc to remove old sessions, but instead run /etc/cron.d/php*, 
		//which check the value of session.gc_maxlifetime in php.ini and delete the session 
		//files in /var/lib/php*.  This is all fine, but it means if you write your own 
		//session handlers you'll need to explicitly call your _gc function yourself.  
		//A good place to do this is in your _close function
		
		// Since XCache takes care of life time, no gc is needed
		//$this->gc(get_cfg_var('session.gc_maxlifetime'));
		return true;
	}
	
	/**
	 * Load session data from xcache
	 */
	function read($key) {
		// Write and Close handlers are called after destructing objects since PHP 5.0.5
		// Thus destructors can use sessions but session handler can't use objects.
		// So we are moving session closure before destructing objects.
		register_shutdown_function('session_write_close');
		$key = 'g$s_' . $key;
		if (xcache_isset($key)) {
			return xcache_get($key);
		}
		return '';
	}
	
	/**
	 * Write session data to XCache
	 */
	function write($key, $value) {
		try {
			xcache_set('g$s_' . $key, $value, get_cfg_var('session.gc_maxlifetime'));
			return true;
		}
		catch(Exception $ex) {
			return false;
		}	
	}
	
	/**
	 * Delete a session
	 */
	function destroy($key) {
		xcache_unset('g$s_' . $key);
	}
	
	/**
	 * Delete outdated sessions
	 */
	function gc($lifetime) {
		// XCache does this for us
		return true;
	}
}
