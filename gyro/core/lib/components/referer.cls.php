<?php
/**
 * Referer extends URL and adds some search engine recognition routines
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Referer extends Url {
	private $keyword_query_key = false;
	private $original = '';

	/**
	 * Constructor
	 * 
	 * @return Referer
	 */
	public static function current() {
		$referer = RequestInfo::current()->referer();
		return new Referer($referer);
	}
	
	/**
	 * Create new Referer instancwe
	 *
	 * @param string $s_url
	 * @return Referer
	 */
	public static function create($s_url, $policy = self::HTTP_ONLY) {
		return new Referer($s_url);
	}
	
	/**
	 * Constructor
	 */
	public function __construct($referer_url = '') {
		$this->original = $referer_url;
		parent::__construct($referer_url);		
	}
	
	
	/**
	 * Returns this query as a string
	 *
	 * The URL is not ready for outputting it on an HTML page, it must be HTMLescaped before! It is however URL escaped.
	 * 
	 * @return string This Url as a string.   
	 * @exception Throws an exception if hostname is empty
	 */
	public function build($mode = Url::ABSOLUTE, $encoding = Url::ENCODE_PARAMS) {
		if ($this->is_valid()) {
			return parent::build($mode, $encoding);
		}
		return '';
	}
	
	
	/**
	 * Returns original referer URL
	 * 
	 * @return string
	 */
	public function get_original_referer_url() {
		return $this->original;
	}
	
	/**
	 * Returns true if referer is internal
	 */
	public function is_internal() {
		$url = Url::current();
		return ($this->get_host() == $url->get_host());
	}
	
	/**
	 * Returns true if referer is external
	 */
	public function is_external() {
		return (!$this->is_empty() && !$this->is_internal());
	}
	
	/**
	 * Returns search engine information if referer is a search engine, else false 
	 * 
	 * Computation may be expensive, so handle with care.
	 * 
	 * Routine checks, if keywords are provided with referer, if not, referer is not recognized as a search engine!
	 * 
	 * @return mixed False if referer is no searchengine, an associateive array with fields 'domain', 'host', and 'keywords' otherwise 
	 */
	public function search_engine_info() {
		if (!$this->is_external()) {
			return false;
		}
		$query_params = $this->get_query_params();
		if (count($query_params) == 0) {
			return false; // No params, no keywords...
		}

		$arr_se = $this->get_search_engines();
		$host = false; 
		// We are interested in search engines that provide a query only
		foreach($query_params as $query_param => $query_value) {
			$possible_searchengine_sld = Arr::get_item($arr_se, $query_param, false);
			if ($possible_searchengine_sld !== false) {
				// We have a query param that *may* contain keywords
				// depending on referer domain ('q' are keywords for google, but not for a Drupal page)
				if (empty($host)) { 
					// Only parse host (expensive!) if needed
					$host = $this->parse_host(); 
				}
				if (in_array($host['sld'], $possible_searchengine_sld)) {		
					// We have a query parameter and a domain that belong to a search engine. Return
					return array(
						'domain' => $host['domain'],
						'searchengine' => $host['sld'],
						'host' => $this->get_host(),
						'keywords' => $query_value
					);
				}
			} 
		}
			
		return false;
	}
		
	/**
	 * Return array of keyword query keys and associated search engine second level domains
	 */	
	private function get_search_engines() {
		return array (
			'encquery' => array('aol'),
			'p' => array('yahoo'),
			'q' => array('google', 'msn', 'ask', 'altavista', 'alltheweb', 'gigablast', 'live', 'najdi', 'aol', 'club-internet', 'seznam', 'search', 'aolsvc', 't-online'),
			'qs' => array('virgilio', 'alice'),
			'qt' => array('looksmart'),
			'query' => array('aol', 'lycos', 'cnn', 'mamma', 'mama'),
			'rdata' => array('voila'),
			's' => array('netscape'),
			'terms' => array('about'),
			'text' => array('yandex'),
			'w' => array('seznam'),
			'wd' => array('baidu')
		);
	}
}
