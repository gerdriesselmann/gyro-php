<?php
/**
 * @defgroup JQuery
 * @ingroup JavaScript
 * 
 * Include JQuery (http://jquery.com/) on all pages 
 *
 * @section Usage
 *
 * On install, the module copies jquery.js to the a folder named "js" below web root. This file will
 * get included on all pages, if JCSSManager WidgetJCSS is used. It also will be compressed by JCSSManager, 
 * if compression is enabled.
 *
 * @section Notes Additional notes
 *
 * JQuery is released under MIT license.
 */

EventSource::Instance()->register(new JavascriptJQueryEventSink());