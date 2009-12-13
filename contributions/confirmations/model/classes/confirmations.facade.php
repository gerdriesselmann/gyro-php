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
		$confirmation = new DAOConfirmations();
		$confirmation->code = $code;
		$confirmation->id = $id;
		if ($confirmation->find(IDataObject::AUTOFETCH)) {
			Load::directories('behaviour/confirmationhandlers');
			$cls = String::to_upper(String::plain_ascii($confirmation->action, ''), 1) . 'ConfirmationHandler';
			if (class_exists($cls)) {
				return new $cls($confirmation);
			}
		}
		// Default implementation handles missing confirmation...
		return new ConfirmationHandlerBase(false);				
	}
}
