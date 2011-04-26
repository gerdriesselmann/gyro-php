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
	
	/**
	 * Return additional mail headers
	 * 
	 * @attention Content-Type header is already added
	 * 
	 * @return $headers Associative array with header name as key
	 */
	public function get_additional_headers();	
}
