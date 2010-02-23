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
			$out .= html::h('Debug Block', 2);
			$out .= $this->render_properties();
			
			$sections = array(
				'DB Queries' => $this->render_db_queries(),
				'Templates'  => $this->render_templates(),
			);
			// Alow modules extending sections
  			EventSource::Instance()->invoke_event('debugblock', 'sections', $sections);
			
			foreach($sections as $heading => $content) {
				$out .= html::h(String::escape($heading), 3);
				$out .= $content;
			}
		}
		return $out;
	}

	protected function render_properties() {
	   	$endtime = microtime(true);
		$modules = Load::get_loaded_modules();
		$debugs = array(
	  		'Memory' => String::number(memory_get_usage()/1024, 2) . ' KB',
	  		'Memory Peak' => String::number(memory_get_peak_usage()/1024, 2) . ' KB',
	  		'Execution time' => $this->duration($endtime - APP_START_MICROTIME),
	   		'DB-Queries execution time' => $this->duration(DB::$queries_total_time),
			'DB connect time' => $this->duration(DB::$db_connect_time),
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
		return html::li($li);
	}

	protected function render_db_queries() {
		$out = '';
		// Query Logs
		if (count(DB::$query_log)) {
			$table = '';
			foreach(DB::$query_log as $query) {
				$table .= $this->render_db_query_times($query);
				$table .= $this->render_db_query_message($query);
				$table .= $this->render_db_query_explain($query);
			}
			$out .= html::tag('table', $table, array('summary' => 'List of all issued DB queries'));
		}
		return $out;
	}
	
	protected function render_db_query_times($query) {
		$is_slow = ($query['seconds'] > Config::get_value(Config::DB_SLOW_QUERY_THRESHOLD));
		$cls = $query['success'] ? 'query ok' : 'query error';
		
		$query_time = array(
			$this->sec($query['seconds']),
			$this->msec($query['seconds'])
		);
		if ($is_slow) {
			$cls .= ' slow';
			$query_time[] = 'Slow!';
		}
		
		return html::tr(
			array(
				html::td(html::b(implode('<br />', $query_time))),
				html::td(String::escape($query['query'])),							
			),
			array('class' => $cls)
		);
	}
	
	protected function render_db_query_message($query) {
		$ret = '';
		if ($query['message']) {
			$ret .= html::tr(
				html::td(String::escape($query['message']), array('colspan' => 2)),
				array('class' => 'query message')
			);
		}
		return $ret;
	}
	
	protected function render_db_query_explain($query) {
		$ret = '';
		$is_slow = ($query['seconds'] > Config::get_value(Config::DB_SLOW_QUERY_THRESHOLD));
		if ($is_slow) {
			$sql = $query['query'];
			$result = DB::explain($query['query'], $query['connection']);
			if ($result) {
				$ret .= html::tr(
					html::td($this->render_db_query_explain_result($result), array('colspan' => 2)),
					array('class' => 'query explain')
				);
			}
		}
		return $ret;
	}
	
	protected function render_db_query_explain_result(IDBResultSet $result) {
		$rows = array();
		$head = false;
		while($row = $result->fetch()) {
			if ($head === false) {
				$head = array();
				foreach(array_keys($row) as $h) {
					$head[] = html::td(String::escape($h), array(), true);
				}
			}
			
			$tr = array();
			foreach($row as $col => $value) {
				$tr[] = html::td(String::escape($value));	
			}
			$rows[] = $tr;
		}
		$table = html::table($rows, $head, 'Explain Result', array('class' => 'full'));
		return $table;
	}
	
	protected function render_templates() {
		$out = '';
		// Template logs
		if (count(TemplatePathResolver::$resolved_paths)) {
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
		return $out;
	}

	protected function render_translations() {
		$out = '';
		// Translation logs
		if (count(Translator::Instance()->groups)) {
			$out .= html::h('Translations', 3);
			
			$li = array(); 
			foreach(Translator::Instance()->groups as $key => $group) {
				if (count($group)) {
					$li[] = String::escape($key);
				}
			}
			$out .= html::li($li, 'translations');
		}
		return $out;	
	}
	
	protected function sec($sec) {
		return String::number($sec, 4) . '&nbsp;sec';
	}

	protected function msec($sec) {
		return String::number($sec * 1000, 2) . '&nbsp;msec';
	}
	
	protected function duration($sec) {
		return $this->sec($sec) . ' - ' . $this->msec($sec);
	}
}
