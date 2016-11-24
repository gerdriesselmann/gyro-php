<?php
/**
 * EventSink to enable Cookie Consent popup.
 *
 * This class should only be loaded, if a popup is wanted
 * 
 * @author Gerd Riesselmann
 * @ingroup CookieConsent
 */
class JavascriptCookieConsentEventSink implements IEventSink {
	/**
	 * Invoked to handle events
	 * 
	 * Events can be anything, and they are invoked through the router
	 * One event is "cron", it has no parameters
	 * 
	 * @param string Event name
	 * @param mixed Event parameter(s)
	 */
	public function on_event($event_name, $event_params, &$result) {
		if ($event_name == 'view_before_render') {
			$view = $event_params['view'];
			if ($view instanceof PageViewBase) {
				$page_data = $view->retrieve('page_data');
				if ($page_data instanceof PageData) {
					$msg = tr(Config::get_value(ConfigCookieConsent::MESSAGE), 'cookieconsent');
					$theme = Config::get_value(ConfigCookieConsent::THEME);
					$link = Config::get_value(ConfigCookieConsent::LINK);
					$dismiss = tr(Config::get_value(ConfigCookieConsent::DISMISS), 'cookieconsent');
					$learn_more = tr(Config::get_value(ConfigCookieConsent::LEARN_MORE), 'cookieconsent');
					/* @var PageData $page_data */
					$page_data->head->add_js_snippet(
						"window.cookieconsent_options = {'message':'$msg','dismiss':'$dismiss','learnMore':'$learn_more','link':'$link','theme':'$theme'};", true
					);
					$page_data->head->add_js_file('js/cookie-consent/cookieconsent.min.js');
				}
			}
		}
	}
}
