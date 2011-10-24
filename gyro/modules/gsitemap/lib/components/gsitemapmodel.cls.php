<?php
/**
 * Encapsulates rules for one sitemap model, which is a combination of a model and an action
 * and a set of rules. 
 * 
 * @author Gerd Riesselmann
 * @ingroup GSitemap
 */
class GSiteMapModel extends PolicyHolder {
	/**
	 * The model
	 * 
	 * @var string | IDataObject
	 */
	public $model;
	/**
	 * The action. Default is 'view'
	 * 
	 * @var string
	 */
	public $action = 'view';

	public $items_per_file = GsitemapController::ITEMS_PER_FILE;
	
	public $priority = 0.0;
	public $changefreq = '';
	
	protected $adapter;

	/**
	 * Constructor
	 */
	public function __construct($model, $action = 'view', $policy = GsitemapController::USE_TIMESTAMP) {
		$this->model = $model;
		$this->action = $action;
		parent::__construct($policy);
	}
	
 	/**
 	 * Creates adapter for given model
 	 *
 	 * @return IDataObject
 	 */
	public function create_adapter() {
		if (empty($this->adapter)) {
	 		if ($this->model instanceof IDataObject) {
	 			$this->adapter = $this->model;
	 		}
	 		else {
	 			$this->adapter = DB::create(Cast::string($this->model));
	 		}
		}
		return $this->adapter;
 	}
 	
 	public function get_model_name() {
 		if (is_string($this->model)) {
 			return $this->model;
 		} else {
 			$adapter = $this->create_adapter();
 			return $adapter->get_table_name();
 		}
 	}

 	/**
 	 * Limit adapter to chunk
 	 */
 	public function select_chunk(IDataObject $adapter, $chunk) {
 		$adapter->limit($chunk * $this->items_per_file, $this->items_per_file);				
 	}
 	
 	protected function get_index_elements() {
 		return array(
 			$this->get_model_name(),
			$this->action
		);
 	}
 	
 	public function build_index_name($chunk) {
		$index_elems = $this->get_index_elements();
		$index_elems[] = $chunk;
 		return implode('.', $index_elems); 		
 	}
 	
 	public function extract_chunk($index) {
 		$ret = false;
 		$index_elems = $this->get_index_elements();
 		$start = implode('.', $index_elems) . '.';
 		$l_start = strlen($start);
		if (substr($index, 0, $l_start) == $start) {
			$si = substr($index, $l_start);
			$i = Cast::int($si);
			if ($si == (string)$i) {
				$ret = $i;
			}
		}
 		return $ret;
 	}
 	
 	public function get_number_of_chunks() {
 		$adapter = $this->create_adapter();
		$c = ceil($adapter->count() / $this->items_per_file);
		return $c;		
 	}
 	
 	/**
 	 * Returns URL for given item
 	 * 
 	 * @param IDataObject $item
 	 * @return string
 	 */
 	public function get_url(IDataObject $item) {
 		return ActionMapper::get_url($this->action, $item);
 	}
 	
 	/**
 	 * Returns last modification date (or 0 if unknown)
 	 * 
 	 * @param IDataObject $item
 	 * @return int
 	 */
	public function get_lastmod(IDataObject $item) {
		$ret = 0;
		if ($this->has_policy(GsitemapController::USE_TIMESTAMP) && ($item instanceof ITimeStamped)) {
 			$ret = $item->get_modification_date();
		}
		return $ret;
 	}

	/**
	 * Return priority of this site
	 *
	 * @return float
	 */
	public function get_priority() {
 		return $this->priority;
 	}

	/**
	 * Return change frequency ofthis site
	 * 
	 * @return string
	 */
	public function get_changefreq() {
 		return $this->changefreq;
 	}

	/**
	 * Create Formatter
	 * 
	 * @param \IDataObject $dao
	 * @return \GSiteMapItemFormatter
	 */
	public function create_formatter(IDataObject $dao) {
		return new GSiteMapItemFormatter($this->get_url($dao), array(
			'lastmod' => $this->get_lastmod($dao),
			'changefreq' => $this->get_changefreq(),
			'priority' => $this->get_priority()
		));
	}
}
