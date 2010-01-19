<?php
/**
 * End a permanent login
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class EndPermanentloginsCommand extends CommandComposite {
	/**
	 * Does executing
	 */
	protected function do_execute() {
		$ret = new Status();
		Load::commands('generics/cookie.delete');
		
		$login = PermanentLogins::get_current();
		if ($login) {
			$this->append(CommandsFactory::create_command($login, 'delete', false));
		}
		// remove cookie
		$this->append(new CookieDeleteCommand(PermanentLogins::COOKIE_NAME));
		
		return $ret;
	}
}	