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
						
			// Query Logs
			if (count(DB::$query_log)) {
				$out .= html::h('Queries', 3);
				
				$table = '';
				foreach(DB::$query_log as $query) {
					$cls = $query['success'] ? 'query ok' : 'query error';
					$time_append = '';
					if ($query['seconds'] > Config::get_value(Config::DB_SLOW_QUERY_THRESHOLD)) {
						$cls .= ' slow';
						$time_append = '<br />Slow!';
					}
					$table .= html::tr(
						array(
							html::td(html::b(String::number($query['seconds'], 4) . '&nbsp;sec') . $time_append),
							html::td(String::escape($query['query'])),							
						),
						array('class' => $cls)
					);
					if ($query['message']) {
						$table .= html::tr(html::td(String::escape($query['message']), array('colspan' => 2)));
					}
				}
				$out .= html::tag('table', $table, array('summary' => 'Lsit of all issued DB queries'));
			}
					
			// Template logs
			if (count(TemplatePathResolver::$resolved_paths)) {
				$out .= html::h('Templates', 3);
				
				$table = ''; 
				foreach(TemplatePathResolver::$resolved_paths as $resource => $file) {
					$cls = $file ? 'template ok' : 'template error';
					$table .= html::tr(
						array(
							html::td(String::escape($resource)),
							html::td(String::escape($file)),
						),
						array('class' => $cls)
					);
				}
				$out .= html::tag('table', $table, array('summary' => 'Mapping of template ressources to files'));
			}

			$out = html::div($out, 'debug_block');
		}
		return $out;	
	}
}
