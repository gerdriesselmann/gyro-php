<?php
/**
 * Dispatcher interface
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDispatcher {
	public function invoke($page_data);	 
}