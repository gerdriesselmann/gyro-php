<?php
/**
 * Update sources counter for existing
 */
function jcssmanager_update_3() {
	$ret = new Status();

	Load::models('jcsscompressedfiles');
	$dao = new DAOJcsscompressedfiles();
	$dao->find();
	while($dao->fetch()) {
		$d = clone($dao);
		$d->num_sources = count($d->sources);
		$ret->merge($d->update());
	}
	return $ret;
}
