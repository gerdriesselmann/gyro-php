<?php
/**
 * Redirect session to write to Memcache
 * 
 * Memcache(d) extension already comes with a session handler build in, so if you want to use
 * that, rather than this implementation, set constant APP_MEMCACHE_STORE_SESSIONS to FALSE in
 * your config files.  
 * 
 * @author Gerd Riesselmann
 * @ingroup Memcache
 */
class MemcacheSession implements ISessionHandler {
	/**
	 * Open a session
	 */ 
	public function open($save_path, $session_name) {
		return true;
	}
	
	/**
	 * Close a session
	 */
	public function close() {
		//Note that for security reasons the Debian and Ubuntu distributions of 
		//php do not call _gc to remove old sessions, but instead run /etc/cron.d/php*, 
		//which check the value of session.gc_maxlifetime in php.ini and delete the session 
		//files in /var/lib/php*.  This is all fine, but it means if you write your own 
		//session handlers you'll need to explicitly call your _gc function yourself.  
		//A good place to do this is in your _close function
		
		// Since Memcache takes care of life time, no gc is needed
		//$this->gc(get_cfg_var('session.gc_maxlifetime'));
		return true;
	}
	
	/**
	 * Load session data from xcache
	 */
	public function read($key) {
		// Write and Close handlers are called after destructing objects since PHP 5.0.5
		// Thus destructors can use sessions but session handler can't use objects.
		// So we are moving session closure before destructing objects.
		register_shutdown_function('session_write_close');
		$key = $this->create_key($key);
		
		$ret = GyroMemcache::get($key);
		if ($ret === false) {
			$ret = '';
		}
		return $ret;
	}
	
	/**
	 * Write session data to XCache
	 */
	public function write($key, $value) {
		try {
			GyroMemcache::set($this->create_key($key), $value, get_cfg_var('session.gc_maxlifetime'));
			return true;
		}
		catch(Exception $ex) {
			return false;
		}	
	}
	
	/**
	 * Delete a session
	 */
	public function destroy($key) {
		GyroMemcache::delete($this->create_key($key));
	}
	
	/**
	 * Delete outdated sessions
	 */
	public function gc($lifetime) {
		// Memcache does this for us
		return true;
	}
	
	protected function create_key($key) {
		return 'g$s' . Config::get_url(Config::URL_DOMAIN) . '_' . $key;
	} 	
}
