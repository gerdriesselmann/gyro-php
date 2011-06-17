<?php
/**
 * EventSink to deal with JCSSManager events
 *
 * @author Gerd Riesselmann
 * @ingroup CLEditor
 */
class JavascriptCLEditorEventSink implements IEventSink {
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
					Load::components('cleditor');
					// Create a compressed file for each config
					foreach(CLEditor::get_all_configs() as $name => $config) {
						$compressed_name = 'cleditor.' . $name;
						if ($config->lang) {
							$result[$compressed_name][] = 'js/cleditor/lang/jquery.cleditor.' . strtolower($config->lang) . '.js';
						}						
						$result[$compressed_name][] = 'js/cleditor/jquery.cleditor.js';
						foreach($config->plugins as $p) {
							$result[$compressed_name][] = $p;
						}
						$result[$compressed_name][] = $config->init_file;
					}
					break;
				case JCSSManager::TYPE_CSS:
					$result[] = 'js/cleditor/jquery.cleditor.css';
					break;
			}
		}
	}
}
