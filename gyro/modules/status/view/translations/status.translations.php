<?php
/**
 * Status translations
 * 
 * @author Gerd Riesselmann
 * @ingroup Status
 */
function status_load_translations($languages) {
	return array(
		Stati::UNCONFIRMED => array(
			'en' => 'Unconfirmed',
			'de' => 'Unbestätigt'
		),
		Stati::ACTIVE => array(
			'en' => 'Active',
			'de' => 'Aktiv'
		),
		Stati::DISABLED => array(
			'en' => 'Disabled',
			'de' => 'Inaktiv'
		),
		Stati::DELETED => array(
			'en' => 'Deleted',
			'de' => 'Gelöscht'
		),
	);
}
			