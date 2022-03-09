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
 			new ExactMatchRoute('sitemap.xml', $this, 'gsitemap_site', new SimpleCacheManager()),
 			new ExactMatchRoute('sitemap.xml', $this, 'gsitemap_index', new SimpleCacheManager()),
 			new ExactMatchRoute('sitemapindex.xml', $this, '', new RedirectRenderDecorator('sitemap.xml')),
 		);
 		if (Config::has_feature(ConfigGSitemap::GSITEMAP_HTML_SITEMAP)) {
 			$ret[] = new ExactMatchRoute('sitemap.html', $this, 'gsitemap_site_html', new SimpleCacheManager()); 				
 			$ret[] = new ExactMatchRoute('sitemap.html', $this, 'gsitemap_index_html', new SimpleCacheManager());
 			$ret[] = new ExactMatchRoute('sitemapindex.html', $this, '', new RedirectRenderDecorator('sitemap.html'));
 		}
 		return $ret; 		
 	}
 	
	/**
	 * Activates includes before action to reduce cache memory 
	 */ 
	public function before_action() {
		Load::components('gsitemapmodel', 'gsitemapitemformatter');
	} 	 	

	/**
	 * Show a sitemap index
	 *
	 * @param PageData $page_data
	 */
 	public function action_gsitemap_index($page_data) {
	    $this->handle_gsitemap_site_or_index($page_data);
 	}
 	
 	/**
 	 * Show a sitemap site
 	 *
 	 * @param PageData $page_data
 	 * @return mixed
 	 */
	public function action_gsitemap_site($page_data) {
		$this->handle_gsitemap_site_or_index($page_data);
 	}

 	/**
 	 * Show a sitemap site
 	 *
 	 * @param PageData $page_data
 	 * @return mixed
 	 */
	private function handle_gsitemap_site_or_index($page_data) {
		$page_data->head->robots_index = ROBOTS_NOINDEX;
		// Somehat hackish. Let two routes work on same path
		$is_sitemap = $page_data->get_get()->contains('i');
		if (!$is_sitemap) {
			// Sitemap index
			$view = ViewFactory::create_view(IViewFactory::XML, 'core::gsitemap/index', $page_data);
			$this->gsitemap_index($page_data, $view);
			$view->render();
 			return self::OK;
		} else {
			$view = ViewFactory::create_view(IViewFactory::XML, 'core::gsitemap/site', $page_data);
			$ret = $this->gsitemap_site($page_data, $view);
			if ($ret === self::OK) {
				$view->render();
			}
			return $ret;
		}
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
		// Somehat hackish. Let two routes work on same path
		if ($page_data->get_get()->count() == 0) {
 			return $this->action_gsitemap_index_html($page_data); 
		}
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'core::gsitemap/site_html', $page_data);
 		$ret = $this->gsitemap_site($page_data, $view);
 		if ($ret === self::OK) {
 			$view->render();
 		}
  		return $ret;
	}

	protected function collect_models() {
		$ret = array();
		$models = array();
		EventSource::Instance()->invoke_event('gsitemap_models', null, $models);
		foreach($models as $model) {
			if ($model instanceof GSiteMapModel) {
				$ret[] = $model;
			}
			else {
				$ret[] = new GSiteMapModel($model);
			}
		}
		return $ret;
	}

	/**
	 * Colelct data for index
	 */
	protected function gsitemap_index(PageData $page_data, IView $view) {
		$arrret = array('main');
		$models = $this->collect_models();
		foreach($models as $model) {
			$arrret = array_merge($arrret, self::build_sitemap_index_for_model($model));
		}
 		EventSource::Instance()->invoke_event('gsitemap_index', null, $arrret);
 		$view->assign('files', $arrret); 
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
 			$arrret[] = new GSiteMapItemFormatter(Config::get_url(Config::URL_BASEURL), array());
 		}
 		else {
			$models = $this->collect_models();
			foreach($models as $model) {
				$arrret = array_merge($arrret, self::build_sitemap_for_model($model, $p));
			}
 		}
 		EventSource::Instance()->invoke_event('gsitemap_site', $p, $arrret);
 		if (count($arrret) == 0) {
 			return self::NOT_FOUND;
 		}
		$formatters = array();
		foreach($arrret as $item) {
			if ($item instanceof GSiteMapItemFormatter) {
				$formatters[] = $item;
			} else if (is_array($item)) {
				$formatters[] = new GSiteMapItemFormatter($item['url'], $item);
			} else {
				$formatters[] = new GSiteMapItemFormatter($item, array());
			}
		}
 		$view->assign('items', $formatters);
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
		$model = new GSiteMapModel($params);
		$model->items_per_file = $itemsperfile;
		return self::build_sitemap_index_for_model($model);		
	}

	/**
	 * Helper to build sitemap index for given sitemap model
	 * 
	 * @param GSiteMapModel $model 
	 * 
	 * @return array 
	 */
	public static function build_sitemap_index_for_model(GSiteMapModel $model) {
		$ret = array();
		$c = $model->get_number_of_chunks();
		for($i = 0; $i < $c; $i++) {
			$ret[] = $model->build_index_name($i);
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
		$model = new GSiteMapModel($params, 'view', $policy);
		$model->items_per_file = $itemsperfile;
		return self::build_sitemap_for_model($model, $index); 
	}	 	

	/**
	 * Build a  sitemap file for given sitemap model
	 * 
	 * @param GSiteMapModel $model The model rules
	 * @param string $index 
	 *  
	 * @return array
	 */
	public static function build_sitemap_for_model(GSiteMapModel $model, $index) {
		$ret = array();
		$chunk = $model->extract_chunk($index);
		if ($chunk !== false) {
			$dao = $model->create_adapter();
			$model->select_chunk($dao, $chunk);
		
			$dao->find();
			while($dao->fetch()) {
				$ret[] = $model->create_formatter($dao);
			}
		}
		return $ret;
	}	 	
	
} 
