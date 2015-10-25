<?php
Load::commands('jcssmanager/concat/compress.base');

class JCSSManagerCompressJSConcatCommand extends JCSSManagerCompressBaseConcatCommand {
	/**
	 * Returns type of compressed file
	 *   
	 * @return string One of TYPE_X constants
	 */
	protected function get_db_type() {
		return JCSSManager::TYPE_JS;
	}
}