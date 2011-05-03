<?php
/**
 * EventSink to deal with JCSSManager events
 *
 * @author Gerd Riesselmann
 * @ingroup JQuery
 */
class JavascriptCKEditorEventSink implements IEventSink {
	/**
	 * Invoked to handle events
	 * 
	 * Events can be anything, and they are invoked through the router
	 * One event is "cron", it has no parameters
	 * 
	 * @param string Event name
	 * @param mixed Event parameter(s)
	 */
	public function on_event($event_name, $event_params, &$result) {
		if ($event_name == 'jcssmanager_compress') {
			switch($event_params) {
				case JCSSManager::TYPE_JS:
					Load::components('ckeditor');
					// Create a compressed file for each config
					foreach(CKEditor::get_all_configs() as $name => $config) {
						$compressed_name = 'ckeditor.' . $name;
						$result[$compressed_name][] = 'js/ckeditor/ckeditor.js';
						if (Load::is_module_loaded('javascript.jquery')) {
							$result[$compressed_name][] = 'js/ckeditor/adapters/jquery.js';
						}
						$result[$compressed_name][] = $config->init_file;
					}
					break;
			}
		}
	}
}
