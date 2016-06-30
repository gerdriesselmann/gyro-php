<?php
/**
 * Defines a view route for searching
 * 
 * @author Gerd Riesselmann
 * @ingroup SearchIndex
 */
class SearchIndexBaseController extends ControllerBase {
	
	/**
 	 * Return array of routes this controller takes responsability
 	 */
 	public function get_routes() {
 		$ret = array(
 			'view' => new ExactMatchRoute('search/', $this, 'searchindex_search', new NoCacheCacheManager()),
 		);
 		return $ret;
 	}
 	
 	/** 
 	 * Show search result
 	 * 
 	 * @param $page_data PageData
 	 */
 	public function action_searchindex_search($page_data) {
 		$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
		$page_data->breadcrumb = array(tr('Search', 'searchindex'));
 		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'searchindex/search', $page_data);
 		$terms = trim($this->get_terms($page_data));
		if ($terms !== '') {
			if (!GyroString::check_encoding($terms)) {
				// Convert. Since autodection is not really good, try most probable encoding
				// manually: Latin1
				if (GyroString::check_encoding($terms, 'ISO-8859-1')) {
					$newterms = GyroString::convert($terms, 'ISO-8859-1');
				}
				else {
					$newterms = GyroString::convert($terms);
				}
				if ($newterms != $terms) {
					Url::current()->replace_query_parameter('q', $newterms)->redirect();
				}
			}
			
			$page_data->head->title = tr('Search »%terms«', 'searchindex', array('%terms' => $terms));
			$page_data->head->description = tr('Search results for query »%terms«.', 'searchindex', array('%terms' => $terms));
			
			$search = SearchIndexRepository::get_index_implementation();
			$search->set_search($terms);
			
			Load::tools('pager', 'sorter');
			$pager = new Pager($page_data, $search);
			$pager->apply($search);
			$pager->prepare_view($view);
			
			$sorter = new Sorter($page_data, $search);
			$sorter->apply($search);
			$sorter->prepare_view($view);

			$view->assign('result', $search->execute());
		} else {
			$page_data->head->title = tr('Search', 'searchindex');
		}
		$view->assign('terms', $terms);
		$view->render(); 
 	}
 	
 	/**
	 * Extract Terms
 	 */
 	protected function get_terms(PageData $page_data) {
 		$param = Config::get_value(ConfigSearchIndex::QUERY_PARAMETER);
 		return $page_data->get_get()->get_item($param, '');
 	}
}
