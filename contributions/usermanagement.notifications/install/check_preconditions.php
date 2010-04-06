<?php
function usermanagement_notifications_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('usermanagement.notifications') . 'data/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('js', 'css'), SystemUpdateInstaller::COPY_OVERWRITE));	
	return $ret;
}
