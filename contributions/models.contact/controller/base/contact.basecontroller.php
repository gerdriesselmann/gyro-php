<?php
/**
 * Defines a view route for contact forms
 * 
 * @author Gerd Riesselmann
 * @ingroup Contact
 */
class ContactBaseController extends ControllerBase {
	
	/**
 	 * Return array of routes this controller takes responsability
 	 */
 	public function get_routes() {
		$base = $this->get_base_route();
 		$ret = array(
 			'form' => new ExactMatchRoute("$base/", $this, 'contact_form', new NoCacheCacheManager()),
 		);
 		return $ret;
 	}

	/**
	 * Base path of all contact form related routes
	 *
	 * @return string
	 */
	protected function get_base_route() {
		return 'contact';
	}
 	
 	/** 
 	 * Show search result
 	 * 
 	 * @param $page_data PageData
 	 */
 	public function action_contact_form($page_data) {
		$page_data->in_history = false;
 		$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
		$page_data->breadcrumb = array(tr('Contact', 'contact'));
		$page_data->head->title = tr('Contact Us', 'contact');
		$page_data->head->description = tr('Send us an e-mail through the contact form.', 'contact');

		Load::tools('formhandler');
		$formhandler = new FormHandler('frmcontact');
		if ($page_data->has_post_data()) {
			$this->do_contact_form($page_data, $formhandler);
		}

		$view = ViewFactory::create_view(IViewFactory::CONTENT, 'contact/form', $page_data);
		$formhandler->prepare_view($view);
		$view->render(); 
 	}

	/**
	 * Send contact form message
	 *
	 * @param PageData $page_data
	 * @param FormHandler $formhandler
	 */
	private function do_contact_form(PageData $page_data, FormHandler $formhandler) {
		$err = $formhandler->validate();
		if ($err->is_ok()) {
			Load::commands('generics/mail');
			$post = $page_data->get_post();
			$data = $post->get_array();
			if (empty($data['name'])) { $err->merge(tr('Please provide a name.', 'contact')); }
			if (empty($data['email'])) {
				$err->merge(tr('Please provide an e-mail address.', 'contact'));
			} elseif (!Validation::is_email($data['email'])) {
				$err->merge(tr('Your e-mail address looks invalid.', 'contact'));
			}
			if (empty($data['message'])) { $err->merge(tr('The message should not be empty.', 'contact')); }
			if (empty($data['subject'])) { $data['subject'] = tr('Contact Form Message', 'contact'); }
			if ($err->is_ok()) {
				$cmd = new MailCommand($data['subject'], Config::get_value(Config::MAIL_SUPPORT), 'contact/mail', $post->get_array());
				$err->merge($cmd->execute());
			}
		}
		$formhandler->finish($err, tr('Your message has been sent successfully.', 'contact'));
	}

}
