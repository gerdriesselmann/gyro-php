<?php
Load::components('mailmessage');

/**
 * Generic class for sending a mail
 * 
 * The class gets passed a template which then is rendered. Within the template
 * the command itself is available as $mailcmd.
 * 
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */ 
class MailBaseCommand extends CommandBase {
	/** The name of the template */
	protected $template = null;
	/** Asscoiative array of template arguments */
	protected $template_args = false;
	/** Subject of mail	 */
	protected $subject;
	/** Email address */
	protected $email = '';
	/** HTML or not? */
	protected $html_mail = false;
	
	/** Error obejct */
	protected $err = null;
	
	protected $alt_message = '';
	protected $files_to_attach = array();
	
	/**
	 * Constructor
	 * 
	 * @param String Subject of Mail
	 * @param String Mail address
	 * @param String Template name
	 * @param Array Associative array of template arguments
	 */
	public function __construct($subject, $email, $template, $template_args = array()) {
		$this->template = $template;
		$this->template_args = $template_args;
		$this->subject = $subject;
		$this->email = $email;
		
		$this->err = new Status();	
	}

	public function can_execute($user) {
		return ( 
			$user && 
			$user != $this->email 
		);
	}
	
	/**
	 * Send Mail
	 * 
	 * Implements a template method and calls functions for subject, from, and template 
	 */
	public function execute() {
		$to = $this->get_to();
		if (empty($to)) {
			return $this->err; // No recipient, no mail...
		}		
		
		$message = $this->get_message();
		$append = $this->get_message_extension();
		if (!empty($append)) {
			$message .= "\n\n------------------------\n\n" . $append;
		}
		
		$subject = $this->get_subject();
		
		if ($this->err->is_ok()) {			
			$mail = $this->create_mail_message($subject, $message, $to);

			// Set alternative message
			if ($this->alt_message instanceof IView) {
				$this->alt_message = $this->alt_message->render();
			}
			$this->alt_message = ConverterFactory::decode($this->alt_message, ConverterFactory::HTML_EX);			
			$mail->set_alt_message($this->alt_message);
			
			// Set custom attachments
			foreach($this->files_to_attach as $filename => $name) {
				$mail->add_attachment($filename, $name);
			}
			
			// Set overlaoded attachments
			foreach(Arr::force($this->get_attachments(), false) as $a) {
				$mail->add_attachment($a);
			}
			
			// Send
			$this->err->merge($mail->send());
		}
		
		return $this->err; 
	}
	
	/**
	 * Create the mail message instance
	 * 
	 * @return MailMessage
	 */
	protected function create_mail_message($subject, $message, $to) {
		$content_type = $this->html_mail ? 'text/html; charset=%charset' : '';
		return new MailMessage($subject, $message, $to, '', $content_type);
	}
	
	/**
	 * Set an error message
	 */
	protected function set_error($message) {
		$this->err->append($message);
	}
	
	/**
	 * Create Mail Subject
	 */
	public function set_subject($subject) {
		$this->subject = $subject;
	}
	
	/**
	 * Create Mail Subject
	 */
	protected function get_subject() {
		return $this->subject;
	}
	
	/**
	 * Return mail message
	 */
	protected function get_message() {
		$view = ViewFactory::create_view(IViewFactory::MESSAGE, $this->template);
		if ($view) {
			foreach(Arr::force($this->template_args) as $name => $value) {
				$view->assign($name, $value);
			}	
			$view->assign('to', $this->get_to());
			$view->assign('mailcmd', $this);
			return $view->render();
		} 
		else {
			$this->set_error(tr('Mail message view not created', 'commands'));			
		}		
	}
	
	/**
	 * Returns default text, that gets appends to every mail
	 */
	protected function get_message_extension() {
		return '';		
	} 
	
	/**
	 * Create Mail Recipient
	 */
	protected function get_to() {
		return Cast::string($this->email);
	}
	
	/**
	 * Returns array of filenames to attach
	 */
	protected function get_attachments() {
		return array();
	}

	/**
	 * Set if this is a HTML mail or not. 
	 * 
	 * Can be set through template:
	 * 
	 * @code
	 * $mailcmd->set_is_html(true);
	 * @endcode
	 */
	public function set_is_html($yesno) {
		$this->html_mail = $yesno;
	}
	
	/**
	 * Set alternative message
	 * @param string $alt_message
	 * 
	 */
	public function set_alt_message($alt_message) {		
		$this->alt_message = $alt_message;		
	}
	
	/**
	 * Get alternative message
	 * 
	 * @return string $alt_message
	 */
	public function get_alt_message() {
		return $this->alt_message;	
	}	
	
	public function add_attachment($file_name, $name) { 
		$this->files_to_attach[$file_name] = $name;
	}
}
