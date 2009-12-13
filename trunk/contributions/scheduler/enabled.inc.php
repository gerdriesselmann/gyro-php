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