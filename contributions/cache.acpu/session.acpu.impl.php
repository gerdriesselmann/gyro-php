<?php
/**
 * Redirect session to write to ACPu
 * 
 * @author Gerd Riesselmann
 * @ingroup ACPu
 */
class ACPuSession implements ISessionHandler {
	/**
	 * Open a session
	 */ 
	public function open(string $save_path, string $session_name): bool {
		return true;
	}
	
	/**
	 * Close a session
	 */
	public function close(): bool {
		//Note that for security reasons the Debian and Ubuntu distributions of 
		//php do not call _gc to remove old sessions, but instead run /etc/cron.d/php*, 
		//which check the value of session.gc_maxlifetime in php.ini and delete the session 
		//files in /var/lib/php*.  This is all fine, but it means if you write your own 
		//session handlers you'll need to explicitly call your _gc function yourself.  
		//A good place to do this is in your _close function
		
		// Since ACPu takes care of life time, no gc is needed
		//$this->gc(ini_get('session.gc_maxlifetime'));
		return true;
	}
	
	/**
	 * Load session data from ACPu
	 */
	public function read(string $key): string|false {
		// Write and Close handlers are called after destructing objects since PHP 5.0.5
		// Thus destructors can use sessions but session handler can't use objects.
		// So we are moving session closure before destructing objects.
		register_shutdown_function('session_write_close');
		$key = $this->create_key($key);
		if (apcu_exists($key)) {
			return apcu_fetch($key);
		}
		return '';
	}
	
	/**
	 * Write session data to ACPu
	 */
	public function write(string $key, string $value): bool {
		try {
			apcu_store($this->create_key($key), $value, ini_get('session.gc_maxlifetime'));
			return true;
		}
		catch(Exception $ex) {
			return false;
		}	
	}
	
	/**
	 * Delete a session
	 */
	public function destroy(string $key): bool {
		apcu_delete($this->create_key($key));
		return true;
	}
	
	/**
	 * Delete outdated sessions
	 */
	public function gc(int $lifetime): int|false {
		// ACPu does this for us
		return true;
	}
	
	protected function create_key($key) {
		return 'g$s' . Config::get_url(Config::URL_DOMAIN) . '_' . $key;
	} 
}
