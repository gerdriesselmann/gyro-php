<?php
/**
 * Base interfae for mail message builders
 */
interface IMailMessageBuilder {
	/**
	 * Return mime type of mail as a whole
	 * 
	 * @return string
	 */
	public function get_mail_mime();
	
	/**
	 * Return mail body
	 * 
	 * @return string
	 */
	public function get_body();
}
