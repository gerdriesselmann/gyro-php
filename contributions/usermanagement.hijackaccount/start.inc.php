<?php
/**
 * Hijack config options
 *
 * @author Gerd Riesselmann
 * @ingroup Binaries
 */
class ConfigHijackAccounts {
	/**
	 * If this module should integrate with notifications module
	 */
	const INTEGRATE_WITH_NOTIFICATIONS = 'HIJACK_ACCOUNTS_INTEGRATE_WITH_NOTIFICATIONS';
}


Config::set_feature_from_constant(
	ConfigHijackAccounts::INTEGRATE_WITH_NOTIFICATIONS,
	'APP_HIJACK_ACCOUNTS_INTEGRATE_WITH_NOTIFICATIONS',
	true
);

EventSource::Instance()->register(new HijackAccountEventSink());