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
 * @attention 
 *   Status should be used with plain text message only, since its output routines
 *   convert everythig to HTML entities. If you for some reason want HTML in you
 *   error messages, you must write your own string conversion.
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Status {
	const OUTPUT_HTML = 'html';
	const OUTPUT_PLAIN = 'plain';
	
	/** 
	 * The error message
	 *
	 * @deprecated Use to_string(), render() or display() to access message
	 * @var String
	 */
	//public $message = '';
	
	/**
	 * Array of messages
	 * 
	 * @var array
	 */
	protected $messages = array();
	
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
			$this->append($message);
		} 
	}
	
	/**
	 * Convert to string
	 */
	public function __toString() {
		return $this->to_string();
	}
	
	/**
	 * Catch old clients access former public member 
	 * $message for compatability reason
	 */
    public function __get($name) {
        if ($name === 'message') {
        	return $this->to_string();
        }
    }
	
	/**
	 * Converts messages to a string
	 * 
	 * Messages are divided by <br /> for HTML and \n for plain text output.
	 * 
	 * @attention For HTML output, messages are escaped, so if your messages
	 *            contain HTML tags, they will be converted to &lt; etc
	 * 
	 * @return string
	 */
	public function to_string($policy = self::OUTPUT_HTML) {
		$ret = '';
		if ($policy == self::OUTPUT_PLAIN) {
			$ret .= implode("\n", $this->messages); 
		}
		else {
			$tmp = array_map(array('String', 'escape'), $this->messages);
			$ret .= implode('<br />', $tmp);
		}
		return $ret; 
	}
	
	/**
	 * Returns messages as array
	 * 
	 * @return array
	 */
	public function get_messages() {
		return $this->messages;
	}

	/**
	 * Clear all error messages, but retain error status
	 */
	public function clear_messages() {
		$this->messages = array();
	}
	
	/**
	 * Append $text to the error message and turns this status into an error
	 *
	 * @param String
	 * @return void
	 */
	public function append($text) {
		if (!empty($text)) {
			$this->messages[] = $text;
			$this->isError = true;
		}
	}
	
	/**
	 * Merges with another status.
	 *
	 * Messages are added and this status becomes an error if either this or 
	 * the merged status are errors
	 *
	 * @param Status|Exception|PEAR_Error|string $other Either Status, Exception, PEAR_Error or a string
	 */
	public function merge($other) {
		if ($other instanceof Status) {
			foreach($other->get_messages() as $m) {
				$this->append($m);
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
		return ($this->isError == false);
	}

	/**
	 * Returns true, if status is an error
	 * 
	 * @return bool
	 */
	public function is_error() {
		return ($this->isError == true);
	}

	/**
	 * Returns true, if there is no message 
	 * 
	 * @return bool
	 */
	public function is_empty() {
		return ($this->count() == 0);
	}
	
	/**
	 * Returns number of error messages
	 * @return int
	 */
	public function count() {
		return count($this->messages);
	}
	
	/**
	 * Print status messages
	 * 
	 * @return void
	 */
	public function display($policy = self::OUTPUT_HTML) {
		print $this->render($policy);
	}

	/**
	 * Render status
	 * 
	 * @return string
	 */
	public function render($policy = self::OUTPUT_HTML) {
		$ret = $this->to_string($policy);
		if ($ret !== '' && $policy == self::OUTPUT_HTML) {
			$ret = html::error($ret);
		}
		return $ret;
	}
	
	/**
	 * Save status into session
	 * 
	 * @return bool True, if status was persisted
	 */
	public function persist() {
		if (!$this->is_empty()) {
			Session::push('status', $this);
			return true;
		}
		return false;
	}
	
	/**
	 * Restore status from sessiom
	 * 
	 * @return Status False if no status was in session
	 */
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
 * @attention
 *   Don't try to mix Status and Message, e.g. by merging a Message with
 *   a Status. The result will be definitely not meaningful, since neither
 *   of them can handle such a case.
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class Message extends Status {
	public function is_error() {
		return false;
	}

	public function is_ok() {
		return true;
	}
	
	public function render($policy = self::OUTPUT_HTML) 	{
		$ret = $this->to_string($policy);
		if ($policy == self::OUTPUT_HTML) {
			$ret = html::success($ret);
		}
		return $ret;
	}
	
	/**
	 * Kept for compatability.. Meaningless
	 * 
	 * @deprecated
	 * @return bool
	 */
	public function is_message() {
		return true;
	}
}
