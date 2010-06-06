<?php
/**
 * Facade for stored tweets
 * 
 * @ingroup Twitter
 * @author Gerd Riesselmann
 * 
 */
class Tweets {
	public static function get_latest_for_user($user, $num) {
		$dao = new DAOTweets();
		$dao->username = $user;
		$dao->sort('creationdate', DataObjectBase::DESC);
		$dao->limit($num);
		return $dao->find_array(); 
	}
}