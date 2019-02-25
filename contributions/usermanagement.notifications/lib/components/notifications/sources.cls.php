<?php
/**
 * Groups information about notification sources
 *
 * Provides a key, a title, and a description
 */
interface INotificationsSource extends ISelfDescribing {
	/**
	 * @return string Immutable internal name of source
	 */
	function get_key();
}

/**
 * A notification source we only know the key
 *
 * This ensures backwards compatibility, since in prior versions,
 * only the key was passed and afterwards translated
 */
class NotificationSource implements INotificationsSource {
	/**
	 * @var string
	 */
	private $key;
	/**
	 * @var string
	 */
	private $title;
	/**
	 * @var string
	 */
	private $description;

	/**
	 * NotificationSource constructor.
	 * @param string $key
	 * @param string $title
	 * @param string $description
	 */
	public function __construct($key, $title, $description = '') {
		$this->key = $key;
		$this->title = $title;
		$this->description = $description;
	}


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
		return $this->description;
	}

	/**
	 * @return string Immutable internal name of source
	 */
	function get_key() {
		return $this->key;
	}
}

/**
 * A notification source we only know the key
 *
 * This ensures backwards compatibility, since in prior versions,
 * only the key was passed and afterwards translated
 */
class NotificationSourceByKey implements INotificationsSource {
	/**
	 * @var string
	 */
	private $key;

	/**
	 * NotificationSourceByKey constructor.
	 * @param string $key
	 */
	public function __construct($key) {
		$this->key = $key;
	}


	/**
	 * Get title for this class
	 *
	 * @return string
	 */
	public function get_title() {
		Load::models('notifications');
		return Notifications::translate_source($this->key);
	}

	/**
	 * Get description for this instance
	 *
	 * @return string
	 */
	public function get_description() {
		return '';
	}

	/**
	 * @return string Immutable internal name of source
	 */
	function get_key() {
		return $this->key;
	}
}