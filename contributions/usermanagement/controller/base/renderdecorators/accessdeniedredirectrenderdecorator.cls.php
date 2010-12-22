<?php
/**
 * Catches 403 and redirects to login page, if user is not already logged in
 * 
 * This render decorator replaces the configuration option USER_403_BEHAVIOUR.
 * 
 * @section Usage Usage
 * 
 * Set this class as a render decorator on the PageData you create in the index.php, 
 * like this:
 * 
 * @code
 * $cache_manager = new AnonymousCacheManager();
 * $page_data = new PageData($cache_manager, $_GET, $_POST);
 * $page_data->add_render_decorator_class('AccessDeniedRedirectRenderDecorator');
 * @endcode
 * 
 * @since 0.6
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class AccessDeniedRedirectRenderDecorator extends RenderDecoratorBase {
	/**
	 * Render content
	 *
	 * @param PageData $page_data
	 * @return void
	 */
	public function render_content($page_data) {
		parent::render_content($page_data);
		if ($page_data->status_code == ControllerBase::ACCESS_DENIED) {
			if (!Users::is_logged_in()) {
				Session::push('login_goto', Url::current()->build(Url::ABSOLUTE));
				Url::create(ActionMapper::get_url('login'))->redirect();
				exit;
			}
		}
	}	
}