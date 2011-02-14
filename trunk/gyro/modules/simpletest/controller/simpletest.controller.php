<?php
/**
 * Controller for running SimpleTest unit tests
 * 
 * Defines action simpletest/run, which starts the tests
 * 
 * @author Gerd Riesselmann
 * @ingroup Simpletest
 */
class SimpleTestController extends ControllerBase {
	/**
	 * Return array of urls which are handled by this controller
 	 */
	public function get_routes() {
		return array(
			new ExactMatchRoute('simpletest/run', $this, 'run_tests', new NoCacheCacheManager()),
			new ExactMatchRoute('simpletest/send_test_mail', $this, 'simpletest_send_test_mail', new NoCacheCacheManager())
		);
	}

	/**
	 * Run Tests
	 *
	 * @param PageData $page_data
	 */
 	public function action_run_tests($page_data) {
 		ob_start();
		Load::components('GyroUnitTestCase');
		
		// load all test classes
		$suite = new TestSuite('Gyro Tests');
		$base_dirs = Load::get_base_directories();
		foreach($base_dirs as $dir) {
			foreach (gyro_glob($dir . 'simpletests/*.test.php') as $inc) {
				$suite->addFile($inc);
			}
		}
		
		Load::directories('simpletests/mocks');
		$suite->run(new DefaultReporter());
		ob_flush();
		exit(); 		
 	} 		 	 			 	
 	
 	/**
 	 * Sends two test mails to the mail admin, one containing plain text, and the other
 	 * one containing HTML.
 	 * 
 	 * @param $page_data
 	 */
 	public function action_simpletest_send_test_mail(PageData $page_data) {
 		$err = new Status();
 		Load::components('mailmessage');
 		
 		// 1. TextMail
 		$text = 'This is a text message with ümlauts';
 		$mail = new MailMessage('Testmail 1 (Plain) with Ümlauts', $text, Config::get_value(Config::MAIL_ADMIN));
 		$err->merge($mail->send());
 		
 		// 2. HTML Mail
 		$text = html::h('Heading', 1) . html::p('This is a HTML message with ümlauts');
 		$mail = new MailMessage('Testmail 2 (HTML)', $text, Config::get_value(Config::MAIL_ADMIN), '', MailMessage::MIME_HTML);
 		$err->merge($mail->send());

 		// 3. HTML Mail with attachment
 		$text = html::h('Heading', 1) . html::p('This is a HTML message with ümlauts and 1 Attachment');
 		$mail = new MailMessage('Testmail 3 (HTML + Attachment)', $text, Config::get_value(Config::MAIL_ADMIN), '', MailMessage::MIME_HTML);
 		$mail->add_attachment(dirname(__FILE__) . '/test.pdf', 'a_test.pdf');
 		$err->merge($mail->send());
 		
 		// 4. Alternative Message + attachment
 		$text = html::h('Heading', 1) . html::p('This is the HTML part of a alternate message');
 		$mail = new MailMessage('Testmail 4 (HTML + Alternate + Attachment)', $text, Config::get_value(Config::MAIL_ADMIN), '', MailMessage::MIME_HTML);
 		$mail->set_alt_message('This is the plain text alternative text.');
 		$mail->add_attachment(dirname(__FILE__) . '/test.pdf', 'a_test.pdf');
 		$err->merge($mail->send());
 		
 		if ($err->is_ok()) {
 			$err = new Message('OK. Mail was probably send successful');
 		}
 		$page_data->status = $err;
 	}
} 
