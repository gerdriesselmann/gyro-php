<?php
/**
 * @defgroup Robots
 * @ingroup Modules
 *  
 * Replaces robots.txt by a view, allowing a different robots.txt for test and live mode
 * 
 * The template is named robots.txt.tpl.php and located at /app/view/templates/{lang}/.
 */

/**
 * Replaces robots.txt with a view
 * 
 * @author Gerd Riesselmann
 * @ingroup Robots
 */
class RobotsController extends ControllerBase {
	/**
	 * Return array of Route instances which are handled by this controller
	 * 
	 * @return array Array of Routes 
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('any://robots.txt', $this, 'robots_txt'),
		);		
	}

	/**
	 * Render robots.txt
	 * 
	 * @param PageData $page_data
	 * @return void
	 */
	public function action_robots_txt(PageData $page_data) {
		$page_data->head->robots_index = ROBOTS_NOINDEX;
		$view = ViewFactory::create_view(ViewFactoryMime::MIME, 'robots.txt', $page_data);
		$view->assign(MimeView::MIMETYPE, 'text/plain');
		$view->render();		
	}	
}