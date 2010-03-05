<?php
function javascript_wymeditor_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('javascript.wymeditor') . 'data/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('js'), SystemUpdateInstaller::COPY_OVERWRITE));
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('css'), SystemUpdateInstaller::COPY_NO_REPLACE));
	return $ret;
}
