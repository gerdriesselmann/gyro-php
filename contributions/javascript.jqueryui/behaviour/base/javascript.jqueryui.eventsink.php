<?php
/**
 * EventSink to catch JCSSManager events
 * 
 * @author Gerd Riesselmann
 * @ingroup JQueryUI
 */
class JavascriptJQueryUIEventSink implements IEventSink {
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
		if ($event_name == 'jcssmanager_compress' || $event_name == 'jcssmanager_collect') {
			switch($event_params) {
				case JCSSManager::TYPE_JS:
					if ($event_name == 'jcssmanager_collect' && JQueryUI::uses_cdn()) {
						$result[] = JQueryUI::get_cdn_url();
					}
					foreach(JQueryUI::get_js_paths(JQueryUI::get_enabled_components()) as $js) {
						$result[] = $js;
					}
					break;
				case JCSSManager::TYPE_CSS:
					foreach(JQueryUI::get_css_paths(JQueryUI::get_enabled_components()) as $css) {
						$result[] = $css;
					}
					break;					
			}
		}
	}
}
