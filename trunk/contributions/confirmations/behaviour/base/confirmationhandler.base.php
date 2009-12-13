<?php
/**
 * Base class for handling confirmation requests
 * 
 * @author Gerd Riesselmann
 * @ingroup Confirmations
 */
class ConfirmationHandlerBase implements IConfirmationHandler {
	const SUCCESS = 'SUCCESS';
	const EXPIRED = 'EXPIRED';
	const NOTFOUND = 'NOTFOUND';
	
	/**
	 * The confirmation to work upon
	 *
	 * @var DAOConfirmation
	 */
	protected $confirmation = null;

	/**
	 * Constructor
	 * 
	 * @param DAOConfirmations 
	 */	
	public function __construct($confirmation) {
		$this->confirmation = $confirmation;
	}

	/**
	 * Confirm a Confirmation
	 * 
	 * Invokes do_confirm(), which should be overloaded by subclasses
	 * 
	 * @return Status
	 */
	public function confirm() {
		$confirmation = $this->confirmation;
		$success = self::NOTFOUND;
		if ($confirmation) {
			$success = $confirmation->expirationdate > time() ? self::SUCCESS : self::EXPIRED; 
			$temp = clone($confirmation);
			$temp->delete();
		}
		return $this->do_confirm($confirmation, $success);
	}
	
	/**
	 * Template method to be overloaded by subclasses to do what should be done
	 * on successfull confirmation
	 * 
	 * @param DAOConfirmations Data of confirmation, not necessarily up to date, depending on status
	 * @param enum Indicates success or failure
	 * @return Status  
	 */
	protected function do_confirm($confirmation, $success) {
		switch ($success) {
			case self::SUCCESS:
				return new Message(tr('Your request was confirmed', 'confirmations'));
			case self::EXPIRED:
				return new Status(tr('Your request has already expired and could not be confirmed', 'confirmations'));
			default:
				return new Status(tr('No confirmations found for your request', 'confirmations'));
		}
	}
	
	/**
	 * Invoked when this confirmation is created
	 * 
	 * INvokes do_created(), which should be overloaded by subclasses
	 * 
	 * @return Status
	 */
	public function created() {
		$confirmation = $this->confirmation;
		return $this->do_created($confirmation);	
	}

	/**
	 * Template method to be overloaded by subclasses to do what should be done
	 * on creation
	 * 
	 * @param DAOConfirmations Data of confirmation
	 * @return Status
	 *   
	 */
	protected function do_created($confirmation) {
		return new Status();
	}	
}
