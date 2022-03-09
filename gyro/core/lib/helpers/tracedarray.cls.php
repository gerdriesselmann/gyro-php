<?php
/**
 * This class traces access on its members
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class TracedArray {
	private $arr = array();
	private $trace = array();
	
	/**
	 * Conctructor
	 *
	 * @param array $arr
	 */
	public function __construct($arr) {
		$this->arr = (array)$arr;
	}
	
	/**
	 * Returns item from array, default value if item is missing 
	 *
	 * @param string $key
	 * @param mixed $default_value
	 * @return mixed
	 */
	public function get_item($key, $default_value = '')  {
		$this->trace[$key] = true;
		return Arr::get_item($this->arr, $key, $default_value); 
	}
	
	/**
	 * Returns numbers of elements in array
	 *
	 * @return int
	 */
	public function count() {
		return count($this->arr);
	}
	
	/**
	 * Returns original array
	 *
	 * @return array
	 */
	public function get_array() {
		return $this->arr;		
	}
	
	/**
	 * Returns true, if array contains item with given key
	 *
	 * @param string $key
	 * @return bool
	 */
	public function contains($key) {
		return array_key_exists($key, $this->arr);
	}
	
	/**
	 * Returns true, if this array contains elements that gave not been accessed by get_item
	 *
	 * @return bool
	 */
	public function has_unused() {
		foreach($this->arr as $key => $value) {
			if (!array_key_exists($key, $this->trace)) {
				return true;		
			}			
		}
		return false;		
	}
	
	/**
	 * Marks all array items as read
	 */
	public function mark_all_as_used() {
		foreach($this->arr as $key => $value) {
			$this->get_item($key);
		}
		
	}

	/**
	 * Redirects to a page containing only unused items as query parameters
	 *
	 * @param bool $permanent if true redierct is 301, else it is 302
	 */
	public function redirect_unused($permanent = false) {
		$url = Url::current()->clear_query();
		foreach($this->arr as $key => $value) {
			if (!array_key_exists($key, $this->trace)) {
				$url->replace_query_parameter($key, $value);		
			}			
		}
		$url->redirect($permanent ? Url::PERMANENT : Url::TEMPORARY);
		exit();
	}
	
	/**
	 * Redirects to a page containing only used items as query parameters
	 *
	 * @param bool $permanent if true redierct is 301, else it is 302
	 */
	public function redirect_used($permanent = false) {
		$url = Url::current()->clear_query();
		foreach($this->arr as $key => $value) {
			if (array_key_exists($key, $this->trace)) {
				$url->replace_query_parameter($key, $value);		
			}			
		}
		$url->redirect($permanent ? Url::PERMANENT : Url::TEMPORARY);
		exit();
	}

	/**
	 * Returns name of all used keys in this array. Used are keys that were used in the
	 * get_item() function
	 *
	 * @return array
	 */
	public function get_used() {
		return array_keys($this->trace);
	}
}
