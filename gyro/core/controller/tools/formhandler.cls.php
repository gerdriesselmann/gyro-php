<?php
Load::models('formvalidations');

/**
 * Wraps functionality related to forms
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */ 
class FormHandler {
	/**
	 * Create a token on each request
	 */
	const TOKEN_POLICY_UNIQUE = 1;
	/**
	 * Do not use tokens
	 */
	const TOKEN_POLICY_NONE = 0;
	/**
	 * Create a token for a form, but reuse it throughout the request (used for commands forms)
	 */
	const TOKEN_POLICY_REUSE = 2;	
	/**
	 * Create a token for a form, and reuse it for several requests (until it expires).
	 *
	 * @attention Note this may lead to behaviours that users may find strange: tokens expiring after short time,
	 *            invalidating of forms, if more than one browser window is opened etc.
	 */
	const TOKEN_POLICY_REUSE_ACROSS_REQUESTS = 3;

	/**
	 * Name of form. 
	 */
	private $name = '';
	
	/**
	 * Url of form
	 */
	private $url = null;
	
	/**
	 * How to deal with tokens
	 */
	private $token_policy = true;
		
	/**
	 * Constructor
	 * 
	 * @param string $name Name of form
	 * @param string $path (optional) Path of form , if != current path
	 * @param bool   $create_token True to create a unique token to identify Form
	 */	
 	public function __construct($name, $path = '', $token_policy = self::TOKEN_POLICY_UNIQUE) {
 		// Compatability
 		if ($token_policy === false) {
 			$token_policy = self::TOKEN_POLICY_NONE;
 		}
 		else if ($token_policy === true) {
 			$token_policy = self::TOKEN_POLICY_UNIQUE;
 		}

		 // Check name length. http://code.google.com/p/gyro-php/issues/detail?id=6
		if (Config::has_feature(Config::TESTMODE) && GyroString::length($name) > FormValidations::LENGTH_NAME) {
			throw new InvalidArgumentException('Name for formhandler ist too long: ' . $name);
		}
 		$this->name = $name;
 		$this->token_policy = $token_policy;
 		$this->url = Url::current();
 		if (!empty($path)) {
 			$this->url->set_path($path);
 		}
 	}
 		
 	/**
 	 * Set data required on view
 	 * 
 	 * @param $view IView The view to populate with data
 	 * @param $data mixed Array or Object containing key/value-pairs for default data
 	 */
 	public function prepare_view($view, $data = false) {
 		$token = $this->create_token();

	 	$token_html = '';
 		if ($token) {
	 		$token_html .= html::input('hidden', Config::get_value(Config::FORMVALIDATION_FIELD_NAME), array('value' => $token));
			$token_html .= html::input('hidden', Config::get_value(Config::FORMVALIDATION_HANDLER_NAME), array('value' => $this->name));

 		}
 		$view->assign('form_validation', $token_html);

		if (!empty($data)) {
 			$this->set_form_data_on_view((array)$data,$view);
		}
 		
 		$form_data = $this->restore_post_data();
 		$this->set_form_data_on_view($form_data, $view);
 	}
 	
 	/**
 	 * Set value of restores POST on view
 	 * 
 	 * Sets variables of form 'form_data_[POST-key]'
 	 * 
 	 * @param array $form_data
 	 * @param IView $view
 	 */
 	private function set_form_data_on_view($form_data, $view) {
 		$form_data_clean = Arr::force($view->retrieve('form_data'), false);
 		if (is_array($form_data)) {
 			foreach($form_data as $key => $value) {
 				if ($key != Config::get_value(Config::FORMVALIDATION_FIELD_NAME) && $key != Config::get_value(Config::FORMVALIDATION_HANDLER_NAME)) {
 					$form_data_clean[$key] = $value;
 				}
 			}
 		}
 		$view->assign('form_data', $form_data_clean);
 	}

 	/**
 	 * Create a new token
 	 */
 	private function create_token() {
 		$token = ''; 
 		switch($this->token_policy) {
 			case self::TOKEN_POLICY_NONE:
 				break;
 			case self::TOKEN_POLICY_REUSE:
 				$token = FormValidations::create_or_reuse_token($this->name);
 				break;
			case self::TOKEN_POLICY_REUSE_ACROSS_REQUESTS:
				$token = FormValidations::create_or_reuse_token_across_requests($this->name);
				break;
 			default:
 				$token = FormValidations::create_token($this->name);
 				break;						 				
 		}
 		
 		return $token; 			
 	}
 	
 	/**
 	 * Validate a Form
 	 * 
 	 * @return Status 
 	 */
 	public function validate($data = false) {
 		if ($data === false) {
 			$data = $_POST;
 		}
 		$ret = new Status();
		$success = true;
 		if ($this->token_policy != self::TOKEN_POLICY_NONE) {
			$token = Arr::get_item($data, Config::get_value(Config::FORMVALIDATION_FIELD_NAME), '');
			// Validate if token is in DB
			$success = $success && ($this->name == Arr::get_item($data, Config::get_value(Config::FORMVALIDATION_HANDLER_NAME), ''));
	 		$success = $success && FormValidations::validate_token($this->name, $token);
 		}
 		if ($success == false) {
 			$ret->append(tr('Form verification token is too old. Please try again.', 'core'));
 		}
 		return $ret;
 	}

	/**
	 * Called after a form has been processed
	 *
	 * @param Status $status
	 * @param string $success_message Optional message to display on success
	 * @param bool $default_page Page used as redirect source on success, if History is empty
	 */
 	public function finish($status, $success_message = '', $default_page = false) {
 		$params = array(
 			'name' => $this->name,
 			'status' => $status
 		);
 		EventSource::Instance()->invoke_event_no_result('form_finished', $params);
 		
 		if ($status->is_error()) {
 			$this->error($status);
 		}
 		else {
 			$msg = ($status->is_empty()) ? $success_message : $status->to_string(Status::OUTPUT_PLAIN);
 			$this->success($msg, $default_page);
 		}
 	}

	/**
	 * Called if a form has been processed successfully
	 *
	 * @param Status|string $message
	 * @param bool $default_page Page that is used for redirect, if History is empty
	 */
 	public function success($message, $default_page = false) {
 		History::go_to(0, $message, $default_page);
 		exit;
 	}
 	
 	/**
 	 * Called if form has finished unsucessfully
 	 *
 	 * @param Status|string $status
 	 */
 	public function error($status) {
 		if (!($status instanceof Status)) {
 			$status = new Status($status);
 		}
 		$this->fix_post_history($status);
 		exit;
 	}
 	
 	/**
 	 * Allows back button in browser even after POST
 	 * 
 	 * Does a redirect. Requires open session.
 	 * Stores POST data (restored in constructor)
 	 * 
 	 * @param Status $status
 	 */
 	public function fix_post_history($status = null) {
 		$this->store_post_data();
 		if ($status) {
 			$status->persist();
 		}
 		$this->url->redirect();
 		exit;
 	}
 		
 	/**
 	 * Stores POST in associate array '
 	 */ 
 	private function store_post_data() {
 		Session::push('form_data', $_POST); 
 	}
 	
 	/**
 	 * Restores former saved POST data
 	 */ 
 	private function restore_post_data() {
 		return Session::pull('form_data'); 
 	} 	
} 

