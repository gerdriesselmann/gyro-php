= Scheduler Module =

Author: Gerd Riesselmann
Depends: console

== Purpose ==

Schedule console invoked actions. 

== Usage ==

On install, the module copies scheduler.controller.php to the controller directory
of your application. You then can modify specific scheduler behaviour to fit your
needs.

Use scheduler/process to invoke the next task - if any. 
use scheduler/watchdog to clean up things.  

== Additional notes ==

In case of an error, a mail will be send to the application admin mail address, 
defined in your config. Overload ProcessSchedulerCommand, if you don't want that.

