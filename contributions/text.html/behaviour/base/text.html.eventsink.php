<?php
/**
 * EventSink to catch view rendering events
 * 
 * @author Gerd Riesselmann
 * @ingroup Html
 */
class TextHtmlEventSink implements IEventSink {
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
		if ($event_name == 'view_after_render') {
			$view = Arr::get_item($event_params, 'view', false);
			if ($view instanceof ContentViewBase) {
				HtmlText::apply_enabled_editors($view->get_page_data());
			}
		}
	}
}
