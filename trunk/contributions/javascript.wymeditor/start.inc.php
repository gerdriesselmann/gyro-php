<?php
/**
 * @defgroup WYMEditor
 * @ingroup JavaScript
 * 
 * Include WYMEditor (http://www.wymeditor.org/) 
 *
 * @section Usage
 *
 * On install, the module copies wymeditor files to the a folder named "js/wymeditor" below web root. The version
 * of WYMEditor installed is 0.5, but tidy and fullscreen plugin are taken from trunk.
 * 
 * @see http://forum.wymeditor.org/forum/viewtopic.php?f=3&t=734
 * 
 * WYMEditor files are not included by default. To enable WYMEditor on a page, place the
 * following code
 * 
 * @code
 * Load::components('wymeditor');
 * WYMEditor::enable($page_data);
 * @endcode
 * 
 * This will turn all textareas with class "rte" into a WYMEditor instance.
 * 
 * @note It is good practice to use "rte" as classname for rich text editors.
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
 * Load::components('wymeditor');
 * $config WYMEditor::create_config('fancy');
 * $config->init_file = 'js/fancy_wym.js'; // JS script to fire up editor
 * // Add tidy plugin
 * $config->plugins['js/wymeditor/plugins/tidy/jquery.wymeditor.tidy.js'] = 'var wymtidy = wym.tidy();wymtidy.init();';
 * @endcode
 * 
 * You now can use the config anywhere:
 * 
 * @code
 * WYMEditor::enable($page_data, 'fancy);
 * @endcode
 *
 * Note you don't need to change the init file, if you add or remove plugins.
 *
 * @section Notes Additional notes
 *
 * WYMEditor is released under GPL and MIT license.
 * 
 */

EventSource::Instance()->register(new JavascriptWYMEditorEventSink());
