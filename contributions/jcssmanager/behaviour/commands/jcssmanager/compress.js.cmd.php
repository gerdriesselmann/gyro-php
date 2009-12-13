<?php
Load::commands('jcssmanager/compress.base');

class JCSSManagerCompressJSCommand extends JCSSManagerCompressBaseCommand {
	/**
	 * Invoke YUICOmpressor
	 * 
	 * @param string $in_file
	 * @param string $out_file
	 * @return Status 
	 */
	protected function invoke_yui($in_file, $out_file) {
		return $this->run_yui($in_file, $out_file, 'js');
	}

	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return JCSSManager::TYPE_JS;
	}
}