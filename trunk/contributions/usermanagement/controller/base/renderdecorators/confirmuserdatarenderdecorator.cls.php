<?php
/**
 * A render decorator that forces users to confirm or update their account data
 * 
 * This render decorator
 * 
 * - checks if a user is logged in
 *  
 * - for logged in users checks if the email address is CONFIRMED and the TOS the user 
 *   acknowledged are the same then the current valid TOS
 *   
 * - If this fails redirects to action "users_confirmdata"
 * 
 * - Except if the current route is one of the allowed ones, that can be set through
 *   ConfirmUserDataRenderDecorator::append_allowed_route_id()  
 * 
 * @author Gerd Riesselmann
 * @ingroup Controller 
 */
class ConfirmUserDataRenderDecorator extends RenderDecoratorBase {
	private static $allowed_route_ids = array();
	
	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$user = Users::get_current_user();
		if ($user) {
			$this->confirm_data_if_required($user, $page_data);
		}
		
		parent::initialize($page_data);
	}	
	
	/**
	 * Check if data must be confirmed, if so do it
	 * 
	 * @param DAOUsers $user
	 */
	protected function confirm_data_if_required($user, PageData $page_data) {
		if ($user->confirmed_email() == false || $user->confirmed_tos() == false) {
			if (!$this->is_allowed_route($page_data->router->get_route_id())) {
				if ($page_data->status) {
					$page_data->status->persist();
				}
				Url::create(ActionMapper::get_url('users_confirm'))->redirect(Url::TEMPORARY);
				exit;
			}
		}
	}
	
	/**
	 * Returns true, if the current route is allowed
	 * 
	 * @param string $route_id
	 * @return bool
	 */
	protected function is_allowed_route($route_id) {
		$allowed = self::get_allowed_route_ids();
		$allowed[] = 'UsersController::users_confirm';
		$allowed[] = 'UsersController::logout';
		return in_array($route_id, $allowed);
	}
	
	/**
	 * Return the allowed route ids
	 * 
	 * @return array
	 */
	public static function get_allowed_route_ids() {
		return self::$allowed_route_ids;
	}

	/**
	 * Add allowed route ids
	 * 
	 * @param array|string $route_ids
	 */
	public static function add_allowed_route_ids($route_ids) {
		self::$allowed_route_ids = array_merge(self::$allowed_route_ids, Arr::force($route_ids, false));
	}
}