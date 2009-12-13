= Console Module =

Author: Gerd Riesselmann

== Purpose ==

Invoke action through the command line   

== Usage ==

Copy install/run_console.php.example to your application directory and rename it to run_console.php.
If you invoke PHP other then through simply calling 'php', define CONSOLE_PHP_INVOCATION to suite your
needs.  

You now can invoke any action (if not forbidden) through 

	php app/run_console.php path/to/action param=value

The above call would be equivalent to call

	path/to/action?param=value

in a browser
  