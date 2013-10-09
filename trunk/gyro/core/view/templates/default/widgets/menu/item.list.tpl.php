<?php
/**
 * Render an array of menu items
 *
 * @var $items array Array of items as map of name => html
 * @var $class string additional CSS class name
 */
$c = count($items) - 1;
$i = 0;
foreach($items as $arr_item) {
	foreach($arr_item as $name => $item) {
		$name = String::plain_ascii($name);
		$cls = array($class);
		if ($i === 0) {
			$cls[] = "{$class}_first";
		}
		if ($i === $c) {
			$cls[] = "{$class}_last";
		}
		$cls[] = "{$class}_{$name}";
		print html::tag('li', $item, array('class' => implode(' ', $cls)));
		$i++;
	}
}

