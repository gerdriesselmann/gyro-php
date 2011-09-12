<?php
require_once dirname(__FILE__) . '/dataobject.post.base.cls.php';

/**
 * A post that has an origin (auithor, license, etc)
 */
abstract class DataObjectPostOriginatedBase extends DataObjectPostBase {
	public $license;
	public $originator;             // e.g. author, Flickr User Name
	public $originator_source;      // e.g. Flickr, private, blog
	public $originator_url;         // e.g. Flickr URL, Blog URL. Validated as URL!
	public $originator_data;        // Any additional stuff, e.g. Flickr ID

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
				new DBFieldText('license', 100),
				new DBFieldText('originator', 100),
				new DBFieldText('originator_source', 100),
				new DBFieldTextUrl('originator_url', null, DBField::NONE),
				new DBFieldText('originator_data', 255)
			),
			parent::collect_field_definitions()
		);
	}
}
