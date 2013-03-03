<?php
/**
 * Sequences facade
 */
class Sequences {
	/**
	 * Return sequences with next value set
	 *
	 * @param $slot
	 * @return bool|DAOSequences
	 */
	public static function next($slot) {
		$ret = false;
		$seq = new DAOSequences();
		$seq->slot = $slot;
		$query = $seq->create_select_query(DBQuerySelect::FOR_UPDATE);
		if ($seq->query($query, IDataObject::AUTOFETCH)) {
			$ret = clone($seq);
			$ret->current += 1;
		} else {
			$seq->current = 0;
			$err = $seq->insert();
			if ($err->is_ok()) {
				$ret = self::next($slot);
			}
		}
		return $ret;
	}
}