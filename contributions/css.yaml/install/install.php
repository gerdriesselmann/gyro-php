<?php
function css_yaml_install() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('css.yaml') . 'www/' . Config::get_value(ConfigYAML::YAML_VERSION) . '/';
	$ret->merge(SystemUpdateInstaller::copy_to_app('view/templates/default/', $root . 'template/', array('page.tpl.php'), SystemUpdateInstaller::COPY_OVERWRITE));
	return $ret;
}