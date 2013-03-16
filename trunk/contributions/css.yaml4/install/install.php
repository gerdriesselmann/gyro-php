<?php
function css_yaml4_install() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	
	// Check if there is a YAML file installed already
	$do_copy = true;
	try {
		$current_page_tpl = TemplatePathResolver::resolve('page');
		$c = file_get_contents($current_page_tpl);
		if (strpos($c, '##Installed by CSS.YAML##') !== false) {
			$do_copy = false;
		}
	}
	catch (Exception $ex) {
		// No template found... Ignore
	}
	
	if ($do_copy) {
		$root = Load::get_module_dir('css.yaml4') . 'data/' . Config::get_value(ConfigYAML::YAML_VERSION) . '/';
		$ret->merge(SystemUpdateInstaller::copy_to_app('view/templates/default/', $root . 'template/', array('page.tpl.php'), SystemUpdateInstaller::COPY_OVERWRITE));
	}
	return $ret;
}