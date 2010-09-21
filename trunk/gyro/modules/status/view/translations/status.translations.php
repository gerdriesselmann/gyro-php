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
		'Change to ' . Stati::UNCONFIRMED => array(
			'en' => 'Change to Unconfirmed',
			'de' => 'Als Unbestätigt markieren'
		),
		'Change to ' . Stati::ACTIVE => array(
			'en' => 'Activate',
			'de' => 'Aktivieren'
		),
		'Change to ' . Stati::DISABLED => array(
			'en' => 'Disable',
			'de' => 'Deaktivieren'
		),
		'Change to ' . Stati::DELETED => array(
			'en' => 'Delete',
			'de' => 'Löschen'
		),
		'The status has been changed' => array(
			'de' => 'Der Status wurde geändert.'
		),		 
	);
}
			