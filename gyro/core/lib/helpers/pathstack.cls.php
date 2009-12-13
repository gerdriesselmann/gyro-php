<?php
/**
 * Transform path into array and apply stack functionality 
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class PathStack {
	private $path_front = null;
	private $path_back = null;
	
	public function __construct($path = '') {
		$this->set_path($path);
	}
	
	/**
	 * Set path from string
	 */
	public function set_path($path) {
		$path = trim($path, '/');
		if (!empty($path)) {
			$this->path_front = explode('/', $path);
		}
		else {
			$this->path_front = array();
		}
		$this->path_back = array();		
	}
	
	/**
	 * Return original path as string
	 */
	public function get_path() {
		return $this->append_to_back($this->implode_front());
	}
	
	
	/**
	 * Returns current item in path
	 */
	public function current() {
		if (count($this->path_front) > 0) {
			return $this->path_front[0];
		}
		
		return false;
	}

	/**
	 * Takes current element, moves it to back 
	 */	
	private function do_next() { 	
		$ret = array_shift($this->path_front);
		if (!empty($ret)) {
			array_push($this->path_back, $ret);
		}
	}
	
	
	/**
	 * Takes current element, moves it to back, moves pointer forward and returns new current element 
	 */
	public function next() {
		$this->do_next();
		return $this->current();
	}

	/**
	 * Returns current element and moves pointer forward one step 
	 */
	public function shift() {
		$ret = $this->current();
		if ($ret !== false) {
			$this->do_next();
		}
		return $ret;
	}
	
	/**
	 * Points current element to first element not covered by $path_adjust
	 * 
	 * @return True, if success, false otherwise
	 */
	public function adjust($path) {
		$adjust_stack = new PathStack($path);
		$this_stack = clone($this);
		
		$cur = $adjust_stack->current(); 
		while($cur !== false) {
			if ($cur !== $this_stack->current()) {
				return false;
			}

			$cur = $adjust_stack->next();
			$this_stack->do_next();
		}
		
		$this->path_back = $this_stack->path_back;
		$this->path_front = $this_stack->path_front;
		return true;
	}
	
	/**
	 * Return path processed as string
	 */  
	public function implode_back() {
		return implode('/' , $this->path_back);
	}
	
	/**
	 * Appends given path to back
	 */
	public function append_to_back($path) {
		$arr = array($this->implode_back());
		if ($path !== '') {
			$arr[] = $path;
		}
		return implode('/', $arr);
	}
	
	/**
	 * Prepends given path to front
	 */
	public function prepend_to_front($path) {
		$arr = array($this->implode_front());
		if ($path !== '') {
			array_unshift($arr, $path);
		}
		return implode('/', $arr);		
	}
	
	/**
	 * Return path yet to process as string
	 */  
	public function implode_front() {
		return implode('/' , $this->path_front);
	}
	
	/**
	 * Returns the numbers of items in front
	 */
	public function count_front() {
		return count($this->path_front);
	}
	
	/**
	 * Pushes all front items to back
	 */
	public function clear_front() {
		while($this->next()) { }
	}
}
?>