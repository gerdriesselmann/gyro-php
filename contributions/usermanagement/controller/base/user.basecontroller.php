<?php
/**
 * Basic user controller, offers log in, logout etc
 * 
 * @attention You must subclass this to enable user management.
 * 
 * Overload get_features_policy() to enable or disable featured
 * 
 * @author Gerd Riesselmann
 * @ingroup Usermanagement
 */
class UserBaseController extends ControllerBase {
	const ALLOW_REGISTER = 1;
	const ALLOW_LOST_PASSWORD = 2;
	const ALLOW_RESEND_REGISTRATION = 4;
	const ALLOW_LOGIN = 8;
	const SUPPORT_DASHBOARD = 16;
	
	/**
	 * Force user to confirm data, if TOS or email is not up to date
	 */
	const SUPPORT_CONFIRM_DATA = 32;
	/**
	 * Display a TOS checkbox on register
	 */
	const SUPPORT_TOS = 64;
	
	const ALL_FEATURES = 255;
	
	/**
	 * A delegate used to render user pages and menus
	 *
	 * @var IDashboard
	 */
	protected $dashboards = null;
	
	/**
 	 * Return array of IDispatchToken this controller takes responsability
 	 */
 	public function get_routes() {
 		$ret = array(
 			'logout' => new ExactMatchRoute('https://logout', $this, 'logout', new NoCacheCacheManager()),
 			'edit_self' => new ExactMatchRoute('https://user/edit', $this, 'users_edit_self', new AccessRenderDecorator()),
 			'delete_account' => new ExactMatchRoute('https://user/delete_account', $this, 'users_delete_account', new AccessRenderDecorator()),
 			'create' => new ExactMatchRoute('https://user/create', $this, 'users_create', new AccessRenderDecorator(USER_ROLE_ADMIN)),
 			'edit' => new ParameterizedRoute('https://user/{id:ui>}/edit', $this, 'users_edit', new AccessRenderDecorator(USER_ROLE_ADMIN)),
 			'list_all' => new ExactMatchRoute('https://user/list', $this, 'users_list_all', new AccessRenderDecorator(USER_ROLE_ADMIN)),
 			'list_confirmations' => new ExactMatchRoute('https://user/confirmations', $this, 'users_list_confirmations', new AccessRenderDecorator(USER_ROLE_ADMIN)),
 		);
 		if ($this->has_feature(self::ALLOW_LOGIN)) {
 			$ret['login'] = new ExactMatchRoute('https://login', $this, 'login', new NoCacheCacheManager());
 		}
 		if ($this->has_feature(self::ALLOW_REGISTER)) {
 			$ret['register'] = new ExactMatchRoute('https://register', $this, 'register', new NoCacheCacheManager());
 		}
 		if ($this->has_feature(self::ALLOW_LOST_PASSWORD)) {
 			$ret['lost_password'] = new ExactMatchRoute('https://lost-password', $this, 'lost_password', new NoCacheCacheManager());
 			$ret['lost_password_reenter'] = new ParameterizedRoute('https://user/lost-password/{token:s}/', $this, 'lost_password_reenter', new AccessRenderDecorator());
 		}
 		if ($this->has_feature(self::ALLOW_REGISTER | self::ALLOW_RESEND_REGISTRATION)) {
 			$ret['resend_registration_mail'] = new ExactMatchRoute('https://resend-registration-mail', $this, 'resend_registration_mail', new NoCacheCacheManager());
 		}
 		if ($this->has_feature(self::SUPPORT_DASHBOARD)) {
 			$ret['dashboard'] = new ExactMatchRoute('https://user', $this, 'dashboard', new AccessRenderDecorator());
 		}
 		if ($this->has_feature(self::SUPPORT_CONFIRM_DATA)) {
 			$ret['confirm'] = new ExactMatchRoute('https://user/confirm', $this, 'users_confirm', new AccessRenderDecorator());
 			$ret['confirm_mail'] = new ExactMatchRoute('https://user/confirm/mail', $this, 'users_confirm_mail', new AccessRenderDecorator());
 		}
 		return $ret;
 	}
 	
