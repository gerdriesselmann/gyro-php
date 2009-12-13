<?php
/**
 * Created on 15.06.2007
 *
 * @author Gerd Riesselmann
 */
 
 
class NewssitemapController extends ControllerBase {
	/**
 	 * Return array of IDispatchToken this controller takes responsability
 	 */	
 	public function get_routes() {
 		return array(
 			new ExactMatchRoute('newssitemap.xml', $this, 'newssitemap_index', new SimpleCacheManager()),
 		);
 	}

	/**
	 * Show a sitemap index
	 *
	 * @param PageData $page_data
	 */
	public function action_newssitemap_index($page_data) {
		$arrret = array(); 		
		EventSource::Instance()->invoke_event('newssitemap_index', '', $arrret);
		$c = count($arrret);
 		if ($c == 0) {
 			return CONTROLLER_NOT_FOUND;
 		}
 		$data = array();
 		$limt = 1000 / $c;
 		if ($limit > 300) { $limit = 300; }
 		foreach($arrret as $news) {
 			$news->limit($limit);
 			$data = array_merge($data, $news->execute());
 		}
 		
 		$view = ViewFactory::create_view(IViewFactory::XML, 'core::newssitemap/index', $page_data);
 		$view->assign('items', $data); 		
 		$view->render();
 	}  
} 
