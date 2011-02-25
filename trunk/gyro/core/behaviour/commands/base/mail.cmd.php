<?php
Load::components('mailmessage');

/**
 * Generic class for sending a mail
 *
 * The class gets passed a template which then is rendered. Within the template
 * the command itself is available as $mailcmd.
 *
 * The template may set title, content-type, alternate messages and attachments.
 *
 * Subclasses can be specialized to provide attachments and a mail footer for every mail.
 *
 * @section HTML HTML Mail
 *
 * A template may contain HTML. If so, it should call
 *
 * @code
 * $mailcmd->set_is_html(true);
 * @endcode
 *
 * HTML should have a plain text fallback. The easies ways to achieve this, is to
 * pass the view itself, which will autmatically get converted to plain text:
 *
 * @code
 * $mailcmd->set_alt_message($self);
 * @endcode
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

	/** Error object */
	protected $err = null;

	/**
	 * The view. Made a member to be comparable to $alt_message
	 * @var IView
	 */
	protected $view;

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
			$mail->set_alt_message($this->compute_alt_message($message));

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
	 * Compute the alternate message
	 *
	 * @param string $org_message Original message. Converted to plain text, if the alt message is the current view
	 */
	protected function compute_alt_message($org_message) {
		$ret = '';
		if ($this->alt_message instanceof IView) {
			if ($this->alt_message == $this->view) {
				$ret = $org_message; // Prevent view from beeing rendered twice
				if ($this->is_html()) {
					$ret = ConverterFactory::decode($ret, ConverterFactory::HTML_EX, array('p' => "\n\n", 'a' => '$url$'));
				}
			} else {
				$ret = $this->alt_message->render();
			}
		}
		return $ret;
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
		$this->view = ViewFactory::create_view(IViewFactory::MESSAGE, $this->template);
		if ($this->view) {
			foreach(Arr::force($this->template_args) as $name => $value) {
				$this->view->assign($name, $value);
			}
			$this->view->assign('to', $this->get_to());
			$this->view->assign('mailcmd', $this);
			return $this->view->render();
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
	 * Returns wether this is a HTML mail or not
	 *
	 * @return bool
	 */
	protected function is_html() {
		return $this->html_mail;
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

	/**
	 * Add an attachment
	 *
	 * @param string $file_name Absolute path to file
	 * @param string $name Optional name of attachment
	 */
	public function add_attachment($file_name, $name = '') {
		$this->files_to_attach[$file_name] = $name;
	}
}
