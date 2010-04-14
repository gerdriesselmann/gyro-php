<?php
/**
 * Controller for static pages. 
 * 
 * Registers routes to all templates found in /view/templates/{lang}/static
 * 
 * @author Gerd Riesselmann
 * @ingroup StaticPages
 */
class StaticPagesController extends ControllerBase {
	private $cache_templates = null;
	
	/**
	 * Return array of urls which are handled by this controller
 	 */
	public function get_routes() {
		$templates = implode(',', $this->collect_templates());
		return array(
			new ParameterizedRoute(STATICPAGES_PREPEND . "{page:e:$templates}" . STATICPAGES_APPEND, $this, 'static')
		);
	}
	
	/**
	 * Load and display template
	 * 
	 * @param PageData $page_data
	 * @param string $template
	 */
	public function action_static($page_data, $page) {
		$path = 'static/' . $page;
		$template_file = TemplatePathResolver::resolve($path);
		if (!file_exists($template_file)) {
			return CONTROLLER_NOT_FOUND;
		}
		
		$cache = $page_data->get_cache_manager();
		if ($cache) {
			$cache->set_cache_duration(GyroDate::ONE_DAY);
		}
		$view = ViewFactory::create_view(IViewFactory::CONTENT, $template_file, $page_data);
		$view->render();
	}
	
	/**
	 * Find all templates that form static pages
	 * 
	 * @return array
	 */
	protected function collect_templates() {
		if (is_null($this->cache_templates)) {
			$this->cache_templates = array();
			$dirs = TemplatePathResolver::get_template_paths();
			foreach($dirs as $dir) {
				foreach(gyro_glob($dir . 'static/*.tpl.php') as $file) {
					$tpl = basename(substr($file, 0, -8));
					$this->cache_templates[$tpl] = $tpl;
				}
			}
		}
		return $this->cache_templates;
	}
	
	/**
	 * Process events.
	 * 
	 * Events processed are:
	 * - gsitemap_site
	 */
	public function on_event($name, $params, &$result) {
		if ($name == 'gsitemap_site' && $params == 'main') {
			$arr = $this->collect_templates();
			foreach($arr as $item) {
				$result[] = array(
					'url' => ActionMapper::get_url('static', array('page' => $item)),
					'lastmod' => filemtime(TemplatePathResolver::resolve('static/' . $item)) 
				);
			}
		}
	}		 	 		
}
