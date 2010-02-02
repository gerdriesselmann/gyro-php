<?php
/**
 * Reads pages from cache without needing a prior routing process
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class UpfrontCache {
	public static function serve_from_cache(PageData $page_data) {
		$view = ViewFactory::create_view(IViewFactory::PAGE, $page_data->page_template, $page_data);
		if ($view->is_cached()) {
			$view->render(IView::DISPLAY);
			exit;
		}		
	}
}