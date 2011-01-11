<?php
/**
 * Facade class for notification exceptions
 */
class NotificationsExceptions {
	/**
	 * Returns true if notifications for given source and source id are
	 * excluded for given user
	 */
	public static function excluded($id_user, $source, $source_id) {
		$ret = false;
		if ($id_user && $source && $source_id) {
			$dao = new DAONotificationsexceptions();
			$dao->id_user = $id_user;
			$dao->source = $source;
			$dao->source_id = $source_id;
			$ret = ($dao->count() > 0);
		}
		return $ret;
	}
}
