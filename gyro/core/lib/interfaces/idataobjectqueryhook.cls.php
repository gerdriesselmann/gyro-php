<?php
/**
 * Interface for classes that hook into query building on dataobject
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDataObjectQueryHook {
	/**
	 * Modify the query given. Hok should take class of query (DBQueryInsert, DBQuerySelect etc) into consideration
	 *
	 * @param IDBQuery $query
	 */
	public function configure_query($query);
}
