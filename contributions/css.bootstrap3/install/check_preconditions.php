<?php
function css_bootstrap3_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('css.bootstrap3') . 'data/' . Config::get_value(ConfigBootstrap3::VERSION) . '/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('js'), SystemUpdateInstaller::COPY_OVERWRITE));
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('fonts'), SystemUpdateInstaller::COPY_OVERWRITE));
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('css'), SystemUpdateInstaller::COPY_NO_REPLACE));
	return $ret;
}
