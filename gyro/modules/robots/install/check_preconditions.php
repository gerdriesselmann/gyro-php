<?php
function robots_check_preconditions() {
	$ret = new Status();
	$webroot = Config::get_value(Config::URL_ABSPATH);
	if (file_exists($webroot . 'robots.txt')) {
		$ret->append('There is a robots.txt in webroot. Robots module will not work, unless you remove it!');
	}
	return $ret;
}
