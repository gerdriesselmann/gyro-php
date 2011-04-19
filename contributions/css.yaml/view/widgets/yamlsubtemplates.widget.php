<?php
/**
 * Prints YAML subtemplates
 */
class WidgetYAMLSubtemplates implements IWidget {
	/**
	 * Two cols à 50%
	 */
	const SLOTS_2_50 = '2';
	/**
	 * Two cols à 25% and 75%
	 */
	const SLOTS_2_25_75 = '25_75';
	/**
	 * Two cols à 75% and 25%
	 */
	const SLOTS_2_75_25 = '75_25';
	/**
	 * Two cols à 33% and 66%
	 */
	const SLOTS_2_33_66 = '33_66';
	/**
	 * Two cols à 33% and 66%
	 */
	const SLOTS_2_66_33 = '66_33';
	/**
	 * Two cols with golden ratio, smaler col on the left
	 */
	const SLOTS_2_GOLDEN_RATIO_LEFT = 'golden_ratio_left';
	/**
	 * Two cols with golden ratio, smaler col on the right
	 */
	const SLOTS_2_GOLDEN_RATIO_RIGHT = 'golden_ratio_right';
	/**
	 * Three cols à 33%
	 */
	const SLOTS_3_33 = '3';
	/**
	 * Four cols à 25%
	 */
	const SLOTS_4_25 = '4';
	/**
	 * Five cols à 20%
	 */
	const SLOTS_5_20 = '5';
	
	
	protected $slotwidths;
	protected $data;
	protected $cls;
	
	/**
	 * Output a subtemplate
	 * 
	 * @param $arr_data Array of HTML elemtns for subtemplates
	 * @param $arr_slotwidths Either array of slot widths, or One of the SLOT_* constants
	 */
	public static function output($arr_data, $arr_slotswidths, $cls = '') {
		$w = new WidgetYAMLSubtemplates($arr_data, $arr_slotswidths, $cls);
		return $w->render();
	}
	
	public function __construct($arr_data, $arr_slotswidths, $cls) {
		$this->data = $arr_data;
		if (is_array($arr_slotswidths)) {
			$this->slotwidths = $arr_slotswidths;
		}
		else {
			$this->slotwidths = $this->translate_slot($arr_slotswidths);
		}
		$this->cls = $cls;
	}
	
	protected function translate_slot($slot) {
		switch ($slot) {
			case self::SLOTS_2_33_66:
				return array(33, 66);
			case self::SLOTS_2_66_33:
				return array(66, 33);
			case self::SLOTS_2_25_75:
				return array(25, 75);
			case self::SLOTS_2_75_25:
				return array(75, 25);
			case self::SLOTS_2_GOLDEN_RATIO_LEFT:
				return array(38, 62);						
			case self::SLOTS_2_GOLDEN_RATIO_RIGHT:
				return array(62, 38);						
			case self::SLOTS_3_33:
				return array(33, 33, 33);
			case self::SLOTS_4_25:
				return array(25, 25, 25, 25);
			case self::SLOTS_5_20:
				return array(20, 20, 20, 20, 20);		
			case self::SLOTS_2_50:
			default:
				return array(50, 50);		
		}
	}
	
	public function render($policy = self::NONE) {
		$slots = $this->create_slots($this->slotwidths);
		$ret = '';
		while(count($this->data)) {
			$row = '';
			foreach($slots as $slot) {
				$row .= html::div(html::div(array_shift($this->data), $slot['subc']), $slot['cls']);
				$row .= "\n";
			}
			$ret .= html::div($row, 'subcolumns ' . $this->cls) . "\n";
		}
		return $ret;
	}

	protected function create_slots($slotwidths) {
		$ret = array();
		$c = count($slotwidths);
		for($i = 0; $i < $c; $i++) {
			$appendix = '';
			if ($i == ($c - 1)) {
				$appendix = 'r';
			} 
			else {
				$appendix = 'l';
			}
			$ret[] = array('cls' => 'c' . $slotwidths[$i] . $appendix, 'subc' => 'subc'. $appendix); 
		}
		return $ret;
	} 
}
