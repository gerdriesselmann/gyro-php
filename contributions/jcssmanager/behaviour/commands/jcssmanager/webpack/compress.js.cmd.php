<?php
Load::commands('jcssmanager/webpack/compress.base');

class JCSSManagerCompressJSWebpackCommand extends JCSSManagerCompressBaseWebpackCommand {
	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return JCSSManager::TYPE_JS;
	}
}