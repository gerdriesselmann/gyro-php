<?php
require_once dirname(__FILE__) . '/viewbase.cls.php';

/**
 * Base class for Views that create simple - non-cachable - content, like E-Mails
 * 
 * @author Gerd Riesselmann
 * @ingroup View
 */
class MessageViewBase extends ViewBase {
	/**
	 * Contructor takes a name only
	 */	
	public function __construct($name) {
		parent::__construct($name, '');
	} 
}
