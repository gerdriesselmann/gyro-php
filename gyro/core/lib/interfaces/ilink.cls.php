<?php
require_once dirname(__FILE__) . '/iselfdescribing.cls.php';
require_once dirname(__FILE__) . '/iurlbuilder.cls.php';

/**
 * Interface for a link
 * 
 * @deprecated Actually never used
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface ILink extends ISelfDescribing, IUrlBuilder {
	
}