 	/**
 	 * Returns a set of bitflags defining the features supported
 	 *
 	 * @return int
 	 */
	protected function get_features_policy() {
		return self::ALL_FEATURES ^ self::SUPPORT_TOS;
	}
	
	protected function has_feature($feature) {
		return Common::flag_is_set($this->get_features_policy(), $feature);
	}
 	
	/**
	 * Create a dashboard for given user
	 * 
	 * @param DAOUsers $user
	 * @return IDashboard
	 */
	protected function create_dashboards($user) {
		if (empty($user) || !$this->has_feature(self::SUPPORT_DASHBOARD)) {
			return null;
		}
		
		$ret = array();

		foreach ($user->get_role_names() as $role) {
			$role = GyroString::plain_ascii($role);
			$dashboard_file = 'controller/tools/dashboards/' .  $role . '.dashboard.php';
			$dashboard_class = ucfirst($role) . 'Dashboard';
			$found = Load::first_file($dashboard_file);
			if ($found) {
				$ret[] = new $dashboard_class($user);
			}
		}
		
		// Add default dashboard
		$dashboard_file =  'controller/tools/dashboards/default.dashboard.php';
		Load::first_file($dashboard_file);
		$ret[] = new DefaultDashboard($user);

		return $ret;
	}
	
	/**
	 * Invoked after setting data and before actions are processed
	 */
	public function preprocess($page_data) {
		$this->dashboards = $this->create_dashboards(Users::get_current_user());
		parent::preprocess($page_data);
	}

	/**
	 * Activates includes before action to reduce cache memory 
	 */ 
	public function before_action() {
		Load::tools(array('formhandler', 'filtertext'));
	}

	/**
	 * Process events.
	 * 
	 * Events processed are:
	 * - cron with param "orders": Prepares orders of newly requested categores for entries
	 */
	public function on_event($name, $params, &$result) {
		if ($name == 'block' && $params['name'] === 'user') {
			$result[] = $this->do_user_block(); 
		}
	}

	/**
	 * Build the user block
	 *
	 * @return BlockBase
	 */
	protected function do_user_block() {
		$user = Users::is_logged_in() ? Users::get_current_user() : NULL;
		$block = new BlockBase('user', $this->get_block_title($user), '');
		
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'users/blocks/menu');
		$view->assign('user', $user);
		$view->assign('prefix', $this->create_user_block_prefix($user));
		$view->assign('menu_list', $this->create_user_block_menu_list($user));
		$view->assign('postfix', $this->create_user_block_postfix($user));
		$view->assign('block', $block);
		
