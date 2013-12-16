<?php
/**
 * Render an array of menu items
 *
 * @var $items array Array of items as map of name => html
 * @var $class string additional CSS class name
 */
foreach($items as $arr_item) {
	foreach($arr_item as $name => $item) {
		print $item;
	}
}

