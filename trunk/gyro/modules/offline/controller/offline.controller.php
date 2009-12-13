<?php
/**
 * Actions for setting page on- or offline
 * 
 * Run offline/off or offline/on on the command line using the 
 * Console module to switch a site off or online.
 * 
 * The controller modifed the .htaccess file and redirects
 * all requests to /offline.php in the web root.
 * 
 * @author Gerd Riesselmann
 * @ingroup Offline
 */
class OfflineController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('offline/off', $this, 'offline_off', new ConsoleOnlyRenderDecorator()),
			new ExactMatchRoute('offline/on', $this, 'offline_on', new ConsoleOnlyRenderDecorator()),
		);
	}	
	
	/**
	 * Switch site offline
	 */
	public function action_offline_off(PageData $page_data) {
		Load::components('systemupdateinstaller');
		SystemUpdateInstaller::modify_htaccess(
			'offline', 
			SystemUpdateInstaller::HTACCESS_REWRITE, 
			array(
				'RewriteCond %{REQUEST_URI} ^/index.php',
				'RewriteRule ^(.*)$ offline.php [L,QSA]'
			)
		);
	}

	/**
	 * Switch site offline
	 */
	public function action_offline_on(PageData $page_data) {
		Load::components('systemupdateinstaller');
		SystemUpdateInstaller::modify_htaccess(
			'offline', 
			SystemUpdateInstaller::HTACCESS_REWRITE, 
			''
		);
	}
}
