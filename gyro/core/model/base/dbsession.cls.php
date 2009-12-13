<?php
Load::models(array('sessions', 'users'));

/**
 * Redirect session to write to DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBSession {
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
		
		$this->gc(get_cfg_var('session.gc_maxlifetime'));
		return true;
	}
	
	/**
	 * Load session data from database
	 */
	function read($key) {
		// Write and Close handlers are called after destructing objects since PHP 5.0.5
		// Thus destructors can use sessions but session handler can't use objects.
		// So we are moving session closure before destructing objects.
		register_shutdown_function('session_write_close');
		$sess = DB::get_item('sessions', 'id', $key);
		if ($sess) {
			return $sess->data;			
		}
		return '';
	}
	
	/**
	 * Write session data to DB
	 */
	function write($key, $value) {
		try {
			// Rollback any open transactions, if there are any
			//DB::rollback();
			$sess = DB::get_item('sessions', 'id', $key);
			$err = false;
			if ($sess) {
				$sess->data = $value;
				$err = $sess->update();
			}
			else {
				$sess = new DAOSessions();
				$sess->id = $key;
				$sess->data = $value;
				$err = $sess->insert();
			}
			return $err->is_ok();
		}
		catch(Exception $ex) {
			return false;
		}	
	}
	
	/**
	 * Delete a session
	 */
	function destroy($key) {
		try {
			$sess = new DAOSessions();
			$sess->id = $key;
			$sess->delete();
		}
		catch(Exception $ex) {}
	}
	
	/**
	 * Delete outdated sessions
	 */
	function gc($lifetime) {
		if (!Session::is_started()) {
			return;
		}
		try {
			$sess = new DAOSessions();
			$sess->add_where('modificationdate', '<', time() - $lifetime);	
			$sess->delete(true);
		}
		catch (Exception $ex) {}
		
		return true;
	}
}
