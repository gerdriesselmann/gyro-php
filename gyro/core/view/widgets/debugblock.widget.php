<?php
/**
 * Debug output
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class WidgetDebugBlock implements IWidget {
	public static function output() {
		$w = new WidgetDebugBlock();
		return $w->render();
	}
	
	public function render($policy = self::NONE) {
		$out = '';
		if (Config::has_feature(Config::TESTMODE)) {
		   	$endtime = microtime(true);
			$modules = Load::get_loaded_modules();
		   	$debugs = array(
		  		'Memory' => String::number(memory_get_usage()/1024, 2) . ' KB',
		  		'Memory Peak' => String::number(memory_get_peak_usage()/1024, 2) . ' KB',
		  		'Execution time' => String::number($endtime - APP_START_MICROTIME, 2) . ' sec',
		   		'DB connect time' => String::number(DB::$db_connect_time, 4) . ' sec',
		  		'DB-Queries execution time' => String::number(DB::$queries_total_time, 4) . ' sec',
				'PHP-Version' => phpversion(),
				'Generated' => GyroDate::local_date(time()),
		   		'Modules' => (count($modules) > 0) ? implode(', ', $modules) : '-' 
	  		);
	  		
	  		// Alow modules extending props
	  		EventSource::Instance()->invoke_event('debugblock', 'properties', $debugs);
	  		
	  		// Output
		  	$li = array();
			foreach($debugs as $key => $value) {
				$li[] = html::b($key . ':') . ' ' . $value;
			}
			$out .= html::h('Debug Block', 2);
			$out .= html::li($li);
						
			if (count(DB::$query_log)) {
				$out .= html::h('Queries', 3);
				$li = array();
				foreach(DB::$query_log as $query) {
					$cls = $query['success'] ? 'query ok' : 'query error';
					$text  = String::escape($query['query']);
					$text .= ' - ';
					$text .= html::b(String::number($query['seconds'], 4) . ' sec');
					if ($query['message']) {
						$text .= ' - ';
						$text .=  html::span(String::escape($query['message']), $cls);
					}
					$li[] = $text;				
				}
				$out .= html::li($li, 'queries');
			}
					
			$out = html::div($out, 'debug_block');
		}
		return $out;	
	}
}
