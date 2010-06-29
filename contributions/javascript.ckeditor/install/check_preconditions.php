<?php
function javascript_ckeditor_check_preconditions() {
	$ret = new Status();
	Load::components('systemupdateinstaller');
	$root = Load::get_module_dir('javascript.ckeditor') . 'data/3.3/';
	$ret->merge(SystemUpdateInstaller::copy_to_webroot($root, array('js'), SystemUpdateInstaller::COPY_OVERWRITE));
	return $ret;
}
