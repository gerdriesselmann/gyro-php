<?php
function javascript_jquery_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('javascript.jquery') . 'data/' . Config::get_value(ConfigJQuery::JQUERY_VERSION) . '/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('js'), SystemUpdateInstaller::COPY_OVERWRITE));
	return $ret;
}
