<?php
/**
 * EventSink to deal with JCSSManager events
 *
 * @author Gerd Riesselmann
 * @ingroup JQuery
 */
class JavascriptJQueryEventSink implements IEventSink {
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
					array_unshift($result, 'js/jquery.js');
					break;
			}
		}
	}
}
