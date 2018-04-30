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
			$enabled = Config::has_feature(ConfigCookieConsent::ENABLED);
			if ($enabled && $view instanceof PageViewBase) {
				$page_data = $view->retrieve('page_data');
				if ($page_data instanceof PageData) {
					gyro_javascript_cookie_consent_activate($page_data);
				}
			}
		}
	}
}
