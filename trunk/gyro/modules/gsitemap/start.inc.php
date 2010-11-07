<?php
/**
 * @defgroup GSitemap
 * @ingroup Modules
 * 
 * Creates XML sitemaps for search engines
 * 
 * You can add content to the sitemap by catching the events "gsitemap_site" and "gsitemap_index"
 * 
 * When building the index, the event gsitemap_index is fired. You may add new sites to the index
 * by just adding them to the result array. A sitemap site is defined by a unique name and you are 
 * responsible for processing request to this site later on. You should also take care the sites 
 * don't get to large.
 * 
 * For example you may decide to create a page for your photos. A possible names may be "photos".
 * 
 * When a sitemap site gets invoked, the content, that is a list of urls, must be build. Catch 
 * the event "gsitemap_site" to do this. Here you must 
 * 
 * - check if a specific site is invoked by checking the event $params
 * - Add URLs to the result in form of an associative array with two parameters: 'url' and 'lastmod', 
 *   which is the last mdification date as PHP timestamp. 'lastmod' is optional.
 *   
 * For the photo example the code may look like this:
 * 
 * @code
 * public function on_event($name, $params, &$result) {
 *   switch ($name) {
 *     case 'gsitemap_index':
 *       $result[] = 'photos';
 *       break;
 *     case 'gsitemap_site':
 *       if ($params == 'photos') {
 *          foreach (get_photo_urls() as $url) {
 *             $result[] = array('url' => $url);
 *          }
 *       }
 *       break;
 *   }
 * }
 * @endcode
 *  
 * In case you want all detail pages of a given model to be part of the sitemap, there are 
 * two convenience functions to create index and sites, respecting a maxium of items per site.
 * 
 * @attention The functions assume there is an action "view" defined for the given model.
 * 
 * @code
 * public function on_event($name, $params, &$result) {
 *   switch ($name) {
 *     case 'gsitemap_index':
 *        $result = array_merge($result, GsitemapController::build_sitemap_index('photos'));
 *        break;
 *     case 'gsitemap_site':
 *        $result = array_merge(
 *           $result, 
 *           GsitemapController::build_sitemap('photos', $params, GsitemapController::ITEMS_PER_FILE, GsitemapController::NO_TIMESTAMP)
 *        );
 *        break; 
 *   }
 * }
 * @endcode
 * 
 * This can be even more simplified by adding your model on the gsitemap_models event.
 * 
 * @attention The module assume there is an action "view" defined for the given model.
 * 
 * @code
 * public function on_event($name, $params, &$result) {
 *   switch ($name) {
 *     case 'gsitemap_models':
 *        $result[] = 'photos';
 *        break;
 *   }
 * }
 * @endcode
 * 
 * Instead of passing a string, you may also pass an IDataObject:
 *  
 * @code
 * public function on_event($name, $params, &$result) {
 *   switch ($name) {
 *     case 'gsitemap_models':
 *        $adapter = new DAOPhotos();
 *        $adapter->status = Stati::ACTIVE;
 *        $result[] = $adapter;
 *        break;
 *   }
 * }
 * @endcode
 */

/**
 * GSitemap config options
 * 
 * @author Gerd Riesselmann
 * @ingroup GSitemap
 */
class ConfigGSitemap {
	/**
	 * Generate HTML sitemaps, too
	 */
	const GSITEMAP_HTML_SITEMAP = 'GSITEMAP_HTML_SITEMAP';
}

Config::set_feature_from_constant(ConfigGSitemap::GSITEMAP_HTML_SITEMAP, 'APP_GSITEMAP_HTML_SITEMAP', false);

