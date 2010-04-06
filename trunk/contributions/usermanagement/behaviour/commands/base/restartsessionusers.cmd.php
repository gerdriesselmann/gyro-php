<?php
/**
 * Restart session before logign in user - created salted session id
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class RestartsessionUsersBaseCommand extends CommandChain {		
	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'restartsession';
	}
	
	/**
	 * Execute this command
	 */
	protected function do_execute() {
		$ret = new Status();
		/* @var $user DAOUsers */
		$user = $this->get_instance();
		$salt = $user->creationdate . $user->email . $user->id . $user->password . $user->modificationdate;
		$sess_id = sha1(uniqid($salt, true));
		Session::restart($sess_id);
		return $ret;
	}	
} 
