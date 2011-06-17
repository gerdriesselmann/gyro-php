<?php
function javascript_cleditor_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('javascript.cleditor') . 'data/1.3/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('js'), SystemUpdateInstaller::COPY_OVERWRITE));
	return $ret;
}
