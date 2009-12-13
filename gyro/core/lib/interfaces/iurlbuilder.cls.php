<?php
/**
 * Interface for classes that can create URLs
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IUrlBuilder {
	const ABSOLUTE = true;
	const RELATIVE = false;

	/**
	 * Build the URL
	 * 
	 * @param bool $absolute_or_relative True to build an absolute URL, false to return path only
	 * @param mixed $params Further parameters to use to build URL
	 */
	public function build_url($absolute_or_relative, $params = null);
}
