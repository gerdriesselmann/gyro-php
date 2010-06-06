<?php
/**
 * Stored tweets 
 * 
 * @ingroup Twitter
 * @author Gerd Riesselmann
 */
class DAOTweets extends DataObjectTimestampedCached implements ISelfDescribing {
	public $id;
	public $id_twitter;
	public $username;
	public $title;
	public $message;
	public $message_html;
	
	protected function create_table_object() {
	    return new DBTable(
	    	'tweets',
			array_merge(array(
				new DBFieldInt('id', null, DBFieldInt::AUTOINCREMENT | DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL),
				new DBFieldText('id_twitter', 20, null, DBField::NOT_NULL),
				new DBFieldText('username', 20, null, DBField::NOT_NULL),
				new DBFieldText('title', 140, null, DBField::NOT_NULL),
				new DBFieldText('message', 140, null, DBField::NOT_NULL),
				new DBFieldText('message_html', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NOT_NULL),
			), $this->get_timestamp_field_declarations()),
			'id'		
	    );
	}

	// ----------------------------------------
	// ISelfDescribing
	// ----------------------------------------
	
	/**
	 * Get title for this class
	 * 
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * Get description for this instance
	 *  
	 * @return string 
	 */
	public function get_description() {
		return '';
	}	
}