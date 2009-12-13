<?php
/**
 * Table for systemupdate version control
 * 
 * @author Gerd Riesselmann
 * @ingroup SystemUpdate
 */
class DAOSystemupdates extends DataObjectBase {
	public $component;
	public $version;
	
	/**
	 * Return table definition
	 *
	 * @return IDBTable
	 */
	function create_table_object() {
		return new DBTable(
			'systemupdates',
			array(
				new DBFieldText('component', 50, null, DBFieldText::NOT_NULL),	
				new DBFieldInt('version', null, DBFieldInt::UNSIGNED | DBFieldInt::NOT_NULL)				
			),
			'component'
		);
	}
		
}
