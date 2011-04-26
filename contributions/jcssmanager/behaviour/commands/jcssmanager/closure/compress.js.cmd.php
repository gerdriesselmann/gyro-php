<?php
Load::commands('jcssmanager/base/compress.base');

class JCSSManagerCompressJSClosureCommand extends JCSSManagerCompressBaseCommand {
	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		if (count($in_files) > 0) {
			$ret->merge($this->run_closure($in_files, $out_file));
		}
		
		return $ret;
	}
	
	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return JCSSManager::TYPE_JS;
	}
	
	/**
	 * Invoke Closure Compiler
	 * 
	 * @param array $in_files
	 * @param string $out_file
	 * @return Status 
	 */
	protected function run_closure($in_files, $out_file) {
		$ret = new Status();
		
		$module_dir = Load::get_module_dir('jcssmanager');
		$options = '';
		$options .= ' --charset=' . GyroLocale::get_charset();
		$options .= ' --compilation_level=SIMPLE_OPTIMIZATIONS'; // WHITESPACE_ONLY, SIMPLE_OPTIMIZATIONS, ADVANCED_OPTIMIZATIONS
		$options .= ' --third_party=1';
		$options .= ' --warning_level=QUIET'; //  QUIET, DEFAULT, VERBOSE 
		foreach($in_files as $file) {
			if (substr($file, 0, 1) !== '/') {
				$file = Config::get_value(Config::URL_ABSPATH) . $file;
			}			
			$options .= ' --js=' . $file;
		}
		$options .= ' --js_output_file=' . $out_file;
		$cmd = 'java -jar ' . $module_dir . '3rdparty/closure/compiler.jar' . $options;
		
		Load::commands('generics/execute.shell');
		$shell = new ExecuteShellCommand($cmd);
		$ret->merge($shell->execute());
		
		return $ret;		
	} 
	
}