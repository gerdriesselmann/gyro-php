<?php
/**
 * Confirmations facade class
 * 
 * @author Gerd Riesselmann
 * @ingroup Confirmations
 */
class Confirmations {
	/**
	 * Create a confirmation
	 * 
	 * @return Status 
	 */
	public static function create($id_item, $data, $action, &$result = null) {
		self::remove_expired();
		
		$params = array(
			'id_item' => $id_item,
			'data' => $data,
			'action' => $action
		);
		$cmd = CommandsFactory::create_command('confirmations', 'create', $params);
		$ret = $cmd->execute();
		if (!is_null($result)) {
			$result = $cmd->get_result();
		}
		return $ret;		
	}
	
	/**
	 * Create a confirmatin handler suitable for the desired action of confirmation for given code
	 */
	public static function create_confirmation_handler($id, $code) {
		self::remove_expired();
		
		$confirmation = new DAOConfirmations();
		$confirmation->code = $code;
		$confirmation->id = $id;
		if ($confirmation->find(IDataObject::AUTOFETCH)) {
			return $confirmation->create_handler();
		}
		// Default implementation handles missing confirmation...
		return new ConfirmationHandlerBase(false);				
	}
	
	/**
	 * Remove expired confirmations
	 * 
	 * @return Status
	 */
	public static function remove_expired() {
		$c = new DAOConfirmations();
		// Don't delete all expired, since users won't get an "Expired" messages in that case, 
		// only "Not found", which might be confusung.
		// Deleting confirmations that expired a month ago (given that usual expiration time is 
		// about a day) seems OK, though      
		$c->add_where('expirationdate', '<', time() - 30 * GyroDate::ONE_DAY);
		return $c->delete(DataObjectBase::WHERE_ONLY);
	}
}
