<?php
/**
 * @defgroup StaticMainPage
 * @ingroup Modules
 *  
 * Routes view/templates/[lang]/index.tpl.php to the main page 
 */

/**
 * Controller for front page
 *
 * Routes view/templates/[lang]/index.tpl.php to the main page
 * 
 * @author Gerd Riesselmann
 * @ingroup StaticMainPage
 */
class IndexBaseController extends ControllerBase {
	/**
	 * Return array of urls which are handled by this controller
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('.', $this, 'index')
		);
	}

	/**
	 * Show front page
	 *
	 * @param Pagedata $page_data
	 */
 	public function action_index($page_data) {
 		$page_data->head->title = Config::get_value(Config::TITLE);
		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'index', $page_data); 
 		$view->render(); 		
 	} 		 	 			 	
} 
