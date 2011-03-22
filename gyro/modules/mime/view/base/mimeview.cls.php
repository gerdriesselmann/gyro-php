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
 * Mime views are normally cached, like any other content. When using a mime view, the according route 
 * therefor should disable server side caching, but enable it at client side. The simplest way to 
 * fullfill this is to use the MimeCacheManager. 
 * 
 * Not caching on client side may lead to trouble in IE 6 when downloading data with SSL encryption. 
 * 
 * @see http://support.microsoft.com/kb/815313/en-us 
 * 
 * @author Gerd Riesselmann
 * @ingroup Mime
 */
class MimeView extends ContentViewBase {
	const MIMETYPE = 'mimetype';
	const EXPIRES = 'expires';

	/**
	 * Contructor takes a name and the page data
	 */	
	public function __construct($template, $page_data) {
		parent::__construct($template, $page_data);
		$page_data->page_template = 'emptypage';
		$this->assign(self::MIMETYPE, 'application/octet-stream');
		$this->assign(self::EXPIRES, 0);
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
			
			// GR: Disabled, since this is too generic. Left to app to handle
			//if (RequestInfo::current()->is_ssl()) {
			//	Common::header('Cache-Control', 'maxage=3600', true); //Fix for IE in SSL 
			//}
			
			if (strpos($mimetype, 'charset') === false) {
				$mimetype .= '; charset=' . GyroLocale::get_charset();
			}			
			GyroHeaders::set('Content-Type', $mimetype, true);
			
			// Expires Header
			$ex = $this->retrieve(self::EXPIRES);
			if ($ex) {
				if ($ex < 10 * GyroDate::ONE_YEAR) {
					// $ex is a duration
					$ex = time() + $ex;
				}
				$this->page_data->get_cache_manager()->set_expiration_datetime($ex);
			}
		}
	}	
	
	/**
	 * Send given File to the browser
	 * 
	 * @param string $file Path to file
	 */
	public function display_file($file) {
		if (file_exists($file)) {
			$modification = filemtime($file);
			if ($modification) {
				$this->page_data->get_cache_manager()->set_creation_datetime($modification);
			}
			
			// Detect mime type
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

			$this->assign('data', file_get_contents($file));
		}
	}
}
