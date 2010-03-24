<?php
/**
 * Allow access only for logged in users (of given role)
 *
 * If constructed with a user role or an array of roles,
 * this render decorator will check, if the current user
 * has the role assigned.
 *
 * If constructed with an empty role, decorator only checks,
 * if a user is logged in.
 *
 * If access is denied, a 403 is returned, unless the constant
 * APP_USER_403_BEHAVIOUR is set to 'REDIRECT_LOGIN', in which 
 * case the user is redirected to the login page.
 * 
 * This render decorates disables caching on the given route.
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class AccessRenderDecorator extends RenderDecoratorBase {
	/**
	 * Stored value of access checking
	 *
	 * @var boolean
	 */
	private $access_granted = false;

	/**
	 * Constructor
	 *
	 * @param string $role Role user needs to have to access this page
	 * @param boolean $require_exact_role If TRUE, accewss checking is done for IsRole() else for hasAcessLevel()
	 * @return void
	 */
	public function __construct($role = null) {
		$allow_access = false;
		if (Users::is_logged_in()) {
			$allow_access = true;
			if (!empty($role)) {
				$allow_access = Users::current_has_role($role);			
			}
		}
		$this->access_granted = $allow_access;
	}

	/**
	 * Initialize this decorator and the data passed
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function initialize($page_data) {
		$page_data->set_cache_manager(new NoCacheCacheManager()); // Do not cache
		if ($this->access_granted == false) {
			$page_data->status_code = ControllerBase::ACCESS_DENIED;
			if (Config::get_value(ConfigUsermanagement::USER_403_BEHAVIOUR) == 'REDIRECT_LOGIN') {
				if (!Users::is_logged_in()) {
					Url::create(ActionMapper::get_url('login'))->redirect();
					exit;
				}
			}
		}
		else {
			parent::initialize($page_data);
		}
	}
	
	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function render_content($page_data) {
		if ($this->access_granted == true) {
			parent::render_content($page_data);
		}
	}
}