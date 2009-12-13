<?php
/**
 * A content view of any mime type, not only text/html
 * 
 * Assign 'data' and MimeView::MIMETYPE to the created view like this:
 * 
 * @code
 * $imagedata = file_get_contents('my/image.png'); 
 * $view = ViewFactory::create_view(ViewFactoryMime::MIME, 'mime/view', $page_data);
 * $view->assign('data', $imagedata);
 * $view->assign(MimeView::MIMETYPE, 'image/png');
 * $view->render();
 * @endcode 
 * 
 * For convenience, the same can be achieved using display_file():
 * 
 * @code
 * $view = ViewFactory::create_view(ViewFactoryMime::MIME, 'mime/view', $page_data);
 * $view->display_file('my/image.png');
 * $view->render();
 * @endcode
 * 
 * @attention 
 *   Mime views are never cached, no matter what cache policy is set on PageData!
 * 
 * @author Gerd Riesselmann
 * @ingroup Mime
 */
class MimeView extends ContentViewBase {
	const MIMETYPE = 'mimetype';

	/**
	 * Contructor takes a name and the page data
	 */	
	public function __construct($template, $page_data) {
		parent::__construct($template, $page_data);
		$page_data->page_template = 'emptypage';
		$this->assign(self::MIMETYPE, 'application/octet-stream');
	}
	
	/**
	 * Always returns false
	 *
	 * @return bool
	 */
	protected function should_cache() {
		return false;
	}	
	
	/**
	 * Called after content is rendered, always
	 * 
	 * @param mixed $rendered_content The content rendered
	 * @paramint $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	protected function render_postprocess(&$rendered_content, $policy) {
		parent::render_postprocess($rendered_content, $policy);
		
		if (!Common::flag_is_set($policy, self::CONTENT_ONLY)) {
			$mimetype = $this->retrieve(self::MIMETYPE);
			header('Cache-Control: maxage=3600'); //Fix for IE in SSL 
			header('Pragma: public');
			// This leads to trouuble in IE and Safari
			// Possibly a gzip-issue?
			//header('Content-Length: ') . strlen($rendered_content);			
			header('Content-Type: ' . $mimetype);
		}
	}	
	
	public function display_file($file) {
		if (file_exists($file)) {
			$this->assign('data', file_get_contents($file));
			$mime_type = 'application/octect-stream';
			if (function_exists('finfo_open')) {
				$handle = finfo_open(FILEINFO_MIME); // return mime type ala mimetype extension
				$mime_type = finfo_file($handle, $file);
				finfo_close($handle);
			}
			else if (function_exists('mime_content_type')) {
				$mime_type = mime_content_type($file);
			}
			else {
				$path_info = pathinfo($file);
    			$path_ext = $path_info['extension'];
				// No MAGIC functions enabled, do a primitiv lookup based upon file ending
				$types = array(
					'gif' => 'image/gif',
					'png' => 'image/png',
					'jpg' => 'image/jpeg',
				);
				foreach($types as $extension => $type) {
					if ($path_ext === $extension) {
						$mime_type = $type;
						break;	
					}
				}				
			}
			$this->assign(self::MIMETYPE, $mime_type);
		}
	}
}