		$block->set_content($view->render());
		return $block;
	} 
	
	/**
	 * Returns title for user block
	 *
	 * @param DAOUsers $user
	 * @return string
	 */
	protected function get_block_title($user) {
 		 return tr('User Menu', 'users');
	}
	
	/**
	 * Prefix text of block
	 *
	 * @param DAOUsers $user NULL if logged out
	 * @return string
	 */
	protected function create_user_block_prefix($user) {
		$ret = '';
		if ($user) {
			$block_text = tr(
				'Logged in as %user%', 
				'users', 
				array('%user%' => html::span($user->name, 'logged_in_as'))
			);
			$ret = html::p($block_text, 'logged_in_as');
		}
		return $ret; 		
	}

	/**
	 * Returns menu list 
	 *
	 * @param DAOUsers $user NULL if logged out
	 * @return array
	 */
	protected function create_user_block_menu_list($user) {
		$li = array();
		if ($user) {
			if ($this->dashboards) {
				$li[] = html::a(
					tr('Your personal site', 'users'), 
					ActionMapper::get_url('dashboard', $user),
					''
				);
				foreach($this->dashboards as $dashboard) {
					$li = array_merge($li, $dashboard->get_user_menu_entries());
				}
			}
		}
		else {
 			if ($this->has_feature(self::ALLOW_LOGIN)) {
				$li[] = html::a(
					tr('Login', 'users'), 
					ActionMapper::get_url('login'), 
					tr('Log into %app%', 'users', array('%app%' => Config::get_value(Config::TITLE)))
				);
 			}
 			if ($this->has_feature(self::ALLOW_REGISTER)) {
 				$li[] = html::a(
 					tr('Register', 'users'), 
 					ActionMapper::get_url('register'), 
 					tr('Registered user can add and edit entries', 'users')
 				);
 			}
			
		}		
		return $li;
	}
	
	/**
	 * Postfix text of block
	 *
	 * @param DAOUsers $user NULL if logged out
	 * @return string
	 */
	protected function create_user_block_postfix($user) {
		$ret = '';
		if ($user) {
			$ret .= html::form(
				'frmlogout', 
				ActionMapper::get_url('logout'), 
				html::submit(
					tr('Logout', 'users'),
					'btnlogout', 
					tr('Quit %app%', 'users', array('%app%' => Config::get_value(Config::TITLE)))
				)
			);
		}
		return $ret;				
	}
	
	
 	/**
 	 * Builds and process login page
 	 * 
 	 * @param PageData $page_data
 	 */
 	public function action_login($page_data) {
		$page_data->in_history = false;

		$err = $this->check_login_preconditions();
		if ($err->is_error()) {
			$page_data->error($err);
			return;
		}

		$formhandler = new FormHandler('login');
 		if ($page_data->has_post_data()) {
			$this->do_login($formhandler, $page_data);
		}

 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'core::users/login', $page_data);
		$formhandler->prepare_view($view);
		$view->assign('goto', Session::peek('login_goto'));
 		$view->render();
 	}

 	/**
 	 * Logs out
 	 */
 	public function action_logout($page_data) {
 		Users::logout();
 		History::go_to(0, new Message(tr('You have been logged out', 'users')), Config::get_url(Config::URL_DEFAULT_PAGE));
 		exit;
 	}
 	
 	/**
 	 * Deletes account
 	 */
 	public function action_users_delete_account($page_data) {
		 $page_data->in_history = false;

		if (Users::current_has_role(USER_ROLE_USER) == false) {
			return self::ACCESS_DENIED;
		}
		
		$formhandler = new FormHandler('delete_account');
 		if ($page_data->has_post_data()) {
			$this->do_delete_account($formhandler, $page_data);
		}

 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/delete_account', $page_data);
		$formhandler->prepare_view($view);
 		$view->render();
 	}

 	/**
 	 * Builds and process the register page
 	 */
 	public function action_register($page_data) {
		$page_data->in_history = false;

		$err = $this->check_login_preconditions();
		if ($err->is_error()) {
			$page_data->error($err);
			return;
		}

		$formhandler = new FormHandler('register');
 		if ($page_data->has_post_data()) {
			$this->do_register($formhandler, $page_data);
		}

 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/register', $page_data);
 		$view->assign('feature_resend', $this->has_feature(self::ALLOW_RESEND_REGISTRATION));
 		$view->assign('feature_tos', $this->has_feature(self::SUPPORT_TOS));

		$formhandler->prepare_view($view);
 		$view->render();
 	}

	/**
	 * Lost password page
	 */
	public function action_lost_password($page_data) {
		$page_data->in_history = false;

		$err = $this->check_login_preconditions();
		if ($err->is_error()) {
			$page_data->error($err);
			return;
		}

		$formhandler = new FormHandler('lost_password');
 		if ($page_data->has_post_data()) {
			$this->do_lost_password($formhandler, $page_data);
		}

 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/lost_password', $page_data);
		$formhandler->prepare_view($view);
 		$view->render();
	}

	/**
	 * Reenter password after loss
	 */
	public function action_lost_password_reenter(PageData $page_data, $token) {
		$page_data->in_history = false;

		if (Session::peek(Users::LOST_PASSWORD_TOKEN) != $token) {
			return self::NOT_FOUND;
		}

		$formhandler = new FormHandler('lost_password_reenter');
		if ($page_data->has_post_data()) {
			$this->do_lost_password_reenter($formhandler, $page_data, $token);
		}

		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/lost_password_reenter', $page_data);
		$formhandler->prepare_view($view);
		$view->assign('token', $token);
		$view->render();
	}

	private function do_lost_password_reenter(FormHandler $formhandler, PageData $page_data, $token) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			$user = Users::get_current_user();
			$params = $user->unset_internals($page_data->get_post()->get_array());
			$err->merge($this->validate_password($params));
			if ($err->is_ok()) {
				$err->merge(Users::update($user, $params));
			}
			if ($err->is_ok()) {
				Session::pull(Users::LOST_PASSWORD_TOKEN);
			}
		}
		$formhandler->finish($err, tr('Your password has been changed', 'users'));
	}

	/**
	 * Page for resending registration e-mail
	 */
	public function action_resend_registration_mail($page_data) {
		$page_data->in_history = false;

		$err = $this->check_login_preconditions();
		if ($err->is_error()) {
			$page_data->error($err);
			return;
		}

		$formhandler = new FormHandler('resend_registration');
 		if ($page_data->has_post_data()) {
			$this->do_resend_registration_mail($formhandler, $page_data);
		}

 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/resend_registration_mail', $page_data);
		$formhandler->prepare_view($view);
 		$view->render();
	}

	/**
	 * Show dashboard, depending on user logged in
	 */
	public function action_dashboard($page_data) {
		if (Users::is_logged_in() == false) {
			return CONTROLLER_ACCESS_DENIED;
		}
		
		$dashboards = $this->create_dashboards(Users::get_current_user());
		if ($dashboards) {
			$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/dashboard', $page_data);
			$view->assign('dashboards', $dashboards);
			$view->render();
		}
		else {
			return CONTROLLER_INTERNAL_ERROR;
		}
	}

	/**
	 * Create user
	 */
	public function action_users_create($page_data) {
		$formhandler = new FormHandler('user_create');
		if ($page_data->has_post_data()) {
			$this->do_create($formhandler, $page_data);
		}

 		$page_data->in_history = false;
		
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/create', $page_data);	
		$roleOptions = Users::get_user_roles();
		$view->assign('role_options', $roleOptions);	
		//$view->assign('user', $user);
		$formhandler->prepare_view($view); //, $user);
		
		$view->render();
	}

 	/**
 	 * Do create a user
 	 * 
 	 * @param FormHandler $formhandler
 	 * @param PageData $page_data
 	 */
 	protected function do_create($formhandler, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			// Validate
			$params = $page_data->get_post()->get_array();
			$err->merge($this->validate_password($params));
			if ($err->is_ok()) {
				$dummy = false;
				$err->merge(Users::create($params, $dummy));
			}
		}
		$formhandler->finish($err, tr('The new user has been created', 'users'));
 	}
	
	/**
	 * Edit account settings
	 */
	public function action_users_edit($page_data, $id) {
		$user = Users::get($id);
		if ($user == false) {
			return self::NOT_FOUND;
		}
		foreach($user->get_roles() as $role) {
			$user->roles[] = $role->id;
		}	
		
		$formhandler = new FormHandler('edit_account');
		if ($page_data->has_post_data()) {
			$this->do_edit($formhandler, $user, $page_data);
		}

 		$page_data->in_history = false;
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/edit', $page_data);	
					
		//smarty option list for user role
		$roleOptions = Users::get_user_roles();
		$view->assign('role_options', $roleOptions);	
		$view->assign('user', $user);
		
		$formhandler->prepare_view($view, $user);
		
		$view->render();
	}

	/**
	 * Validate if password is set and if it is confirmed
	 *
	 * @param array $arr_post
	 * @return Status
	 */
	protected function validate_password(&$arr_post) {
		$ret = new Status();
		// Validate
		$pwd1 = Arr::get_item($arr_post, 'pwd1', '');
		$pwd2 = Arr::get_item($arr_post, 'pwd2', '');
		if ($pwd1 != $pwd2) {
			$ret->append(tr('Password and password confirmation are different', 'users'));
		}
		
		if ($ret->is_ok()) {
			if ($pwd1 !== '') {
				$arr_post['password'] = $pwd1;
			}
		}
		return $ret;			
	}

 	/**
 	 * Change account data of user
 	 * 
 	 * @param FormHandler $formhandler
 	 * @param DAOUsers $user
 	 * @param PageData $page_data
 	 */
 	protected function do_edit($formhandler, $user, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			// Validate
			$params = $page_data->get_post()->get_array();
			$err->merge($this->validate_password($params));
			if ($err->is_ok()) {
				$err->merge(Users::update($user, $params));
			}
		}
		$formhandler->finish($err, tr('Your changes have been saved', 'users'));
 	}
		
	/**
	 * Edit account settings
	 */
	public function action_users_edit_self($page_data) {
		$page_data->in_history = false;

		// User exists, since Route is for logged in only
		Users::reload_current();
		$user = Users::get_current_user();
		$formhandler = new FormHandler('edit_account_self');
		if ($page_data->has_post_data()) {
			$this->do_edit_self($formhandler, $user, $page_data);
		}

 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/edit_self', $page_data);
		$formhandler->prepare_view($view, $user);
		$view->assign('user', $user);
		$view->render();
	}
	
 	/**
 	 * Change account data of current user
 	 * 
 	 * @param FormHandler $formhandler
 	 * @param DAOUsers $user
 	 * @param PageData $page_data
 	 */
 	protected function do_edit_self($formhandler, $user, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			// Validate
			$params = $user->unset_internals($page_data->get_post()->get_array());
			$err->merge($this->validate_email_change($params, $user, $page_data->get_post()->get_item('pwd_mail')));
			$err->merge($this->validate_password_change($params, $page_data->get_post()->get_item('pwd_pwd')));
			if ($err->is_ok()) {
				$err->merge(Users::update($user, $params));
			}
		}
		$formhandler->finish($err, tr('Your changes have been saved', 'users'));
 	}


	/**
	 * Validate password change
	 *
	 * @param array $params
	 * @param DAOUsers $user
	 * @param string $pwd
	 */
	protected function validate_password_change(&$params, $pwd) {
		$err = $this->validate_password($params);
		if ($err->is_ok() && !Config::has_feature(ConfigUsermanagement::ENABLE_MAIL_ON_PWD_CHANGE) && !empty($params['password'])) {
			if (!Users::get_current_user()->password_match($pwd)) {
				$err->append(tr('The password entered for password change confirmation is not correct. Please try again.', 'users'));
			}
		}
		return $err;
	}

 	/**
 	 * Validate password for email change
 	 * 
 	 * @param array $params
 	 * @param DAOUsers $user
 	 * @param string $pwd
 	 */
 	protected function validate_email_change($params, $user, $pwd) {
 		$err = new Status();
		if (Config::has_feature(ConfigUsermanagement::ENABLE_PWD_ON_EMAILCHANGE) && $params['email'] != $user->email) {
 			if (!Users::get_current_user()->password_match($pwd)) {
 				$err->append(tr('The password entered for email change confirmation is not correct. Please try again.', 'users'));	
 			}
		} 	
		return $err;	
 	}
 	
	/**
	 * Confirm account settings
	 */
	public function action_users_confirm($page_data) {
 		$page_data->in_history = false;
		
 		// User exists, since Route is for logged in only
		Users::reload_current();
		$user = Users::get_current_user();
		$formhandler = new FormHandler('users_confirm');
		if ($page_data->has_post_data()) {
			$this->do_confirm($formhandler, $user, $page_data);
		}
		
 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/confirm', $page_data);
 		$this->prepare_confirm_view($view, $formhandler, $user);
		$view->render();
	}
	
	/**
	 * Prepare confirmation view
	 * 
	 * @param IView $view
	 * @param FormHandler $formhandler
	 * @param DAOUsers $user
	 */
	protected function prepare_confirm_view($view, $formhandler, $user) {
		$formhandler->prepare_view($view, $user);
		$view->assign('user', $user);
		$view->assign('do_tos', $this->has_feature(self::SUPPORT_TOS) && !$user->confirmed_tos());
		$view->assign('do_email', !$user->confirmed_email());		
	}
	
 	/**
 	 * Change account data of current user
 	 * 
 	 * @param FormHandler $formhandler
 	 * @param DAOUsers $user
 	 * @param PageData $page_data
 	 */
 	protected function do_confirm($formhandler, $user, $page_data) {
 		$validate_email_cmd = false;
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			$post = $page_data->get_post();
			$params = $post->get_array(); // Variable passed by ref since PHP 7
			$err->merge($this->process_confirm_data($params, $post->get_item('tos'), $user, $validate_email_cmd));
			
			// Update
			if ($err->is_ok()) {
				$err->merge(Users::update($user, $params));
			}			 
			
			if ($validate_email_cmd && $err->is_ok()) {
				$err->merge($validate_email_cmd->execute());	
			}
		}
		$formhandler->finish($err, tr('Your changes have been saved', 'users'));
 	}
 	
 	protected function process_confirm_data(&$params, $tos, $user, &$validate_email_cmd) {
 		$err = new Status();
 		
		// Check for TOS
		if($this->has_feature(self::SUPPORT_TOS) && !$user->confirmed_tos() && !$tos) {
			$err->append(tr('Please agree to the Terms of Service.', 'users'));				
		}
		// Validate
		$params = $user->unset_internals($params);
		$params['tos_version'] = Config::get_value(ConfigUsermanagement::TOS_VERSION);
		$err->merge($this->validate_password($params));
		
		// If email is not validated, validate it
		$email = Arr::get_item($params, 'email', '');
		if ($user->email != $email && Validation::is_email($email)) {
			// Send email validation request
			unset($params['email']);
			$params_validate = array(
				'id_item' => $user->id,
				'action' => 'validateemail',
				'data' => $email
			);
			Session::push('user_confirm_mail_send', true);
			$validate_email_cmd = CommandsFactory::create_command('confirmations', 'create', $params_validate);
		}
		
 		return $err;
 	}

	/**
	 * Showe page stating email verification mail has been sent 
	 */
	public function action_users_confirm_mail($page_data) {
 		// User exists, since Route is for logged in only
 		$page_data->in_history = false;
		Users::reload_current();
		$user = Users::get_current_user();
		if ($user->confirmed_email()) {
			History::go_to(0);
		}
		else {
			$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/confirm_mail', $page_data);
	 		$view->assign('user' , $user);
	 		$view->render();
		}
	} 	
 	
	/**
	 * List all user data
	 */
	public function action_users_list_all($page_data) {
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'users/list', $page_data);
 		$users = Users::create_all_user_adapter();
 		
 		Load::tools(array('sorter', 'filter', 'filterusername', 'pager'));
		$sorter = new Sorter($page_data, $users->get_sortable_columns(), $users->get_sort_default_column());
		$sorter->apply($users);
 		$sorter->prepare_view($view);
		
		$filter = new Filter($page_data, $users->get_filters());
		$filter->apply($users);
 		$filter->prepare_view($view);

		$filtertext = new FilterUsername($page_data);
		$filtertext->apply($users);
 		$filtertext->prepare_view($view);
		
		$count_users = $users->count();
 		$pager = new Pager($page_data, $count_users, Config::get_value(Config::ITEMS_PER_PAGE));
 		$pager->apply($users);
 		$pager->prepare_view($view);

 		$view->assign('users', $users->execute());	
 		$view->render();
	}
	
	/**
	 * List all unconfirmed users
	 * 
	 * This is a placeholder for a filtered user list, and gets redircted to user_list_all 
	 */
	public function action_users_list_confirmations($page_data) {
 		Load::tools(array('sorter', 'filter', 'filtertext', 'pager'));
		$url = Url::current()->set_path(ActionMapper::get_path('users_list_all'));
		Filter::apply_to_url($url, 'unconfirmed', 'status');
		$url->redirect();
	}

	/**
	 * Check if cookies are enabled and if user is not logged in
	 */
 	protected function check_login_preconditions() {
 		$ret = new Status();
		//if (Session::cookies_enabled() == false) {
		//	$ret->append('Bitte schalte in den Browsereinstellungen Cookies ein.');
		//}
		if (Users::is_logged_in()) {
			$ret->append(tr('Already logged in', 'users'));
		}
		return $ret;
 	}

 	/**
 	 * Does the login, as a result of a POST request
 	 *
 	 * @return Status Error
 	 */
 	protected function do_login($formhandler, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			$post = $page_data->get_post();
			$permanent = $post->get_item('stayloggedin', false) != false;

			$err->merge(Users::login($post->get_array(), $permanent));
			if ($err->is_ok()) {
				$goto = $post->get_item('goto', '');
				if ($goto) {
					// Go to specific URL (force it to be same domain, though!)
					$goto_url = Url::create($goto)->set_host(Config::get_value(Config::URL_DOMAIN));
					History::push($goto_url->build(Url::ABSOLUTE));
				}
				else if ($this->has_feature(self::SUPPORT_DASHBOARD)) {
					History::push($this->get_dashboard_url());
				}
				Session::pull('login_goto');
			}
		}
		$formhandler->finish($err, tr('Welcome! You are now logged in.', 'users'));
		exit;
 	}

	/**
	 * URL for dashboard
	 *
	 * @return string
	 */
 	protected function get_dashboard_url() {
	    return Config::get_url(ConfigUsermanagement::DEFAULT_PAGE);
    }

	/**
	 * Process delete account request
	 */
	protected function do_delete_account(FormHandler $formhandler, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			$cmd = Users::create_deletion_command(Users::get_current_user());
			$err->merge($cmd->execute());

			if ($err->is_ok()) {
				Users::logout();
				History::push(Url::create(Config::get_url(Config::URL_BASEURL)));
			}
		}
		$formhandler->finish($err, tr('Your account has been deleted', 'users'));
		exit;		
	}

	protected function create_delete_account_command($user) {
		return Users::create_deletion_command($user);
	}

 	/**
 	 * Processes the register POST request
 	 *
 	 * Sets Session::Status on error
 	 */
 	protected function do_register($formhandler, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			// Validate
			$post = $page_data->get_post();;
			$pwd1 = $post->get_item('pwd1');
			$pwd2 = $post->get_item('pwd2');
			if ($pwd1 != $pwd2) {
				$err->append(tr('Password and password confirmation are different', 'users'));
			}
			
			if ($this->has_feature(self::SUPPORT_TOS) && !$post->get_item('tos')) {
				$err->append(tr('Please agree to the Terms of Service.', 'users'));
			}
			
			if ($err->is_ok()) {
				$result = false;
				$err->merge(Users::register(trim($post->get_item('name')), $pwd1, trim($post->get_item('email')), $result));
			}
		}
		$formhandler->finish($err, tr('Your registration request has been created', 'users'));
		exit;
 	}

	/**
	 * Processes the lost_password POST request
 	 */
 	protected function do_lost_password($formhandler, $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			// Validate
			$post = $page_data->get_post();;
			$email = $post->get_item('email');
			$err->merge(Users::lost_password($email));
		}
		$formhandler->finish($err, tr('Your one time login request has been created', 'users'));
		exit;
 	}

	/**
	 * Processes the resend_registration_mail POST request
 	 */
 	protected function do_resend_registration_mail(FormHandler $formhandler, PageData $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			// Validate
			$post = $page_data->get_post();;
			$email = $post->get_item('email');
			$err->merge(Users::resend_registration_mail($email));
		}
		// At this point we habe an error. Do post fix (redirects)
		$formhandler->finish($err, tr('Your activation information mail has been send to you again', 'users'));
		exit;
 	}

}
