<?php
/**
 * Clear the Cache for an item or all
 *  
 * @author Gerd Riesselmann
 * @ingroup Behaviour
 */
class ClearCacheCommand extends CommandBase {
	protected $item;
	
	public function __construct($item) {
		$this->item = $item;
	}
	
	public function execute() {
		Cache::clear($this->item);
		return new Status();
	}
}
?>