<?php
/**
 * Maps actions to URLs
 *
 * The ActionMapper allows to retrieve the URL for a given action, including possible 
 * parameters.
 *
 * Given a model "blog" and an action blog_view on Controler BlogControler to show a 
 * blog entry defined as a ParameterizedRoute of 'blog/{id:ui>}'. The url of a given 
 * blog entry can now be retrieved like this:
 *
 * @code
 * $blog = Blog::get($id); // Get blog entry with given id
 * $s_url = ActionMapper::get_url('view', $blog); // Retrieve url to view entry
 * @endcode
 *
 * @author Gerd Riesselmann
 * @ingroup Controller
 */
class ActionMapper {
	/**
	 * Array of actions and URLS
	 */
	private static $actions = array();
	
	/**
	 * Registers URL builder  for action.
	 * 
	 * @param string $action Action name
	 * @param IUrlBuilder $urlbuilder The builder for given action
	 * 
	 */
	public static function register_url($action, $urlbuilder) {
		self::$actions[$action] = $urlbuilder;
	}
	
	/**
	 * Returns path for given action. Path is relative to base url, although an url builder may decide to 
	 * return an absolute url (including "http://"!) nonetheless (e.g. if https is required for action url)
	 * 
	 * @param string $action Action name
	 * @param mixed $params Parameters, depend on action
	 * @return string
	 */
	public static function get_path($action, $params = null) {
		return self::build_url($action, $params, IUrlBuilder::RELATIVE);
	}
	
	/**
	 * Returns url for given action. Path is absolute
	 * 
	 * @param string $action Action name
	 * @param mixed $params Parameters, depend on action
	 * @return string
	 */
	public static function get_url($action, $params = null) {
		return self::build_url($action, $params, IUrlBuilder::ABSOLUTE);
	}
	
	/**
	 * Build URL for given action, with given params, either relative or absolute
	 *
	 * @param string $action
	 * @param mixed $params
	 * @param integer $absolute_or_relative Either IUrlBuilder::ABSOLUTE or IUrlBuilder::RELATIVE 
	 * @return string
	 */
	private static function build_url($action, $params, $absolute_or_relative) {
		$url_builder = Arr::get_item(self::$actions, $action, false);
		if (empty($url_builder)) {
			if ($params instanceof IActionSource) {
				$action = $params->get_action_source_name() . '_' . $action;
				$url_builder = Arr::get_item(self::$actions, $action, false);
			}
		}
		if ($url_builder) {
			return $url_builder->build_url($absolute_or_relative, $params);
		}
		return '';
	}
	
}
