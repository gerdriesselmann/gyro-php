<?php
require_once dirname(__FILE__) . '/controllerbase.cls.php';

/**
 * Installs a generic post handler at url /process_commands
 * 
 * The list form controller processes one command taken from the POST array. A command
 * is recognized by starting with "cmd_" and usually is a submit button.
 * 
 * A command has a form of cmd_[type]_[action]_[params]. For example a command to disable
 * a user can be named "cmd_user_disable_5", where 5 is the ID of the user. You need to 
 * install a handler on the user controller, to process this command, though. 
 *  
 * Controllers can install themselves as handlers by registering sub-urls, like
 * for example /process_commands/user. If a command is recognized, the second parameter is 
 * interpreted as type and everything is forwarded to an url like 
 * 
 * @code
 * https://process_commands/[type]/[action]/[params]
 * @endcode
 * 
 * If you are interested in handling a command, you must create a matching route. Given the above example of
 * disableing a user, this would be:
 * 
 * @code
 * ... new CommandsRoute('https://process_commands/users/{id:ui>}/disable', $this, 'users_disable')
 * @endcode
 * 
 * @attention
 *   Note the route is of type CommandsRoute, which extends ParameterizedRoute. This is extremly important, 
 *   since it implements protection against Cross Site Request Forgery. You may alternatively add a 
 *   CommandsRouteRenderDecorator to a custom route to achieve the same effect.  
 *  
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class CommandsBaseController extends ControllerBase {
	/**
 	 * Return array of IDispatchToken this controller takes responsability
 	 */
 	public function get_routes() {
 		return array(
 			new ExactMatchRoute('https://post_command', $this, 'commands_post', new NoCacheCacheManager()),
 		);
 	}
 	
 	/**
 	 * Find according commands and redirect
 	 */
	public function action_commands_post($page_data) {
		$page_data->in_history = false;
		$err = new Status();
		
		if ($page_data->has_post_data() == true) {
			$err = $this->redirect_to_handler($page_data);
		}
		else {
			$err->append(tr('No command data was send', 'core'));
		}
		
		History::go_to(0, $err);
		exit;
	}
	
	/**
	 * Scans POST array for commands and redirects to handler pages
	 * 
	 * @return Status
	 */
	private function redirect_to_handler(PageData $page_data) {
		$post = $page_data->get_post(); 
		foreach($post->get_array() as $key => $value) {
			$arr = explode(GYRO_COMMAND_SEP, $key);
			if (count($arr) >= 2 && $arr[0] == 'cmd') {
				array_shift($arr); // remove 'cmd' from array
				$path = Config::get_url(Config::URL_BASEDIR) . 'process_commands/' . implode('/', $arr);
				$from = $page_data->get_post()->get_item('command_form_source');
				$data = $page_data->get_post()->get_item('command_data');
				
				Url::current()->set_path($path)
					->replace_query_parameter('from', $from)
					->replace_query_parameter('data', $data)
					->replace_query_parameter(Config::get_value(Config::FORMVALIDATION_FIELD_NAME), $post->get_item(Config::get_value(Config::FORMVALIDATION_FIELD_NAME)))
					->replace_query_parameter(Config::get_value(Config::FORMVALIDATION_HANDLER_NAME), $post->get_item(Config::get_value(Config::FORMVALIDATION_HANDLER_NAME)))
					->redirect();
				exit;
			}
		}	
		
		return new Status(tr('Unknown Command', 'default'));
	}
}