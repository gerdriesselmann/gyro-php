<?php
/**
 * Load translation for countries
 * 
 * This is an example of how to use DB based translations
 * 
 * @author Gerd Riesselmann
 * @ingroup Countries
 */
function contact_load_translations($languages) {
	$ret = array(
		'Your Name:' => array(
			'de' => 'Ihr Name:'
		),
		'Your E-Mail Address:' => array(
			'de' => 'Ihre E-Mail-Adresse;'
		),
		'Subject:' => array(
			'de' => 'Titel:'
		),
		'Your Message:' => array(
			'de' => 'Ihre Nachricht:'
		),
		'You can leave a message using the contact form below.' => array(
			'de' => 'Sie können uns eine Nachricht über das folgende Kontakt-Formular schicken.'
		),
		'Contact' => array(
			'de' => 'Kontakt'
		),
		'Contact Us' => array(
			'de' => 'Schicken Sie uns eine Nachricht'
		),
		'Send us an e-mail through the contact form.' => array(
			'de' => 'Schicken Sie und ein Nachricht über das Kontaktformular.'
		),
		'Send' => array(
			'de' => 'Abschicken'
		),
		'Hello,' => array(
			'de' => 'Hallo,'
		),
		'%name (%email) send the following message using the contact form on %app:' => array(
			'de' => '%name (%email) hat folgenden Nachricht über das Kontaktformular auf %app gesendet:'
		),
		'Your message has been sent successfully.' => array(
			'de' => 'Ihre Nachricht wurde erfolgreich versandt.'
		),
		'Please provide a name.' => array(
			'de' => 'Bitte geben Sie Ihren Namen an.'
		),
		'Please provide an e-mail address.' => array(
			'de' => 'Bitte geben Sie eine E-Mail-Adress an.'
		),
		'Your e-mail address looks invalid.' => array(
			'de' => 'Die angegebene E-Mail-Adresse sieht nicht wie eine gültige Adresse aus.'
		),
		'The message should not be empty.' => array(
			'de' => 'Die Nachricht sollte nicht leer sein.'
		),
		'Contact Form Message' => array(
			'de' => 'Nachricht über das Kontakt-Formular'
		),
	);
	return $ret;
}
