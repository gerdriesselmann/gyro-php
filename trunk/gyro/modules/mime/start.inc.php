<?php
/**
 * @defgroup Mime
 * @ingroup Modules
 *  
 * Content views for any mime type, like images, PDF etc. 
 */


// Register our views
ViewFactory::set_implementation(new ViewFactoryMime());
