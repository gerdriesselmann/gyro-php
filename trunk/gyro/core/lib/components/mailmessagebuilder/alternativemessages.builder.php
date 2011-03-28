<?php
require_once dirname(__FILE__) . '/attachments.builder.php';
require_once dirname(__FILE__) . '/singlemessage.builder.php';

/**
 * Build message body for a single and an alternative message  
 */
class AlternativeMessageBuilder extends AttachmentsBuilder {
	/**
	 * Alternative message
	 * 
	 * @var string
	 */
	protected $alt_message;
	
	/**
	 * Constructors
	 * 
	 * @param string $msg Message body
	 * @param string $mime Mime type
	 * @param string $alt Alternative message 
	 */
	public function __construct($msg, $mime, $alt) {
		parent::__construct(new SingleMessageBuilder($msg, $mime), array());
		$this->alt_message = $alt;
	}
	
	/**
	 * Return mime type of mail as a whole
	 * 
	 * @return string
	 */
	public function get_mail_mime() {
		return 'multipart/alternative; boundary=' . $this->get_boundary();
	}
	
	/**
	 * Return mail body
	 * 
	 * @return string
	 */
	public function get_body() {
		$blocks = array();
		$altbuilder = new SingleMessageBuilder($this->alt_message, MailMessage::MIME_TEXT_PLAIN);
		$blocks[] = $this->create_block($altbuilder->get_mail_mime(), false, $altbuilder->get_body());
		$blocks[] = $this->create_block($this->message_builder->get_mail_mime(), false, $this->message_builder->get_body());
		return 
			$this->start_seperator($this->boundary) .
			implode("\n" . $this->start_seperator($this->boundary), $blocks) .
			$this->end_seperator($this->boundary);
	}	
}
