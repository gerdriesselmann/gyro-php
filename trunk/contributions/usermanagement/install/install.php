<?php
//COpy user controller 
function usermanagement_install() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$ret->merge(
		SystemUpdateInstaller::copy_file_to_app(
			dirname(__FILE__) . '/users.controller.php.example', 
			'controller/users.controller.php', 
			SystemUpdateInstaller::COPY_NO_REPLACE
		)
	);
	return $ret;	
}