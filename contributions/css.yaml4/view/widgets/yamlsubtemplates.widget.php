<?php
/**
 * Prints YAML subtemplates
 */
class WidgetYAMLSubtemplates implements IWidget {
	const EQUALIZE = 1024;
	const NO_EXPLICIT_LEFT_RIGHT = 2048;


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
	/**
	 * Automatically detect (= 100/count(data))
	 */
	const SLOTS_AUTO = 'AUTO';
	
	protected $slotwidths;
	protected $data;
	protected $cls;
	
	/**
	 * Output a subtemplate
	 * 
	 * @param $arr_data Array of HTML elemtns for subtemplates
	 * @param $arr_slotwidths Either array of slot widths, or One of the SLOT_* constants
	 */
	public static function output($arr_data, $arr_slotswidths, $cls = '', $policy = self::NONE) {
		$w = new WidgetYAMLSubtemplates($arr_data, $arr_slotswidths, $cls);
		return $w->render($policy);
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
			case self::SLOTS_AUTO:
				$c = count($this->data);
				if ($c > 0) {
					return array_fill(0, $c, floor(100/$c));
				} else {
					return array(50, 50);
				}
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
		/*
		 * Example Markup:
		 *
		 * <div class="ym-grid">
		 *    <div class="ym-g33 ym-gl"><div class="ym-gbox">33.333 %</div></div>
		 *    <div class="ym-g33 ym-gl"><div class="ym-gbox">33.333 %</div></div>
		 *    <div class="ym-g33 ym-gr"><div class="ym-gbox"> 33.333 %</div></div>
		 * </div>
		 */
		$container_cls = Arr::force($this->cls, false);
		$container_cls[] = 'ym-grid';
		if (Common::flag_is_set($policy, self::EQUALIZE)) { $container_cls[] = '.ym-equalize'; }

		$container_cls = implode(' ', $container_cls);

		$slots = $this->create_slots($this->slotwidths, $policy);
		$ret = '';
		while(count($this->data)) {
			$row = '';
			foreach($slots as $slot) {
				$row .=
					html::div(
						html::div(
							array_shift($this->data),
							$slot['inner']
						),
						$slot['outer']
					);
				$row .= "\n";
			}
			$ret .= html::div($row, $container_cls) . "\n";
		}
		return $ret;
	}

	/**
	 * Returns array with three members:
	 *
	 * - outer: Holds class of outer div
	 * - inner: Holds class of inner div
	 *
	 *
	 * @param $slot_widths
	 * @return array
	 */
	protected function create_slots($slot_widths, $policy) {
		$explicit_left_right = !Common::flag_is_set($policy, self::NO_EXPLICIT_LEFT_RIGHT);
		$ret = array();
		$c = count($slot_widths);
		$idx_last = $c - 1;
		for($i = 0; $i < $c; $i++) {
			$outer = 'ym-g' . $slot_widths[$i];
			$inner = 'ym-gbox';

			$outer .= ($i == $idx_last) ? ' ym-gr' : ' ym-gl';
			if ($explicit_left_right) {
				switch ($i) {
					case 0:
						$inner = 'ym-gbox-left';
						break;
					case $idx_last:
						$inner = 'ym-gbox-right';
						break;
					default:
						break;
				}
			}
			$ret[] = array('outer' => $outer, 'inner' => $inner);
		}
		return $ret;
	} 
}
