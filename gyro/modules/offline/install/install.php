<?php
function offline_install() {
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('offline') . 'www/';
	SystemUpdateInstaller::copy_to_webroot($root, array('offline.php'), SystemUpdateInstaller::COPY_NO_REPLACE);
}