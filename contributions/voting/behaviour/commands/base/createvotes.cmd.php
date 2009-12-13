<?php
/**
 * Comamdn to create a vote
 */
class CreateVotesBaseCommand extends CommandChain {
	protected function do_execute() {
		$params = $this->get_params();
		$ret = new Status();
		
		Load::commands('generics/create');
		$create_cmd = new CreateCommand('votes', $params);
		$ret->merge($create_cmd->execute());
		/* @var $created_vote DAOVotes */
		$created_vote = $create_cmd->get_result();
		$this->set_result($created_vote);
		
		if ($ret->is_ok()) {
			$ret->merge($this->on_success($created_vote));
		}
		if ($ret->is_ok()) {
			$aggr_params = VotesAggregates::aggregate_for_instance($created_vote->instance);
			$ret->merge($this->aggregate($created_vote, $aggr_params));
		}
		return $ret;
	}
	
	/**
	 * Called after vote was created
	 *
	 * @param DAOVotes $vote
	 * @return Status
	 */
	protected function on_success(DAOVotes $vote) {
		Load::commands('generics/clearcache');
		$this->append(new ClearCacheCommand($vote->instance));		
		
		return new Status();
	}
	
	/**
	 * Aggregates results and saves them
	 *
	 * @param DAOVotes $vote
	 * @param array $params
	 * @return Status
	 */
	protected function aggregate(DAOVotes $vote, $params) {
		$aggregate = VotesAggregates::get_for_instance($vote->instance);
		if ($aggregate) {
			// Update
			$this->append(CommandsFactory::create_command($aggregate, 'update', $params));
		}
		else {
			// Create
			$this->append(CommandsFactory::create_command('votesaggregates', 'create', $params));
		}
		
		return new Status();
	}

	/**
	 * Returns title of command.
	 */
	public function get_name() {
		return 'create';
	}
	
}
