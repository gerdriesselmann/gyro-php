<?php
/**
 * @defgroup PhpInfo
 * @ingroup Modules
 *  
 * Adds link to phpinfo() to debug block 
 */


/**
 * Show PHPinfo
 * 
 * When enabled will place a link to phpinfo into the debug block, which
 * this controller also creates.
 * 
 * On live sites will do nothing.
 * 
 * @author Gerd Riesselmann
 * @ingroup PhpInfo
 */
class PhpinfoController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('phpinfo', $this, 'phpinfo')
		);
	}
	
	/**
	 * Show PHP Info
	 * 
	 * @param PageData $page_data
	 * @return void
	 */
	public function action_phpinfo(PageData $page_data) {
		print phpinfo();
		exit;
	}
	
	/**
	 * Invoked to handle events
	 */
	public function on_event($name, $params, &$result) {
		if ($name === 'debugblock' && $params === 'properties') {
			$result['PHP-Version'] = phpversion() . ', ' . WidgetActionLink::output('phpinfo()', 'phpinfo', '', array('target' => '_blank'));
		}		
	}
}