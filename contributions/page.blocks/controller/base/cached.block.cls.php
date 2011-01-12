<?php
/**
 * A block that caches its content
 * 
 * This blocks renders the IBlock given as $delegate and caches it's output.
 * 
 * @ingroup Blocks
 * @author Gerd Riesselmann
 */
class CachedBlock extends BlockBase {
	/**
	 * @var IBlock
	 */
	protected $delegate;
	/**
	 * @var array
	 */
	protected $cache_id;
	protected $life_time;
	protected $cache_read = false;
	
	/**
	 * Constructor
	 * 
	 * @param string|array $cache_id The cache id. "block" always gets prepended
	 * @param IBlock $delegate The block which output to cache
	 * @param int $life_time Cache duration in seconds
	 */
	public function __construct($cache_id, $delegate, $life_time = GyroDate::ONE_DAY) {
		$this->delegate = $delegate;
		$this->life_time = $life_time;
		$this->cache_id = Arr::force($cache_id, false);
		array_unshift($this->cache_id, 'block');
		
		parent::__construct('', '', '', $delegate->get_index(), $delegate->get_position());
	}

	/**
	 * Restore from cache or write block into cache
	 * 
	 * @return void
	 */
	protected function read_cache() {
		if (!$this->cache_read) {
			$this->cache_read = true;
			$content = '';
			$data = array();
			DB::start_trans();
			$cache = Cache::read($this->cache_id);
			if ($cache) {
				$content = $cache->get_content_plain();
				$data = $cache->get_data();
			}
			else {
				// Put into cache
				$content = $this->delegate->get_content();
				$data = array(
					'title' => $this->delegate->get_title(),
					'name' => $this->delegate->get_name()
				);
				Cache::store($this->cache_id, $content, $this->life_time, $data, false);
			}
			DB::commit();
			$this->set_content($content);
			$this->set_title(Arr::get_item($data, 'title', ''));
			$this->set_name(Arr::get_item($data, 'name', ''));
		}	
	}
	
	/**
	 * Get title of block (heading)
	 *
	 * @return string
	 */
	public function get_title() {
		$this->read_cache();
		return parent::get_title();
	}
	
	/**
	 * Get content of block (HTML)
	 *
	 * @return string
	 */
	public function get_content() {
		$this->read_cache();
		return parent::get_content();		
	}
	
	/**
	 * Name (used as CSS class)
	 *
	 * @return string
	 */
	public function get_name() {
		$this->read_cache();
		return parent::get_name();		
	}	
}