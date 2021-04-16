<?php
/**
 * @defgroup Offline
 * @ingroup Modules
 *  
 * Switch your site off- or online easily.
 * 
 * @attention
 *   Needs write access to the .htaccess file in the web root, and is 
 *   compatabile with Apache web server (and Lighttpd, eventually) only.
 */

Load::enable_module(['console', 'systemupdate']);
