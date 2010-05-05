<?php
/**
 * EventSink to deal with system update
 */
class HijackAccountEventSink implements IEventSink {
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
		switch ($event_name) {
			case 'get_actions':
				$source = $event_params['source'];
				$contextes = array('view', 'list');
				if ($source instanceof DAOUsers && in_array($event_params['context'], $contextes)) {
					$result['hijack'] = tr('Hijack Account', 'hijackaccount'); 
				}
				break;
			case 'notifications_collect_sources':
				$result['usermanagement.hijackaccount'] = tr('usermanagement.hijackaccount', 'hijackaccount');
				break;
			case 'notifications_translate':
				if ($event_params == 'usermanagement.hijackaccount') {
					$result = tr('usermanagement.hijackaccount', 'hijackaccount');
				}
				break;
		}
	}
}
