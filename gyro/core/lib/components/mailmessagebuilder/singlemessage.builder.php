<?php
require_once dirname(__FILE__) . '/imailmessagebuilder.cls.php';

/**
 * Build message body for a single message 
 */
class SingleMessageBuilder implements IMailMessageBuilder {
	/**
	 * Message to build from
	 * @var string
	 */
	protected $message;
	/**
	 * Mime type of message
	 * @var string
	 */
	protected $mime_type;
	
	/**
	 * Constructors
	 * 
	 * @param string $msg Message body
	 * @param string $mime Mime type
	 */
	public function __construct($msg, $mime) {
		$this->message = $msg;
		$this->mime_type = str_replace('%charset', GyroLocale::get_charset(), $mime);
	}
	
	/**
	 * Return mime type of mail as a whole
	 * 
	 * @return string
	 */
	public function get_mail_mime() {
		return $this->mime_type;
	}
	
	/**
	 * Return mail body
	 * 
	 * @return string
	 */
	public function get_body() {
		return chunk_split(base64_encode($this->message));
	}
	
	/**
	 * Return additional mail headers
	 * 
	 * @attention Content-Type header is already added
	 * 
	 * @return $headers Associative array with header name as key
	 */
	public function get_additional_headers() {
		return array('Content-Transfer-Encoding' => 'base64');
	}
}
