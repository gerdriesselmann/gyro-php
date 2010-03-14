<?php
/**
 * Base class for items where creation and modification date is of interest
 * 
 * If used, just array_merge your table fields and $this->get_timestamp_field_declarations():
 * 
 * @code
 *   ...,
 *   array_merge(array(
 *   	new DBFieldInt('id', ...),
 *      .. more of your fields ...
 *      ), $this->get_timestamp_field_declarations()
 *   ),
 *   ... 
 * @endcode
 * 
 * @since 0.5.1
 * 
 * @author Gerd Riesselmann
 * @ingroup model
 */
class DataObjectTimestampedCached extends DataObjectCached implements ITimeStamped {
	public $creationdate;
	public $modificationdate;
	
	/**
	 * Returns array of field instances for ceration- and modificationdate
	 *
	 * @return array
	 */
	protected function get_timestamp_field_declarations() {
		return array(
			new DBFieldDateTime('creationdate', DBFieldDateTime::NOW, DBFieldDateTime::NOT_NULL),
			new DBFieldDateTime('modificationdate', DBFieldDateTime::NOW, DBFieldDateTime::TIMESTAMP | DBFieldDateTime::NOT_NULL),
		);
	}
	
	 /**
 	 * Insert data. Autoincrement IDs will be automatically set.
 	 * 
 	 * @return Status
 	 */
 	public function insert() {
 		$this->modificationdate = time();
 		$this->creationdate = time();
 		return parent::insert();
 	}

 	/**
 	 * Update current item
 	 * 
 	 * @param int $policy If DBDataObject::WHERE_ONLY is used, no conditions are build automatically
 	 * @return Status
 	 */
 	public function update($policy = self::NORMAL) {
 		$this->modificationdate = time();
 		return parent::update($policy);	
 	}
 	
 	// ---------------------------------
 	// ITimestamped
 	// ---------------------------------
 	
	/**
	 * Return creation date and time
	 *
	 * @return timestamp
	 */
	public function get_creation_date() {
		return $this->creationdate;
	}

	/**
	 * Return modification date and time
	 *
	 * @return timestamp
	 */
	public function get_modification_date() {
		return $this->modificationdate;
	} 	
}
