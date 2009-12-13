<?php
/**
 * Interface for all confirmation handlers to implement
 *  
 * @author Gerd Riesselmann
 * @ingroup Confirmations
 */
interface IConfirmationHandler {
	/**
	 * Confirm a Confirmation
	 * 
	 * @return Status
	 */
	public function confirm();

	/**
	 * Invoked when this confirmation is created
	 * 
	 * @return Status
	 */
	public function created();
}
