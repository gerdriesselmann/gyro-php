= Offline Module =

Author: Gerd Riesselmann
Depends: systemupdate, console
Requires: Write access to .htaccess file

== Purpose ==
 
Switch site offline, that is catch all requests and send a 503 header aling with a maintenance 
html page instead

== Usage ==

On install, the module copies a file named offline.php to the web root. This file can be changed 
to fit your needs, you may not - however - rely on any Gyro features, sind the framework will not
have been started (unless you explicitly do so in offline.php). Note that index.php does not get 
called, when the site is offline!

To switch your site offline simply invoke offline/off through the console. This may, e.g. look like 
this, but may differ on your local install:

    php app/run_console.php offline/off

To enable the site again call

    php app/run_console.php offline/on  

== Background ==

When switching a site offline, the .htaccess will be changed to redirect all requests to index.php - 
these are all request that do not serve physical files like images, JS or CSS - to offline.php. 

To prevent the content of offline.php to be indexed by search engines, offline.php sends a 
"503 Service Unavailable" header. 
