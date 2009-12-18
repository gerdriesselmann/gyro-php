<?php
function staticmainpage_install() {
	$ret = new Status();
	Load::components('systemupdateinstaller');

	$root = Load::get_module_dir('staticmainpage') . 'view/templates/default/';
	$ret->merge(SystemUpdateInstaller::copy_to_app('view/templates/default/', $root, array('index.tpl.php'), SystemUpdateInstaller::COPY_NO_REPLACE));
	return $ret;
}