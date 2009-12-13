<?php
function systemupdate_update_1() {
	$ret = new Status();
	$htaccess = Config::get_value(Config::URL_ABSPATH) . '.htaccess';
	if (file_exists($htaccess)) {
		$c = file_get_contents($htaccess);
		if (strpos($c, '### BEGIN OPTIONS') === false) {
			$c = str_replace('RewriteEngine On', "### BEGIN OPTIONS ###\n\n### END OPTIONS  ###\n\nRewriteEngine On", $c);
		}
		if (strpos($c, '### BEGIN REWRITE') === false) {
			$c = str_replace('RewriteBase /', "RewriteBase /\n\n### BEGIN REWRITE ###\n\n### END REWRITE   ###\n", $c);
		}
		if (file_put_contents($htaccess, $c) === false) {
			$ret->append('Could not update .htaccess');
		} 
	}
	else {
		$ret->append(".htaccess not found at $htaccess");
	}
	return $ret;
}
