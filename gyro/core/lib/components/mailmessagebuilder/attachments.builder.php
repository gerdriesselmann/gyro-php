<?php
require_once dirname(__FILE__) . '/imailmessagebuilder.cls.php';

/**
 * Build message body for a message containing attachments 
 */
class AttachmentsBuilder implements IMailMessageBuilder {
	/**
	 * Builder for the message body
	 * 
	 * @var IMailMessageBuilder
	 */
	protected $message_builder;
	
	/**
	 * Attachments as associative array of name to filename
	 * 
	 * @var MailAttachment[]
	 */
	protected $attachments;
	
	/**
	 * Boundary to sperate attachments
	 * 
	 * @var string
	 */
	protected $boundary;
	
	/**
	 * Constructors
	 * 
	 * @param IMailMessageBuilder $msg_builder Builder for the message body
	 * @param MailAttachment[] $attachments Attachments as array of array with members
	 */
	public function __construct(IMailMessageBuilder $msg_builder, $attachments) {
		$this->message_builder = $msg_builder;
		$this->attachments = $attachments;
		$this->boundary = Common::create_token();
	}
	
	/**
	 * Return mime type of mail as a whole
	 * 
	 * @return string
	 */
	public function get_mail_mime() {
		return  'multipart/mixed; boundary="' . $this->boundary . '"';
	}
	
	/**
	 * Return mail body
	 * 
	 * @return string
	 */
	public function get_body() {
		$blocks = array();
		$blocks[] = $this->create_block(
			$this->message_builder->get_mail_mime(), false, $this->message_builder->get_body(), $this->message_builder->get_additional_headers()
		);
		foreach($this->attachments as $attachment) {
			$blocks[] = $this->create_attachment_block($attachment);
		} 		
		return 
			$this->start_seperator($this->boundary) .
			implode("\n" . $this->start_seperator($this->boundary), $blocks) .
			$this->end_seperator($this->boundary);
	}
	
	/**
	 * Returns generated boundary
	 * 
	 * @return string
	 */
	public function get_boundary() {
		return $this->boundary;
	}
	
	/**
	 * Return seperator 
	 */
	protected function start_seperator($boundary) {
		return "--" . $boundary . "\n";
	}
	
	/**
	 * Return seperator 
	 */
	protected function end_seperator($boundary) {
		return "\n--" . $boundary . "--\n";
	}

	/**
	 * Create a block for an attachment 
	 */
	protected function create_attachment_block(MailAttachment $attachment) {
		return $this->create_block(
			$attachment->get_mime_type() . '; name=' . ConverterFactory::encode($attachment->get_name(), ConverterFactory::MIMEHEADER),
			'base64',
			chunk_split(base64_encode($attachment->get_data()))
		);		
	}	
	
	/**
	 * Create a block in the body 
	 */
	protected function create_block($mime_type, $encoding, $content, $more_headers = array()) {
		$ret = '';
		$header = $more_headers;
		$header['Content-Type'] = $mime_type;
		if ($encoding) {
			$header['Content-Transfer-Encoding'] = $encoding;
		} 	
		
		$ret = Arr::implode("\n", $header, ': ') . "\n\n" . $content;
		return $ret;
	}
	
	/**
	 * Figure out content mime type of file
	 * 
	 * @param string Filename
	 * @return string Mime Type
	 */	
	private function get_attachment_mime($file) {
		if (function_exists('mime_content_type')) {
			return mime_content_type($file);
		}
		return 'application/octet-stream';
	}	

	/**
	 * Return additional mail headers
	 * 
	 * @attention Content-Type header is already added
	 * 
	 * @return $headers Associative array with header name as key
	 */
	public function get_additional_headers() {
		return array();
	}	
}
