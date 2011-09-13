<?php
/**
 * Model class for a rich content item 
 * 
 * @ingroup PostBase
 * @author Gerd Riesselmann
 */
abstract class DataObjectPostBase extends DataObjectTimestampedCached implements ISelfDescribing {
	private static $global_extensions = array();
	
	public $id;
	public $title;
	public $teaser;
	public $text;
	public $meta_title;
	public $meta_keywords;
	public $meta_description;
	
	/**
	 * Create Table features
	 *
	 * @return DBTable
	 */
	protected function create_table_object() {
		return new DBTable(
			$this->get_model_name(),
			$this->collect_field_definitions(),
			'id',
			$this->collect_relations()
		);
	}

	/**
	 * Collect all table fields
	 *
	 * Should be overloaded only by classes which single purpose is to be overloaded itself.
	 * The rest should overload get_additional_field_definitions()
	 *
	 * @return array
	 */
	protected function collect_field_definitions() {
		return array_merge(
			array(
				new DBFieldInt('id', null, DBFieldInt::PRIMARY_KEY),
				new DBFieldText('title', 200, null, DBField::NOT_NULL),
				new DBFieldTextHtml('text', DBFieldText::BLOB_LENGTH_LARGE, null, $this->get_text_field_policy()),
			),
			$this->get_teaser_field(),
			$this->get_meta_tag_fields(),
			$this->get_global_field_definitions(),
			$this->get_timestamp_field_declarations(),
			$this->get_additional_field_definitions()
		);
	}

	/**
	 * Should be overloaded only by classes which single purpose is to be overloaded itself.
	 * The rest should overload get_additional_relations()
	 *
	 * @return array Array of DBRelation
	 */
	protected function collect_relations() {
		return $this->get_additional_relations();
	}
	
	/**
	 * Extend every instance that extends PostBase by arbitrary many fields
	 * 
	 * @param array $arr_fields Array of DBField instances
	 */
	public static function extend_table($arr_fields) {
		self::$global_extensions = $arr_fields;
	}
	
	/**
	 * Too be overloaded. Return addition table name
	 * 
	 * @return string
	 */
	abstract protected function get_model_name();

	/**
	 * Too be overloaded. Return addition table fields
	 * 
	 * @return array Array of IDBField
	 */
	protected function get_additional_field_definitions() {
		return array();
	}

	/**
	 * Too be overloaded. Return addition relations
	 * 
	 * @return array Array of DBRelation
	 */
	protected function get_additional_relations() {
		return array();
	}
	
	/**
	 * Return policy for teaser
	 * 
	 * @return Int DBField::NONE or DBField::NOT_NULL
	 */
	protected function get_teaser_field_policy() {
		return DBField::NOT_NULL;
	}
	
	/**
	 * Return policy for text
	 * 
	 * @return Int DBField::NONE or DBField::NOT_NULL
	 */
	protected function get_text_field_policy() {
		return DBField::NOT_NULL;
	}
	
	/**
	 * Return global extension
	 * 
	 * @return array Array of IDBField
	 */
	protected function get_global_field_definitions() {
		return self::$global_extensions;
	}

	/**
	 * Returns fields for meta tag information
	 * 
	 * @return array
	 */
	protected function get_meta_tag_fields() {
		return array(
			new DBFieldText('meta_title', 200, null, DBField::NONE),
			new DBFieldText('meta_keywords', 255, null, DBField::NONE),
			new DBFieldText('meta_description', DBFieldText::BLOB_LENGTH_SMALL, null, DBField::NONE),
		);
	}
	
	/**
	 * Returns teaser field
	 * 
	 * @return array
	 */
	protected function get_teaser_field() {
		return array(
			new DBFieldText('teaser', DBFieldText::BLOB_LENGTH_SMALL, null, $this->get_teaser_field_policy()),
		);
	}
	
	
	// -- Getters ---
	
	/**
	 * Get text for this instance. Text is converted using HtmlText::OUTPUT chains
	 *  
	 * @return string 
	 */
	public function get_text() {
		return HtmlText::apply_conversion(HtmlText::OUTPUT, $this->text, $this->get_table_name());
	}
	
	/**
	 * Get text for this instance. Text is converted using HtmlText::OUTPUT chains
	 *  
	 * @return string 
	 */
	public function get_teaser() {
		return ($this->teaser) ? $this->teaser : String::substr_word(String::clear_html($this->text), 0, 300);
	}
	
	/**
	 * Returns meta title
	 */
	public function get_meta_title() {
		return $this->meta_title ? $this->meta_title : $this->get_title();
	}
	
	/**
	 * Returns meta description
	 */
	public function get_meta_description() {
		return $this->meta_description ? $this->meta_description : $this->get_teaser();
	}
	
	// **************************************
	// Dataobject
	// **************************************
 	
	/**
	 * Return array of sortable columns. Array has column name as key and a sort type (enum) as value  
	 */
	public function get_sortable_columns() {
		return array(
			'title' => new DBSortColumn('title', tr('Title', 'postbase'), DBSortColumn::TYPE_TEXT),
			'creationdate' => new DBSortColumn('creationdate', tr('Creation Date'), DBSortColumn::TYPE_DATE, DBSortColumn::ORDER_BACKWARD),		
		);	
	}
	
	/**
	 * Get the column to sort by default
	 */
	public function get_sort_default_column() {
		return 'title';	
	}
	
	/**
	 * To be overloaded. Returns array of actions with action title as key and action description as value 
	 *
	 * Subclasses can return array of actions, this class will detect if they are commands or actions.
	 * 
	 * Optionally, params can be added in brackets like 'status[DISABLED]' => 'Disable this item'.  
	 * 
	 * @param string $context
	 * @param mixed $user
	 * @param mixed $params
	 * @return array
	 */
	protected function get_actions_for_context($context, $user, $params) {
		$ret = array();		
		$ret['edit'] = tr('Edit');		
		return $ret;
	}	
	
	// **************************************
	// ISelfDescribing
	// **************************************
	
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
		return $this->teaser;
	}
}
