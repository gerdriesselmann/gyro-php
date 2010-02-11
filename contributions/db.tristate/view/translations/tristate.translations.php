<?php
/**
 * Tristate translations
 * 
 * @author Gerd Riesselmann
 * @ingroup Tristate
 */
function tristate_load_translations($languages) {
	return array(
		Tristate::YES => array(
			'en' => 'Yes',
			'de' => 'Ja'
		),
		Tristate::NO => array(
			'en' => 'No',
			'de' => 'Nein'
		),
		Tristate::UNKOWN => array(
			'en' => 'Unknown',
			'de' => 'Unbekannt'
		),
	);
}
