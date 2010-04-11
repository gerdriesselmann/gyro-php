<?php
/**
 * @defgroup CKEditor
 * @ingroup JavaScript
 * 
 * Include CKEditor (http://ckeditor.com/) 
 *
 * @section Usage
 *
 * On install, the module copies CKEditor files to the a folder named "js/ckeditor" below web root. The version
 * of CKEditor installed is 3.2.
 * 
 * CKEditor files are not included by default. To enable CKEditor on a page, place the following code
 * 
 * @code
 * Load::components('ckeditor');
 * CKEditor::enable($page_data);
 * @endcode
 * 
 * This will turn all textareas with class "rte" into a CKEditor instance.
 * 
 * @note It is good practice to use "rte" as classname for rich text editors.
 *  
 * @attention 
 *   The above code only works if javascript.jquery module is enabled, too. If
 *   you don't user jQuery, you must create you own configuration.   
 * 
 * @section Config Creating Configurations
 * 
 * If the default configuration is not sufficent, you may create your own. This is
 * a two step process:
 * 
 * - Create and name a config
 * - Refer to it by name when enabling editor
 * 
 * First, create a config:
 * 
 * @code
 * Load::components('ckeditor');
 * $config CKEditor::create_config('fancy');
 * $config->init_file = 'js/fancy_ck.js'; // JS script to fire up editor
 * @endcode
 * 
 * You now can use the config anywhere:
 * 
 * @code
 * CKEditor::enable($page_data, 'fancy);
 * @endcode
 *
 * @section Notes Additional notes
 *
 * CKEditor is released under GPL, LGPL and MPL license.
 */
 
EventSource::Instance()->register(new JavascriptCKEditorEventSink());
