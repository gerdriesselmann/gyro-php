<?php
function javascript_ckeditor_update_1() {
	Load::models('jcsscompressedfiles');
	return JCSSCompressedFiles::remove(JCSSManager::TYPE_JS, 'js/compressed.ckeditor.js');
}