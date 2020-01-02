<?php
/**
 * Controller for Voting related actions
 */
class VotingController extends ControllerBase {
	/**
 	 * Return array of IRoute this controller takes responsability
 	 */
 	public function get_routes() {
 		$ret = array(
 			new InstanceReferenceRoute('voting/vote/{instance:inst}%', $this, 'voting_vote', new NoCacheCacheManager()),
 		);
 		return $ret;
 	}
	
 	/**
 	 * Process a vote
 	 */
 	public function action_voting_vote(PageData $page_data, $instance) {
 		Session::start();
 		Load::components('referer');
 		$referer = Referer::current();
 		if ($referer->is_internal()) {
 			History::push($referer->build());
 		}
 		$instance = InstanceReferenceSerializier::string_to_instance($instance);
 		if (empty($instance)) {
 			History::go_to(0, new Status(tr('Instance you voted for not found', 'voting')));
 			exit;
 		}
 		
 		if (!$page_data->has_post_data()) {
 			History::go_to(0, new Status(tr('No vote submitted', 'voting')));
 			exit; 			
 		}
 		
 		$created = false;
 		Load::models('votes');
 		$status = Votes::create($page_data->get_post()->get_array(), $instance, $created);
 		if ($status->is_ok()) {
 			$status = new Message(tr('Your vote has been counted', 'voting'));
 		}
 		History::go_to(0, $status);
 	}
}
