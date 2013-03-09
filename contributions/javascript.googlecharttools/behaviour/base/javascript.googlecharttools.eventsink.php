<?php
/**
 * EventSink to catch JCSSManager events
 * 
 * @author Gerd Riesselmann
 * @ingroup GoogleChartTools
 */
class JavascriptGoogleChartToolsEventSink implements IEventSink {
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
				GoogleChartTools::prepare($page_data);
			}
		}
	}
}
