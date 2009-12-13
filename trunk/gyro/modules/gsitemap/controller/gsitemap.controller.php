<?php
/**
 * Controls building and rendering of sitemap
 * 
 * @author Gerd Riesselmann
 * @ingroup GSitemap
 */
class GsitemapController extends ControllerBase {
	const ITEMS_PER_FILE = 300;
	const USE_TIMESTAMP = 1;
	const NO_TIMESTAMP = 0;
	
	/**
 	 * Return array of IDispatchToken this controller takes responsability
 	 */	
 	public function get_routes() {
 		$ret = array(
 			new ExactMatchRoute('sitemapindex.xml', $this, 'gsitemap_index', new SimpleCacheManager()),
 			new ExactMatchRoute('sitemap.xml', $this, 'gsitemap_site', new SimpleCacheManager())
 		);
 		if (Config::has_feature(ConfigGSitemap::GSITEMAP_HTML_SITEMAP)) {
 			$ret[] = new ExactMatchRoute('sitemapindex.html', $this, 'gsitemap_index_html', new SimpleCacheManager());
 			$ret[] = new ExactMatchRoute('sitemap.html', $this, 'gsitemap_site_html', new SimpleCacheManager()); 				
 		}
 		return $ret; 		
 	}

	/**
	 * Show a sitemap index
	 *
	 * @param PageData $page_data
	 */
 	public function action_gsitemap_index($page_data) {
 		$view = ViewFactory::create_view(IViewFactory::XML, 'core::gsitemap/index', $page_data);
 		$this->gsitemap_index($page_data, $view);
 		$view->render();
 	}
 	
 	/**
 	 * Show a sitemap site
 	 *
 	 * @param PageData $page_data
 	 * @return mixed
 	 */
	public function action_gsitemap_site($page_data) {
		$view = ViewFactory::create_view(IViewFactory::XML, 'core::gsitemap/site', $page_data);
		$ret = $this->gsitemap_site($page_data, $view);
 		if ($ret === self::OK) {
 			$view->render();
 		}
		return $ret;
 	}  
 	
	/**
	 * Show sitemap index as HTML
	 *
	 * @param PageData $page_data
	 */
	public function action_gsitemap_index_html($page_data) {
 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'core::gsitemap/index_html', $page_data);
 		$ret = $this->gsitemap_index($page_data, $view);
 		$view->render();
  		return $ret;
	}
 	
	/**
	 * Show sitemap file as HTML
	 *
	 * @param PageData $page_data
	 */
	public function action_gsitemap_site_html($page_data) {
 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'core::gsitemap/site_html', $page_data);
 		$ret = $this->gsitemap_site($page_data, $view);
 		if ($ret === self::OK) {
 			$view->render();
 		}
  		return $ret;
	}

	/**
	 * Colelct data for index
	 */
	protected function gsitemap_index(PageData $page_data, IView $view) {
		$arrret = array('main');
 		EventSource::Instance()->invoke_event('gsitemap_index', null, $arrret);
 		$view->assign('files', $arrret); 		
 		
 		$arrNow = getdate();
 		$timeUpdate = mktime($arrNow['hours'], 5, 0, $arrNow['mon'], $arrNow['mday'], $arrNow['year']);
 		if ($timeUpdate > time()) {
 			$timeUpdate = $timeUpdate - 60 * 60; // - 1 hour
 		}
 		$view->assign('timestamp', $timeUpdate); 		 		
 	}
 
 	/**
 	 * Show a sitemap site
 	 *
 	 * @param PageData $page_data
 	 * @return mixed
 	 */
	protected function gsitemap_site(PageData $page_data, IView $view) {
 		$p = $page_data->get_get()->get_item('i');
 		if (empty($p)) {
 			return self::NOT_FOUND;
 		}
 		$arrret = array();
 		if ($p == 'main') {
 			$arrret[] = array(
				'url' => Config::get_url(Config::URL_BASEURL),
				'lastmod' => 0 
			);
 		}
 		EventSource::Instance()->invoke_event('gsitemap_site', $p, $arrret);
 		if (count($arrret) == 0) {
 			return self::NOT_FOUND;
 		}
 		$view->assign('files', $arrret); 	
 		return self::OK;
 	}  
 	
 	/**
 	 * Extracts model form given params
 	 *
 	 * @param mixed $params
 	 * @return ISearchAdapter
 	 */
 	private static function extract_adapter($params) {
 		if ($params instanceof ISearchAdapter) {
 			return $params;
 		}
 		return DB::create(Cast::string($params));
 	}

	/**
	 * Helper to build sitemap index for given dao class
	 * 
	 * @param string|ISearchAdapter $params 
	 *    If a string, it is interpreted as a model name
	 *    If ISearchAdapter limit() and execute() are executed uipen the insance
	 * @param integer $itemsperfile Number of items per sitemap file
	 * 
	 * @return array 
	 */
	public static function build_sitemap_index($params, $itemsperfile = self::ITEMS_PER_FILE) {
		if ($itemsperfile <= 0) {
			$itemsperfile = self::ITEMS_PER_FILE;
		}
		$adapter = self::extract_adapter($params);
		$model = $adapter->get_table_name();
		$c = ceil($adapter->count() / $itemsperfile);
		$ret = array();
		for($i = 0; $i < $c; $i++) {
			$ret[] = $model . $i;
		}
		return $ret;		
	}
	
	/**
	 * Build a  sitemap file for given model
	 * 
	 * @param string|ISearchAdapter $params 
	 *    If a string, it is interpreted as a model name
	 *    If ISearchAdapter limit() and execute() are executed upon the instance
	 * @param string $index 
	 * @param int $itemsperfile Number of items per sitemap file
	 *  
	 * @return array
	 */
	public static function build_sitemap($params, $index, $itemsperfile = self::ITEMS_PER_FILE, $policy = self::USE_TIMESTAMP) {
		if ($itemsperfile <= 0) {
			$itemsperfile = self::ITEMS_PER_FILE;
		}
		$dao = self::extract_adapter($params);
		$model = $dao->get_table_name();
		$ret = array();
		if (String::starts_with($index, $model) == false) {
			return $ret;
		}

		$si = String::substr($index, strlen($model));
		$i = Cast::int($si);
		if ($si != (string)$i) {
			return $ret;
		}
		
		$dao->limit($i * $itemsperfile, $itemsperfile);				
		$dao->find();
	
		$do_timestamp = Common::flag_is_set($policy, self::USE_TIMESTAMP) && ($dao instanceof ITimeStamped); 
		while($dao->fetch()) {
			$lastmod = $do_timestamp ? $dao->get_modification_date() : 0;
			$ret[] = array(
				'url' => ActionMapper::get_url('view', $dao),
				'lastmod' => $lastmod 
			);
		}
		return $ret;
	}	 	
} 
