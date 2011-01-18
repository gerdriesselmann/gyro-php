<?php
/**
 * @defgroup StaticPageData
 * @ingroup Page
 *  
 * Keeps the PageData of last content view, so it can be accessed without passing it around
 * 
 * @code
 * $page_data = StaticPageData::data();
 * @endcode
 */

// Register our views
ViewFactory::set_implementation(new ViewFactoryStaticPageData());
