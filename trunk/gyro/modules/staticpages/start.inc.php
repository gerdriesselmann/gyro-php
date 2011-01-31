<?php
/**
 * @defgroup StaticPages
 * @ingroup Modules
 *  
 * Routes all templates in view/templates/[lang]/static
 * 
 * This allows simple creation of more or less static pages, like your privacy policy, an imprint etc.
 * 
 * Just drop you files in the 'static' subdirectory of the template folder. Creating subdirectoris is supported, files named
 * "index.tpl.php" will be routed as directory
 * 
 * Examples: 
 * 
 * \li The file app/view/templates/default/static/test.html.tpl.php will be served as /test.html
 * \li The file app/view/templates/default/static/subdir/test.php.tpl.php will be served as /subdir/test.php
 * \li The file app/view/templates/default/static/subdir/index.tpl.php will be served as /subdir/
 * \li The file app/view/templates/default/static/subdir/index.html.tpl.php will be served as /subdir/index.html
 * \li The file app/view/templates/default/static/test.html will be not be served at all, since it does not 
 *     end on ".tpl.php"
 *     
 * To access URLs of static pages use the action "static_[template_name]", where template name is the file name 
 * without the ".tpl.php" extension. So the above examples are access like this:
 * 
 * \li app/view/templates/default/static/test.html.tpl.php => static_test.html
 * \li app/view/templates/default/static/subdir/test.php.tpl.php => static_subdir/test.php.
 * \li app/view/templates/default/static/subdir/index.tpl.php => static_subdir/
 *   
 * Alternatively, you may map actions using the template name as a parameter named "page" to an action named 
 * "static". This is used for historical reasons mainly, and should be regarded as deprecated  
 */
if (!defined('STATICPAGES_PREPEND')) define('STATICPAGES_PREPEND', '');
if (!defined('STATICPAGES_APPEND')) define('STATICPAGES_APPEND', '');
