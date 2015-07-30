<?php
/**
 * Load translation for cookie consent dialog
 *
 * @author Gerd Riesselmann
 * @ingroup CookieConsent
 */
function cookieconsent_load_translations($languages) {
	$ret = array(
		'Cookies help us deliver our services. By using our services, you agree to our use of cookies.' => array(
			'de' => 'Cookies erleichtern die Bereitsstellung unserer Dienste. Mit der Nutzung unserer Dienste erklären Sie sich damit einverstanden, dass wir Cookies verwenden.'
		),
		'Got it!' => array(
			'de' => 'OK'
		),
		'More info' => array(
			'de' => 'Weitere Informationen'
		),
	);
	return $ret;
}
