<?php
//COpy user controller 
function scheduler_install() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$ret->merge(
		SystemUpdateInstaller::copy_file_to_app(
			dirname(__FILE__) . '/scheduler.controller.php.example', 
			'controller/scheduler.controller.php', 
			SystemUpdateInstaller::COPY_NO_REPLACE
		)
	);
	return $ret;	
}