<?php
/**
 * Class to instantiate only the systemupdate controller
 *
 * Useful if bootstraping an existing application, where controller
 * may rely on tables that are not yet installed
 *  
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class SystemUpdateControllerClassInstantiater implements IClassInstantiater {
	public function get_all() {
	    require_once __DIR__ . '/../systemupdate.controller.php';
		return array(
		    new SystemupdateController()
        );
	}
}
