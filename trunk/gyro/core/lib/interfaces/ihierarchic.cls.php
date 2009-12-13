<?php
/**
 * Interface for things that are part of a hierarhic (parent => child) relationship
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IHierarchic { 	
	/**
	 * Get parent for this item 
	 * 
	 * @return IHierarchic Parent item or null
	 */
	public function get_parent();
	
	/**
	 * Get childs for this item 
	 * 
	 * @return array Array of IHierarchic items
	 */
	public function get_childs();
}
