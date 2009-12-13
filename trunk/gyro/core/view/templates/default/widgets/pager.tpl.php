<?php
/* @var $pager_calculator WidgetPagerCalculator */
$out = $pager_calculator->get_page_x_of_n();
if ($out !== '') {
	$out .= ' | ';
}
$out .= $pager_calculator->get_previous_link();
$out .= ' ';
$out .= implode(' ', $pager_calculator->get_page_links_array(Config::get_value(Config::PAGER_NUM_LINKS, false, 11)));
$out .= ' ';
$out .= $pager_calculator->get_next_link();

print html::p($out, 'pager_list');
