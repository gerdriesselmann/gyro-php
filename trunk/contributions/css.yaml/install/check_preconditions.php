<?php
function css_yaml_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('css.yaml') . 'www/' . Config::get_value(ConfigYAML::YAML_VERSION) . '/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('yaml'), SystemUpdateInstaller::COPY_OVERWRITE));
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('css'), SystemUpdateInstaller::COPY_NO_REPLACE));
	return $ret;
}
