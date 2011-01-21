<?php
Load::enable_module('status');
Load::enable_module('console');

/**
 * @devgroup Scheduler
 * @ingroup Contributions
 * 
 * The scheduler allows (repeated) execution of arbitrary many tasks, as long as they can be 
 * invoked through the Gyro console module.
 * 
 * Tasks can be scheduled to be executed at a given date and time. 
 * 
 * Tasks can configured to be repeated a given number of times, even with different frequence.
 * Reschedule policies can be different in case of success or failure of a task.
 */

/**
 * Defines Scheduler Config Options
 * 
 * @author Gerd Riesselmann
 * @ingroup Scheduler
 *
 */
class ConfigScheduler {
	/** Whether to send mails on errors or not */
	const SEND_ERROR_MAIL = 'SCHEDULER_SEND_ERROR_MAIL';
}

Config::set_feature_from_constant(ConfigScheduler::SEND_ERROR_MAIL, 'APP_SCHEDULER_SEND_ERROR_MAIL', true);
