<?php
/** 
 * Compress fiels on each update
 */
function jcssmanager_check_postconditions() {
	return JCSSManager::collect_and_compress();
}
