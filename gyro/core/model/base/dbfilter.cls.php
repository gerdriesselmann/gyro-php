<?php
/**
 * A filter to apply to a search result
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */ 
class DBFilter implements IDBQueryModifier {
	/**
	 * Title of filter
	 *
	 * @var string
	 */
	private $title;
	
	/**
	 * Key for filter
	 *
	 * @var string
	 */
	private $key;

	/**
	 * If is default or not
	 *
	 * @var bool
	 */
	private $is_default = false;
	
	/**
	 * contructor
	 * 
	 * @param string title
	 * @param string key
	 */
	public function __construct($title, $key = '') {
		$this->title = $title;
		$this->key = ($key) ? $key : $title;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param string $title
	 */
	public function set_title($title) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function get_key() {
		return $this->key;
	}

	/**
	 * @param string $key
	 */
	public function set_key($key) {
		$this->key = $key;
	}

	public function apply($query) {
		// Do nothng here
	}

	/**
	 * @return bool
	 */
	public function is_default() {
		return $this->is_default;		
	}
	
	public function set_is_default($bool) {
		$this->is_default = $bool;
	}
	
	/**
	 * Preporocess value depending on operator
	 * 
	 * @since 0.5.1
	 * 
	 * @param string $value
	 * @param string $operator
	 * @return string
	 */
	protected function preprocess_value($value, $operator) {
		$ret = $value;
		switch ($operator) {
			case DBWhere::OP_LIKE:
			case DBWhere::OP_NOT_LIKE:
				if ($value !== '') {
					$ret = '%' . $value . '%';
				}
				break;
		}
		return $ret;
	}
	
	
}
