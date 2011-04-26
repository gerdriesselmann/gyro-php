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
		$templates = $this->collect_templates();
		$ret = array();
		foreach ($templates as $template => $path) {
			// Generates actions with fixed action like ActionMapper::get_path('static_somedir/somefile.html')
			$ret[] = new StaticPageRoute(STATICPAGES_PREPEND, $path, STATICPAGES_APPEND, $template, $this, 'static');
		}
		// This allows using a parameterized syntax: ActionMapper::get_path('static', array('page' => 'somedir/somefile.html') 
		$ret[] = new StaticPageParamterizedRoute(STATICPAGES_PREPEND . '{page:s}' . STATICPAGES_APPEND, $this, 'static');

		return $ret;
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
				$statics_dir = $dir . 'static';
				if (is_dir($statics_dir)) {
					$this->collect_templates_in_dir($statics_dir, '');
				}
			}
		}
		return $this->cache_templates;
	}
	
	protected function collect_templates_in_dir($dir_path, $template_prefix) {
		$it = new DirectoryIterator($dir_path);
		foreach($it as $fileinfo) {
			if (!$fileinfo->isDot()) {
				$file = $fileinfo->getFilename();
				if ($fileinfo->isDir()) {
					$this->collect_templates_in_dir($fileinfo->getPathname(), $template_prefix . $file . '/');
				}
				else if(substr($file, -8, 8) === '.tpl.php') {
					$file = basename(substr($file, 0, -8));
					$tpl = $template_prefix . $file;
					$path = $template_prefix;
					if ($file != 'index') {
						$path .= $file;
					}
					$this->cache_templates[$tpl] = $path;
				}
			}
		}
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
			foreach($arr as $template => $path) {
				$result[] = array(
					'url' => ActionMapper::get_url('static', array('page' => $path)),
					'lastmod' => filemtime(TemplatePathResolver::resolve('static/' . $template)) 
				);
			}
		}
	}		 	 		
}
