<?php
/**
 * EventSink to deal with postprocessing CSS by incoking postcss on the commandline
 * @author Gerd Riesselmann
 * @ingroup PostCSS
 */
class CSSPostCSSEventSink implements IEventSink {
	/**
	 * Invoked to handle events
	 *
	 * Events can be anything, and they are invoked through the router
	 * One event is "cron", it has no parameters
	 *
	 * @param string $event_name Event name
	 * @param mixed $event_params Event parameter(s)
	 * @param mixed $result Result of function, but may also be input.
	 * @return Status
	 */
	public function on_event($event_name, $event_params, &$result) {
		$ret = new Status();
		switch ($event_name) {
			case 'jcssmanager_concated':
				if (
					Config::has_feature(ConfigPostCSS::JCSSMANAGER_INTEGRATION) &&
					Load::is_module_loaded('jcssmanager') &&
					$event_params == JCSSManager::TYPE_CSS
				) {
					// Delegate to PostCSS main event
					$ret = EventSource::Instance()->invoke_event('postcss_process', null, $result);
				}
				break;

			case 'postcss_process':
				$tmp = '';
				$ret = PostCSS::process_content($result, $tmp);
				if ($ret->is_ok()) {
					$result = $tmp;
				}
				break;

			default:
				break;
		}
		return $ret;
	}
}
