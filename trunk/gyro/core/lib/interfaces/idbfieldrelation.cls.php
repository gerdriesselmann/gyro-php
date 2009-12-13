<?php
/**
 * Interface for defining a relation between fields beeing part of a table relation 
 *  
 * We use terminology source and target here. Of course what is source and what is target depends on 
 * the perspective, so we could also call this A and B.
 * 
 * A field relation is defined for one field on source table that relates to one field on target table  
 * 
 * @author Gerd Riesselmann
 * @ingroup Interfaces
 */
interface IDBFieldRelation {
	/**
	 * Return source table name
	 * 
	 * @return string
	 */	
	public function get_source_field_name();

	/**
	 * Return target table name
	 * 
	 * @return string
	 */	
	public function get_target_field_name();
	
	/**
	 * Returns an IDBFieldRelation with source field as target field and vice versa
	 *
	 * @return IDBFieldRelation
	 */
	public function reverse();
}
