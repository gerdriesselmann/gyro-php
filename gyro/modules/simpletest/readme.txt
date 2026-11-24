= Simpletest Module =

Author: Gerd Riesselmann

== Purpose ==

Wraps simpletest unit test library 

== Usage ==

Place your tests in a directory "simpletests" directly beneath the module or application root.
They will be automatically run when simpletest/run is invoked.

There are two test runners available:

* simpletest/run - Run all tests
* simpletest/run/{filter} - Run tests whose file paths match the filter as a substring or regular expression.

Additionally, there is a test runner for sending test emails:

* simpletest/send_test_mail - Send test emails to the configured mail administrator.

All test runners can be invoked via browser or command line, e.g.

```
php app/run_console.php simpletest/run
```

== Additional notes ==

Simpletest (http://www.simpletest.org/) is released under GNU LESSER GENERAL PUBLIC LICENSE. 
See 3rdparty/simpletest/LICENSE.
