<?php
/**
 * A relation between two fields in DB
 * 
 * @author Gerd Riesselmann
 * @ingroup Model
 */
class DBFieldRelation implements IDBFieldRelation {
	
	protected $source_field;
	protected $target_field;

	public function __construct($source_field, $target_field) {
		$this->source_field = $source_field;
		$this->target_field = $target_field;
	}
	
	/**
	 * Return source table name
	 * 
	 * @return string
	 */	
	public function get_source_field_name() {
		return $this->source_field;
	}

	/**
	 * Return target table name
	 * 
	 * @return string
	 */	
	public function get_target_field_name() {
		return $this->target_field;
	}

	/**
	 * Returns an IDBFieldRelation with source field as target field and vice versa
	 *
	 * @return IDBFieldRelation
	 */
	public function reverse() {
		return new DBFieldRelation($this->target_field, $this->source_field);
	}	
}
