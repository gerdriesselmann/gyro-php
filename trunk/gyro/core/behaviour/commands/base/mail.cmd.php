<?php
Load::components('mailmessage');

/**
 * Generic class for sending a mail
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
	/** User to send mail to or an emailaddress */
	protected $email = '';
	
	/** Error obejct */
	protected $err = null;
	
	/**
	 * Constructor
	 * 
	 * @param String Subject of Mail
	 * @param dao_User User to send mail to
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
	 * IMplements a template method and calls functions for subject, from, and template 
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
		
		$attachments = $this->get_attachments();
		
		if ($this->err->is_ok()) {
			$mail = new MailMessage($subject, $message, $to);
			foreach((array)$attachments as $a) {
				$mail->add_attachment($a);
			}
			
			$this->err->merge($mail->send());
		}
		
		return $this->err; 
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
			$view->assign('to', $this->email);
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
}
?>