<?php
/**
 * Created on 11.04.2007
 *
 * @author Gerd Riesselmann
 */

Load::commands('base/logout');

class LogoutUsersCommand extends LogoutUsersBaseCommand {
	// Overload logout, if account was hijacked
	protected function do_execute() {
		$ret = new Status();
		
		$old_session_id = Cookie::get_cookie_value(HijackAccount::COOKIE_NAME);
		$saved_session = DB::get_item('hijackaccountsavedsessions', 'id', $old_session_id);
		if ($saved_session) {
			Load::commands('users/loginknown', 'generics/cookie.delete');
			$cmd = new LoginknownUsersCommand($saved_session->get_user());
			$ret->merge($cmd->execute());
			if ($ret->is_ok()) {
				$_SESSION = $saved_session->data;
				$cmd_del_cookie = new CookieDeleteCommand(HijackAccount::COOKIE_NAME);
				$cmd_del_cookie->execute(); 
			}			 
		}
		else {
			$ret->merge(parent::do_execute());
		}
		
		return $ret;
	}
}
