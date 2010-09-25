<?php
require_once dirname(__FILE__) . '/../simple/templateengine.simple.php';

/**
 * Template engines that introduces new syntax
 * 
 * @code
 * <?={statement}[;]?>
 * @endcode
 * 
 * will get translated to 
 * 
 * <?php print String::escape({statement})[;]?>
 * 
 * Examples:
 * 
 * @code
 * <?=$var; ?> becomes <?php print String::escape($var); ?>
 * <?=ActionMapper::action_url('view', $item)?> becomes <?php print String::escape(ActionMapper::action_url('view', $item))?>
 * <?=$a; print $b; ?> becomes <?php print String::escape($a); print $b; ?>
 * @endcode
 * 
 * Additionally you can include templates using this syntax:
 * 
 * @code
 * <?php gyro_include_template({template_name}) ?>
 * @encode
 * 
 * @attention The statement "gyro_include_template" must be enclosed by <?php and ?>!
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class TemplateEngineCore extends TemplateEngineSimple {
	/**
	 * Resolve template path
	 *
	 * @param string $file
	 * @return string
	 */
	protected function resolve_path($file) {
		$file_path = parent::resolve_path($file); 
		$compile_path = $this->get_compile_name($file_path);
		if (!file_exists($compile_path)) {
			$this->compile($compile_path, $file_path);
		}
		else if (filemtime($compile_path) < filemtime($file_path)) {
			$this->compile($compile_path, $file_path);
		}
		return $compile_path;
	}
	
	protected function get_compile_name($file) {
		$path = str_replace('.tpl.php', '', $file);
		foreach(Load::get_base_directories() as $dir) {
			$path = str_replace($dir, '', $path); 
		}
		$path = str_replace('view/templates/', '', $path);
		$pos = strpos($path, '/');
		$path = substr($path, $pos + 1);
		return Config::get_value(Config::TEMP_DIR) . 'view/templates_c/' . str_replace('/', '-', $path) . '-' . md5($file) . '.tpl-c.php';
	}
	
	protected function compile($compile_path, $source_path) {
		$c = file_get_contents($source_path);

		// Resolve includes
		$c = $this->resolve_includes($c);	
		
		// Repalce <?=  with <?php print String:.escape()
		$c = $this->resolve_quick_tags($c);
		file_put_contents($compile_path, $c);
	}
	
	/**
	 * Resolve quick tags <?=...?>
	 * 
	 * Replaces <?=...  with <?php print String::escape(...)
	 * 
	 * @param string $content
	 * @return string
	 */ 
	protected function resolve_quick_tags($content) {
		$regex = '@(<\?=)(.*?)(;|\?>)@';
		$rep = '<?php print String::escape($2)$3';
		return String::preg_replace($regex, $rep, $content);		
	}
	
	/**
	 * Resolve gyro_include_template('path')
	 *
	 * @param string $content
	 * @return string
	 */
	protected function resolve_includes($content) {
		$regex = '@(<\?php(.*?))gyro_include_template\((.*?)\)(.*?)(\?>)@';
		$matches = array();
		while (preg_match($regex, $content, $matches)) {
			//$include_file = trim($matches[3], '"\'');
			//$include_file_path = parent::resolve_path($include_file);
			//if (!file_exists($include_file_path)) {
			//	throw new Exception(tr('Include Template File Not Found: %t', 'core', array('%t' => $include_file)));
			//}
			//$rep = '';
			$rep = '$1include($this->resolve_path($3))$4$5';
			$content = preg_replace($regex, $rep, $content, 1);
		}
		
		return $content;
	}
}
