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
	public static function get($component, $connection) {
		$dao = new DAOSystemupdates();
		$dao->component = $component;
		$dao->connection = $connection;
		
		$query = $dao->create_select_query();
		
		$result = DB::query($query->get_sql(), $connection);
		if ($data = $result->fetch()) {
			$dao = new DAOSystemupdates();
			$dao->read_from_array($data);
			return $dao;
		}
		
		return false;
	}
	
	/**
	 * Create update information entry
	 *
	 * @param string $component
	 * @param DAOSystemupdates $result
	 */
	public static function create($component, $connection, &$result) {
		$params = array(
			'component' => $component,
			'version' => 0 
		); 
		
		// We must go low level, cause of connection
		$dao = new DAOSystemupdates();
		$dao->connection = $connection;
		$dao->read_from_array($params);
		
		$query = $dao->create_insert_query();
		
		$ret = DB::execute($query->get_sql(), $connection);
		if ($ret->is_ok()) {
			$dao->id = DB::last_insert_id($connection);
			$result = $dao;
		}

		return $ret;
	}
	
	/**
	 * Update entry
	 */
	public static function update(DAOSystemupdates $entry, $connection) {
		$entry->connection = $connection;
		$query = $entry->create_update_query();
		return DB::execute($query->get_sql(), $connection);
	}
}