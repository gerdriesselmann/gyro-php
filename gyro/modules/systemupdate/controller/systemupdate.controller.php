<?php
/**
 * Controller for system update calls
 * 
 * This controller defines only one route: systemupdate. It an be invokes
 * through the command line using the Console module or through the web browser.
 * In later case you need to define SYSTEMUPDATE_PWD, a password the user has to
 * provide to execute the update
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class SystemupdateController extends ControllerBase {
	/**
	 * Returns array of IRoute this controller takes responsibility
	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('https://systemupdate', $this, 'systemupdate_update', new NoCacheCacheManager()),
		);
	}
	
	/**
	 * Update System
	 *
	 * @param PageData $page_data
	 */
	public function action_systemupdate_update(PageData $page_data) {
		if (class_exists('Console') && Console::is_console_request()) {
			$this->do_update_console($page_data);	
		}
		else if (defined('SYSTEMUPDATE_PWD')) {
			Load::tools('formhandler');
			$formhandler = new FormHandler('frmsystemupdate');
			if ($page_data->has_post_data()) {
				// Execute Update
				$this->do_update_form($formhandler, $page_data);	  
			}
			else {
				$view = ViewFactory::create_view(IViewFactory::CONTENT, 'core::systemupdate/auth', $page_data);
				$formhandler->prepare_view($view);
				$view->render();
			}
		}
		else {
			return CONTROLLER_NOT_FOUND;
		}
	}
	
	/**
	 * Actually do the updates
	 *
	 * @param FormHandler $formhandler
	 * @param PageData $pagedata
	 */
	protected function do_update_form(FormHandler $formhandler, PageData $page_data) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			if ($page_data->get_post()->get_item('a') == SYSTEMUPDATE_PWD) {
				$logs = $this->execute_updates();
		
				$view = ViewFactory::create_view(IViewFactory::CONTENT, 'systemupdate/log', $page_data);
				$view->assign('logs', $logs);
				$view->render();
				return;	
			}
			else {
				$err->append(tr('Sorry, try again', 'systemupdate'));
			}			
		}
		$formhandler->finish($err);
	}
	
	/**
	 * Actually do the updates, invoked from commandline
	 */
	protected function do_update_console($page_data) {
		$logs = $this->execute_updates();
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'systemupdate/log_console', $page_data);
		$view->assign('logs', $logs);
		$view->render();
	}
	
	/**
	 * Execute updates 
	 *
	 * @return array Array of log entries
	 */
	protected function execute_updates() {
		Load::models('systemupdates');
		$ret = $this->check_systemupdate_is_uptodate();
		$dirs = Load::get_base_directories(Load::ORDER_DECORATORS);
		foreach($dirs as $dir) {
			$component = $this->extract_component($dir);
			$err_prerequisites = $this->check_component_preconditions($component, $dir);
			if ($err_prerequisites->is_ok()) {
				$update_entry = SystemUpdates::get($component);
				if ($update_entry === false) {
					// Install
					$ret = array_merge($ret, $this->install_component($component, $dir, $update_entry));
				}
				if ($update_entry) {
					$ret = array_merge($ret, $this->update_component($component, $dir, $update_entry));
				}
			}
			else {
				$ret[] = $this->create_log_entry($component, 'preconditions', $err_prerequisites);				
			}
		}
		foreach($dirs as $dir) {
			$component = $this->extract_component($dir);
			$ret = array_merge($ret, $this->check_component_postconditions($component, $dir));
		}
		
		// Always clear cache!
		Load::commands('generics/clearcache');
		$cmd = new ClearCacheCommand(null);
		$cmd->execute();			

		return $ret;
	}
		
	/**
	 * Check if systemupdate module itself is installed 
	 *
	 * @return array
	 */
	protected function check_systemupdate_is_uptodate() {
		$ret = array();
		$component = 'systemupdate';
		$dir = dirname(__FILE__) . '/../';
		try {
			$update_entry = SystemUpdates::get($component);
			if ($update_entry === false) {
				// Install
				$ret = array_merge($ret, $this->install_component($component, $dir, $update_entry));
			}
		}
		catch (Exception $ex) {
			// No table: Install
			$ret = $this->install_component($component, $dir, $update_entry);
		}
		$ret = array_merge($ret, $this->update_component($component, $dir, $update_entry));
		return $ret;
	}
	
	/**
	 * Execute php file to check preconditions
	 *
	 * @param string $component
	 * @param string $dir
	 * @return Status
	 */
	protected function check_component_preconditions($component, $dir) {
		$check_php = $dir . 'install/check_preconditions.php';
		$err = new Status();
		// Check for check_preconditions.php and try to execute function {component}_check_preconditions()
		if (file_exists($check_php)) {
			$func = $this->make_func_name($component, 'check_preconditions'); 
			$err->merge($this->execute_php_script($check_php, $func));  
		}
		
		return $err;		
	}
	
	/**
	 * Execute php file after update
	 *
	 * @param string $component
	 * @param string $dir
	 * @return Status
	 */
	protected function check_component_postconditions($component, $dir) {
		$ret = array();
		
		$check_php = $dir . 'install/check_postconditions.php';
		$err = new Status();
		// Check for check_postconditions.php and try to execute function {component}_check_postconditions()
		if (file_exists($check_php)) {
			$func = $this->make_func_name($component, 'check_postconditions'); 
			$err->merge($this->execute_php_script($check_php, $func));
			$ret[] = $this->create_log_entry($component, 'check postconditions', $err);  
		}
		
		return $ret;		
	}
	
	/**
	 * Execute install scripts and create entry in system update tables 
	 *
	 * @param string $component
	 * @param string $dir
	 * @param DAOSystemupdate $update_entry
	 * @return array
	 */
	protected function install_component($component, $dir, &$update_entry) {
		$install_dir = $dir . 'install/';
		$install_sql = $install_dir . 'install.sql';
		$install_php = $install_dir . 'install.php';
		
		$ret = array();
		$err = new Status();
		// Check for install SQL and execute if found
		if (file_exists($install_sql)) {
			$err->merge($this->execute_sql_script($install_sql));
			$ret[] = $this->create_log_entry($component, $install_sql, $err); 
		}
		// Check for install.php and try to execute function {component}_install()
		if ($err->is_ok() && file_exists($install_php)) {
			$func = $this->make_func_name($component, 'install'); // app_install, core_install, {modulename}_install
			$err = $this->execute_php_script($install_php, $func);  
			$ret[] = $this->create_log_entry($component, $install_php, $err); 			
		}

		if ($err->is_ok()) {
			$err->merge(SystemUpdates::create($component, $update_entry));
		}
		
		$ret[] = $this->create_log_entry($component, 'install', $err);
		return $ret;
	}
	
	/**
	 * Run Updates for component 
	 *
	 * @param string $component
	 * @param string $dir
	 * @param DAOSystemupdates $update_entry
	 * @return array
	 */
	protected function update_component($component, $dir, $update_entry) {
		$updates_dir = $dir . 'install/updates/';
		$ret = array();
		if (!file_exists($updates_dir)) {
			return $ret;
		}

		$err = new Status();
		$files = scandir($updates_dir);		
		// Step through all files
		foreach($files as $file) {
			$version = intval($file);
			if ($version > $update_entry->version) {
				// Execute file
				$err->merge($this->update_from_file($updates_dir . $file, $component, $version));
				$ret[] = $this->create_log_entry($component, $file, $err); 
				if ($err->is_ok()) {
					$update_entry->version = $version;
					$err->merge($update_entry->update());						 
				}
			}
			if ($err->is_error()) {
				break;
			}
		}
		
		// Create Log entry, if something was actually done
		if (count($ret) > 0) {
			$ret[] = $this->create_log_entry($component, 'updates', $err);
		}
		
		return $ret;
	}
	
	/**
	 * Execute update file
	 *
	 * @param string $file
	 * @param int $version
	 * @return Status
	 */
	protected function update_from_file($file, $component, $version) {
		$ret = new Status();
		$fileinfo = pathinfo($file);
		$extension = strtolower(Arr::get_item($fileinfo, 'extension', ''));
		switch ($extension) {
			case 'sql':
				$ret->merge($this->execute_sql_script($file));				
				break;
			case 'php':
				$func = $this->make_func_name($component, 'update_' . $version);
				$ret->merge($this->execute_php_script($file, $func));
				break;
			default:
				$ret->append(tr('Could not process file: Unknown file type', 'systemupdate'));
				break;
		}
		return $ret;		
	}
	
	/**
	 * Execute SQL file
	 *
	 * @param string $file
	 * @return Status
	 */
	protected function execute_sql_script($file) {
		$ret = new Status();
		try {
			$ret->merge(DB::execute_script($file));
		}
		catch (Exception $ex) {
			$ret->merge($ex);
		}
		return $ret;
	}
	
	/**
	 * Run PHP script
	 *
	 * @param string$file
	 * @param string $func Function to call
	 * @return Status
	 */
	protected function execute_php_script($file, $func) {
		$ret = new Status();
		require_once($file);
		if (function_exists($func)) {
			$ret->merge($func());
		}
		else {
			// No function found
			$ret->append(tr('Function %func not found!', 'systemupdate', array('%func' => $func)));
		}
		return $ret;		
	}
	
	/**
	 * Create a log entry
	 *
	 * @param string $component
	 * @param string $task
	 * @param Status $err
	 * @return array
	 */
	protected function create_log_entry($component, $task, $err) {
		return array(
			'component' => $component,
			'task' => $task,
			'status' => $err
		); 
	}
	
	/**
	 * Extract component name (module, core, app)
	 *
	 * @param string $dir
	 * @return string
	 */
	protected function extract_component(&$dir) {
		// last element in path
		$tmp = explode('/', trim($dir, '/')); 
		$ret = array_pop($tmp);
		if  ($ret === 'core') { 
			$dir .= '../'; // We pass core install as gyro_path/core/../
		}
		return $ret;
	}
	
	protected function make_func_name($component, $func_postfix) {
		return String::plain_ascii($component, '_', true) . '_' . $func_postfix;
	}
}
