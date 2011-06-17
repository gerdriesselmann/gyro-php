<?php
/**
 * @defgroup CLEditor
 * @ingroup JavaScript
 * 
 * Include CLEditor (http://premiumsoftware.net/cleditor/)
 *
 * @section Usage
 *
 * On install, the module copies CLEditor files to the a folder named "js" below web root. The version
 * of CLEditor installed is 1.3.
 * 
 * CLEditor files are not included by default. To enable CLEditor on a page, place the following code
 * 
 * @code
 * Load::components('cleditor');
 * CLEditor::enable($page_data);
 * @endcode
 * 
 * This will turn all textareas with class "rte" into a CLEditor instance.
 * 
 * @note It is good practice to use "rte" as classname for rich text editors.
 *
 * @section Config Creating Configurations
 * 
 * If the default configuration is not sufficient, you may create your own. This is
 * a two step process:
 * 
 * - Create and name a config
 * - Refer to it by name when enabling editor
 * 
 * First, create a config:
 * 
 * @code
 * Load::components('cleditor');
 * $config CLEditor::create_config('fancy');
 * $config->init_file = 'js/fancy_cl.js'; // JS script to fire up editor
 * §config->plugins = array('js/cleditor/plugins/jquery.cleditor.table.js');
 * @endcode
 * 
 * You now can use the config anywhere:
 * 
 * @code
 * CLEditor::enable($page_data, 'fancy);
 * @endcode
 *
 * @section Notes Additional notes
 *
 * CLEditor is released under GPL 2, and MIT license.
 */
 
EventSource::Instance()->register(new JavascriptCLEditorEventSink());
