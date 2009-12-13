<?php
define('MAIL_LF', "\n"); // "\r\n"

require_once 'Mail.php';
require_once 'Mail/mime.php';

/**
 * Encapsulates an e-mail message, allowing attachments
 * 
 * @note The class relies on the PEAR classes Mail and Mail_Mime
 * 
 * @author Gerd Riesselmann
 * @ingroup Lib
 */
class MailMessage {
	/**
	 * Receipient
	 *
	 * @var String
	 */
	private $to = '';

	/**
	 * Sender
	 *
	 * @private String
	 */
	private $from = '';

	/**
	 * Subject
	 *
	 * @private String
	 */
	private $subject = '';

	/**
	 * Message
	 */
	private $message = '';

	/**
	 * CC
	 *
	 * @private String
	 */
	private $cc = '';
	
	private $content_type = '';

	/**
	 * Attachment (Filename)
	 */
	private $files_to_attach = array();

	/**
	 * constructor
	 */
	public function __construct($subject, $message, $to, $from = '', $content_type = '') {
		$this->subject = trim(Config::get_value(Config::MAIL_SUBJECT) . ' ' . $subject);
		$this->message = $message;
		$this->to = $to;
		$this->from = $from;
		if (empty($content_type)) {
			$this->content_type = 'text/plain; charset=%charset'; 
		}
		else {
			$this->content_type = $content_type;
		}
		$this->content_type = str_replace('%charset', GyroLocale::get_charset(), $this->content_type);  
	}

	/**
	 * Sends email
	 *
	 * @return Status
	 */
	public function send() {
		// Check for injection attack;
		$ret = $this->safety_validate_header();
		if ($ret->is_error()) {
			return $ret;
		}
		
		$headers = array(
			'From' => empty($this->from) ? Config::get_value(Config::MAIL_SENDER, true) : $this->from,
			'Subject' => $this->subject,
		);
		if ($this->cc != '') {
			$headers['Bcc'] = $this->cc;
		}
		$headers['Content-Type'] = $this->content_type;
		
		$msg = new Mail_Mime(MAIL_LF);
		foreach($this->files_to_attach as $name => $file) {
			$msg->addAttachment($file, $this->get_attachment_mime($file), $name);
		}
		$msg->setTXTBody($this->message);

		$params = array(
			'text_charset' => GyroLocale::get_charset(),
			'head_charset' => GyroLocale::get_charset(),
		);		
		$body = $msg->get($params);
		$hdrs = $msg->headers($headers, true);
		
		$mail = $this->create_mailer();
		if ($mail) {
			$ret->merge($mail->send($this->to, $hdrs, $body));
		}
		else {
			$ret->append('Konnte keinen Mailer erzeugen');
		}

		return $ret;
	}
	
	/**
	 * Create Mailer
	 */
	protected function create_mailer() {
		switch (Config::get_value(Config::MAILER_TYPE)) {
			case 'smtp':
				$params = array ();
				$params['host'] = Config::get_value(Config::MAILER_SMTP_HOST, true);
				if (Config::get_value(Config::MAILER_SMTP_USER)) {
					$params['auth'] = true;
					$params['username'] = Config::get_value(Config::MAILER_SMTP_USER);
    				$params['password'] = Config::get_value(Config::MAILER_SMTP_PASSWORD);
				}
				return Mail::factory('smtp', $params);
				break;
			default:
				return Mail::factory('mail');
				break;
		}
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
	 * Append a file to attach
	 */
	public function add_attachment($file_name, $name = '') {
		if ($name == '') {
			$name = $file_name;
		}
		$this->files_to_attach[$name] = $file_name;
	}

	/**
	 * Clears from, subject, cc and to data to avaoid header injection
	 * http://www.anders.com/projects/sysadmin/formPostHijacking/
	 */
	public function preprocess_header() {
		$this->to = $this->safety_preprocess_header_field($this->to);
		$this->from = $this->safety_preprocess_header_field($this->from);
		$this->subject = $this->safety_preprocess_header_field($this->subject);
		$this->cc = $this->safety_preprocess_header_field($this->cc);
	}

	/**
	 * Clears header field to avoid injection
	 * http://www.anders.com/projects/sysadmin/formPostHijacking/
	 */
	private function safety_preprocess_header_field($value) {
		$ret = str_replace("\r", '', $value);
		$ret = str_replace("\n", '', $ret);

		// Remove injected headers
		// From http://www.davidseah.com/archives/2005/09/01/wp-contact-form-spam-attack/
		$find = array("/bcc\:/i", "/Content\-Type\:/i", "/Mime\-Type\:/i", "/cc\:/i", "/to\:/i");
		$ret = preg_replace($find, '**bogus header removed**', $ret);

		return $ret;
	}

	/**
	 * Clears from, subject, cc and to data to avaoid header injection
	 */
	 private function safety_validate_header() {
		$ret = new Status();
		$ret->merge($this->safety_check_header_field($this->to, 'Empfänger'));
		$ret->merge($this->safety_check_header_field($this->from, 'Absender'));
		$ret->merge($this->safety_check_header_field($this->subject, 'Betreff'));
		$ret->merge($this->safety_check_header_field($this->cc, 'CC'));
		$ret->merge($this->safety_check_header_field($this->content_type, 'Content-Tye'));
		$ret->merge($this->safety_check_exploit_strings($this->message, 'Nachricht', true));
		return $ret;
	}

	private function safety_check_header_field(&$value, $type) {
		if (strpos($value, "\r") !== false || strpos($value, "\n") !== false) {
			return new Status($type. ': Zeilenumbrüche sind nicht erlaubt.');
		}

		return $this->safety_check_exploit_strings($value, $type, false);
	}

	private function safety_check_exploit_strings(&$value, $type, $beginLineOnly = false) {
		$err = new Status();
		$find = array($this->safety_prepare_exploit_string("bcc" , $beginLineOnly),
		              $this->safety_prepare_exploit_string("Content\-Type" , $beginLineOnly),
		              $this->safety_prepare_exploit_string("Mime\-Type" , $beginLineOnly),
		              $this->safety_prepare_exploit_string('cc' , $beginLineOnly),
		              $this->safety_prepare_exploit_string('to' , $beginLineOnly));
		$temp = preg_replace($find, '**!HEADERINJECTION!**', $value);
		if (strpos($temp, '**!HEADERINJECTION!**') !== false) {
			$err->append($type . ': "To:", "Bcc:", "Subject:" und weitere reservierte Wörter eines E-Mail-Headers sind nicht erlaubt.');
		}
		
		return $err;
	}

	private function safety_prepare_exploit_string($val, $multiline) {
		if ($multiline)
			return "/^" . $val . "\:/im";
		else
			return "/" . $val . "\:/i";
	}
}
