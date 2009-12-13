<?php
/**
 * Model class for Votes
 * 
 * Votes can hold values from 0 to 100 (so they contain percents, actually)
 */
class DAOVotes extends DataObjectBase {
	public $id;
	public $instance;
	public $value;
	public $weight;
	public $voterid;
	public $creationdate;
	
	// Table structure
	protected function create_table_object() {
	    return new DBTable(
	    	'votes',
			array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldInstanceReference('instance'), 
				new DBFieldInt('value'),
				new DBFieldInt('weight'),
				new DBFieldText('voterid', 30),
				new DBFieldDateTime('creationdate', null, DBFieldDateTime::TIMESTAMP),
			),
			'id'			
	    );
	}

 	/**
 	 * Validate this object
 	 * 
 	 * @return Status Error
 	 */
 	public function validate() {
 		$ret = parent::validate();
 		if ($this->value < 0 || $this->value > 100) {
 			$ret->append(tr('Vote must be between 0 and 100', 'voting'));
 		}
 		if (empty($this->weight)) {
 			$this->weight = 1;
 		}
 		return $ret;
 	}

 	
}
