<?php
/**
 * Contains default stati commonly used
 *  
 * @author Gerd Riesselmann
 * @ingroup Status
 */
class Stati {
	const UNCONFIRMED = 'UNCONFIRMED';
	const ACTIVE = 'ACTIVE';
	const DISABLED = 'DISABLED';
	const DELETED = 'DELETED';
	
	/**
	 * Returns all statis as assoziative array with status as key and translation as value
	 */
	public static function get_stati() {
		return array(
			self::UNCONFIRMED => tr(self::UNCONFIRMED, 'status'),
			self::ACTIVE => tr(self::ACTIVE, 'status'),
			self::DISABLED => tr(self::DISABLED, 'status'),
			self::DELETED => tr(self::DELETED, 'status')
		);
	}
}
