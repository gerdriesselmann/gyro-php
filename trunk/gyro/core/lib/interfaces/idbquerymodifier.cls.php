<?php
/**
 * Base interface for all query modifying classes like filters, sorters, and alike 
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */  
interface IDBQueryModifier {
	/**
	 * Do what should be done
	 * 
	 * @param DataObjectBase The current query to  be modified
	 */
	public function apply($query);	 
}
