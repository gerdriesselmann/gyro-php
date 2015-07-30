<?php
/**
 * EventSink to catch JCSSManager events
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
			if (!Load::is_module_loaded('usermanagement') || !Users::is_logged_in()) {
				$view = $event_params['view'];
				if ($view instanceof PageViewBase) {
					$msg = Config::get_value(ConfigCookieConsent::MESSAGE);
					$theme = Config::get_value(ConfigCookieConsent::THEME);
					$link = Config::get_value(ConfigCookieConsent::LINK);
					$dismiss = Config::get_value(ConfigCookieConsent::DISMISS);
					$learn_more = Config::get_value(ConfigCookieConsent::LEARN_MORE);
					/* @var PageData $page_data */
					$page_data = $view->retrieve('page_data');
					$page_data->head->add_js_snippet(
						"window.cookieconsent_options = {'message':'$msg','dismiss':'$dismiss','learnMore':'$learn_more','link':'$link','theme':'$theme'};"
					);
					$page_data->head->add_js_file('//s3.amazonaws.com/cc.silktide.com/cookieconsent.latest.min.js');
				}
			}
		}
	}
}
