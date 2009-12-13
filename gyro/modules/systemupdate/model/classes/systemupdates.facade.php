<?php
/**
 * Facade for System Update information
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class SystemUpdates {
	/**
	 * Find system update information for given component
	 *
	 * @param string $component
	 * @return DAOSystemupdated
	 */
	public static function get($component) {
		return DB::get_item('systemupdates', 'component', $component);
	}
	
	/**
	 * Create update information entry
	 *
	 * @param string $component
	 * @param DAOSystemupdates $result
	 */
	public static function create($component, &$result) {
		$params = array(
			'component' => $component,
			'version' => 0
		); 
		$cmd = CommandsFactory::create_command('systemupdates', 'create', $params);
		$ret = $cmd->execute();
		$result = $cmd->get_result();
		return $ret;
	}
}