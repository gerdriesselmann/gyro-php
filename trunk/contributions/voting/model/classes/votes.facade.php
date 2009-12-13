<?php
Load::models('votesaggregates');

/**
 * Facade class for votes
 */
class Votes {
	/**
	 * Create adapter to find votes for instance
	 *
	 * @return DAOVotes
	 */
	public static function create_instance_adapter(IDataObject $inst) {
		$dao = new DAOVotes();
		$dao->instance = $inst;
		return $dao;  
	}
	
	/**
	 * Find all votes for instance
	 *
	 * @param IDataObject $inst
	 * @return array
	 */
	public static function find_for_instance(IDataObject $inst) {
		$dao = self::create_instance_adapter($inst);
		return $dao->find_all();
	}
		
	/**
	 * Calucate average for instance
	 * 
	 * @param IDataObject $inst
	 * @return int
	 */
	public static function get_average_for_instance(IDataObject $inst, $precision = 0) {
		$aggr = VotesAggregates::get_for_instance($inst);
		if ($aggr) {
			return $aggr->get_average($precision);
		}
		return 0;
	}
	
	/**
	 * Create a vote instance
	 *
	 * @param array $params
	 * @param IDataObject $instance
	 * @param DAOVotes $created
	 * @return Status
	 */
	public static function create($params, $instance, &$created) {
		$params['instance'] = $instance;
		$cmd = CommandsFactory::create_command('votes', 'create', $params);
		$ret = $cmd->execute();
		$created = $cmd->get_result();
		return $ret;
	}
}
