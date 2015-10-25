<?php
Load::commands('jcssmanager/base/compress.base');

class JCSSManagerCompressBaseConcatCommand extends JCSSManagerCompressBaseCommand {
	/**
	 * COmpress given files 
	 * 
	 * @return Status
	 */
	protected function compress($in_files, $out_file, &$files_to_unlink) {
		$ret = new Status();
		$ret->merge($this->concat($in_files, $out_file, $ret));
		return $ret;
	}

	/**
	 * Concat the gicen files into one
	 * 
	 * @param array $arr_files
	 * @param Status $err
	 * @return string Created concatenation file
	 */
	protected function concat($arr_files, $out_file) {
		$err = new Status();
		$handle = fopen($out_file, 'w');
		if (empty($handle)) {
			$err->merge('Could not write to ' . $out_file);
		} else {
			foreach ($arr_files as $file) {
				if (substr($file, 0, 1) !== '/' && strpos($file, '://') == false) {
					$file = Config::get_value(Config::URL_ABSPATH) . $file;
				}
				fwrite($handle, $this->get_file_contents($file));
				fwrite($handle, "\n");
			}
			fclose($handle);
		}
		return $err;
	}

	/**
	 * Return file content
	 * 
	 * @param string $file
	 * @return string
	 */
	protected function get_file_contents($file) {
		return file_get_contents($file);
	}
}