<?php
/**
 * Facade class for vote aggregates
 */
class VotesAggregates {
	/**
	 * Get votes aggregates for instance
	 *
	 * @param IDataObject $inst
	 * @return DAOVotesaggregates
	 */
	public static function get_for_instance(IDataObject $inst) {
		return DB::get_item('votesaggregates', 'instance', InstanceReferenceSerializier::instance_to_string($inst));
	}

	/**
	 * Aggregates average et al for given instance 
	 *
	 * Returns array with following properties:
	 * - average: Average vote
	 * - numtotal: Total number of votes
	 * - instance: The instance given
	 * 
	 * @param IDataObject $instance
	 * @return array
	 */
	public static function aggregate_for_instance(IDataObject $instance) {
		$dao = Votes::create_instance_adapter($instance);
		/* 
		 * @var $query DBQuerySelect
		 * @var $dao DAOVotes 
		 */
		$query = $dao->create_select_query();
		$query->set_fields(array(
			'SUM(value * weight)' => 'value',
			'SUM(weight)' => 'count'
		));
		$result = DB::query($query->get_sql());
		
		$data = $result->fetch();
		$value = $data['value'];
		$count = $data['count'];
		$avg = ($count > 0) ? $value / $count : 0;

		$ret = array(
			'instance' => $instance,
			'average' => $avg, 
			'numtotal' => $count
		);
		return $ret;
	}
	
	/**
	 * Create adapter to find all votes for a given instance type
	 *
	 * @param string $instance_type
	 * @return DAOVotesaggregates
	 */
	public static function create_type_adapter($instance_type) {
		$dao = new DAOVotesaggregates();
		$dao->add_where('instance', DBWhere::OP_LIKE, "$instance_type%");
		return $dao;
	}
	
	/**
	 * Returns n top voted aggregsates of given type 
	 *
	 * @param string $type
	 * @param integer $limit
	 * @return array
	 */
	public static function get_top_voted_of_type($type, $limit) {
		$dao = self::create_type_adapter($type);
		/* @var $dao DAOVotesaggregates */
		$dao->sort('average', DAOVotesaggregates::DESC);
		$dao->limit(0, $limit);
		return $dao->find_array();
	}
}
