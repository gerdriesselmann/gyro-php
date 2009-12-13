<?php
/**
 * Status class
 *
 * Indicates either success or an error
 *
 * If a function returns successfull, it can return a status with isError == false, 
 * which is equivalent to is_ok() == true. This is obtained using the default contructor.
 * If a function returns an error, it uses the constructor with a message. isError in this
 * case is true, and is_ok() returns false. 
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Status {
	/** 
	 * The error message
	 *
	 * @var String
	 */
	public $message = '';
	
	/**
	 * Indicates if an error occured (TRUE) or if not (FALSE)
	 *
	 * @var Boolean
	 */
	protected $isError = false;
	
	/**
	 * Constructor
	 *
	 * @param Mixed If String: The error message, else if left blank : No error
	 */
	public function __construct($message = false) {
		if (!empty($message)) {
			$this->message = $message;
			$this->isError = true;
		} 
	}
	
	/**
	 * Convert to string
	 */
	public function __toString() {
		return $this->message;
	}
	
	/**
	 * Append $text to the error message and turns this status into an error
	 *
	 * @param String
	 * @return void
	 */
	public function append($text) {
		if ( !empty($this->message) ) {
			$this->message .= "<br />";
		}
		
		$this->message .= $text;
		$this->isError = true;
	}
	
	/**
	 * Merges with another status.
	 *
	 * Messages are added and this status becomes anerror if either this or 
	 * the merged status are errors
	 *
	 * @param mixed Either Status or PEAR_Error
	 */
	public function merge($other) {
		if ($other instanceof Status) {
			if (!$other->is_empty()) {
				$this->append($other->message);
			}
		}
		else if ($other instanceof PEAR_Error) {
			$this->append($other->getMessage());
		}
		else if ($other instanceof Exception) {
			$this->append($other->getMessage());
		}
		else if (!empty($other) && is_string($other)){
			$this->append($other);
		}
	}  
	
	/**
	 * Indicates if there is an error or not
	 *
	 * @return Boolean
	 */
	public function is_ok() {
		return ($this->isError == false || $this->is_empty());
	}

	public function is_error() {
		return ($this->isError == true && !$this->is_empty());
	}

	
	public function is_empty() {
		return empty($this->message);
	}
	
	public function display() {
		if ($this->is_ok())
			return;
			
		print html::error($this->message);
	}
	
	public function persist() {
		if ($this->message) {
			Session::push('status', $this);
			return true;
		}
		return false;
	}
	
	public static function restore() {
		$ret = false;
		if (Session::is_started()) {
			$ret = Session::pull('status');
		}
		return $ret;
	}  
}

/**
 * Success message class
 *
 * Used to indicate success  
 *
 * For the Status class, each text passed turns it into an error. 
 * The Message class however, allows to return a message on success, too   
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Message extends Status {
	public function __construct($text) {
		$this->message = $text;
		$this->isError = false;
	}
	
	function display() 	{
		if ($this->is_message())
			print html::success($this->message);
		else
			Status::display();
	}
	
	function is_message() {
		return ($this->is_ok() && !$this->is_empty());
	}
}
?>