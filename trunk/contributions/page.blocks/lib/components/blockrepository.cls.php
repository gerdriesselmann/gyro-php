<?php
/**
 * The block repository allows naming of blocks and easy enabling/disableing them
 * 
 * @ingroup Blocks
 * @author Gerd Riesselmann
 */
class BlockRepository {
	private static $blocks = array();
	
	/**
	 * Add a block 
	 * 
	 * @param string $key Name of block, used as key
	 * @param IBlock $block
	 */
	public static function add($key, IBlock $block) {
		self::$blocks[$key] = $block;
	}
	
	/**
	 * Retrieve block for key
	 * 
	 * @param string $key
	 * @return IBlock
	 */
	public static function get($key) {
		return Arr::get_item(self::$blocks, $key, false);
	}
	
	/**
	 * Enable given blocks for given position
	 * 
	 * @param PageData $page_data
	 * @param string $position LEFT, RIGHT etc
	 * @param array $keys Keys of blocks to enable OR array of IBlock or mixed
	 * @param int $start_index Index of first block
	 * @param int $increment Index of blocks get incremented by this
	 */
	public static function enable(PageData $page_data, $position, $blocks, $start_index = 1000, $increment = 10) {
		$enable_blocks = array();
		foreach(Arr::force($blocks) as $key) {
			if ($key instanceof IBlock) {
				$enable_blocks[] = $key;
			}
			else {
				$block = self::get($key);
				if ($block) {
					$enable_blocks[] = $block;
				}
			}
		}
		// Now enable them
		foreach($enable_blocks as $block) {
			$page_data->add_block($block, $position, $start_index);
			$start_index += $increment;	
		}
	}
}