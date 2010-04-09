<?php
/**
 * EventSink to deal with JCSSManager events
 *
 * @author Gerd Riesselmann
 * @ingroup JQuery
 */
class JavascriptCKEditorEventSink implements IEventSink {
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
		if ($event_name == 'jcssmanager_compress') {
			switch($event_params) {
				case JCSSManager::TYPE_JS:
					$result['ckeditor'][] = 'js/ckeditor/ckeditor.js';
					if (Load::is_module_loaded('javascript.jquery')) {
						$result['ckeditor'][] = 'js/ckeditor/adapters/jquery.js';
					}
					break;
			}
		}
	}
}
