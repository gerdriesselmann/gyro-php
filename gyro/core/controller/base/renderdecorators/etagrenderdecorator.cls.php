<?php
/**
 * The etag render decorator creates a md5 hash of rendered page and compares this with an etag sended
 * along by the browser
 * 
 * It does this only if no etag is already present, this is: If the site is not cached.  
 */
class ETagRenderDecorator extends RenderDecoratorBase {
	/**
	 * Render page
	 *
	 * @param PageData $page_data
	 * @param IRenderDecorator Decorator to invoke render_content upon
	 * @param int $policy If set to IView::DISPLAY, content is printed, if false it is returned only
	 * @return void
	 */
	public function render_page($page_data, $content_render_decorator, $policy = IView::NONE) {
		$ret = parent::render_page($page_data, $content_render_decorator, $policy);
		if ($ret && !Common::is_header_sent('etag')) {
			$etag = md5($ret);
			Common::check_if_none_match($etag);
			Common::header('etag', $etag);
			// Send Cache headers
			Common::header('Pragma', '', true);
			Common::header('Cache-Control', 'private, no-cache, must-revalidate', true);
		}
		return $ret;
	}	
}