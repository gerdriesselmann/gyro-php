<?php
/**
 * Allows to chaneg meta information for paged pages
 * 
 * gets $page_data and $pager_calculator sets
 * 
 * This view can not return anything!
 */

/* @var $pager_calculator WidgetPagerCalculator */
if ($pager_calculator->get_data_item('page', 1) > 1) {
	if ($pager_calculator->has_policy(WidgetPager::DONT_INDEX_PAGE_2PP)) {
		$page_data->head->robots_index = ROBOTS_NOINDEX_NOFOLLOW;
	}
	if (!$pager_calculator->has_policy(WidgetPager::DONT_CHANGE_TITLE)) {
		$page = $pager_calculator->get_data_item('page', 1);
		$t = tr(' - page %page', 'core', array('%page' => $page));
		$d = tr(' Shown is page %page.', 'core', array('%page' => $page));
		
		if (strpos($page_data->head->title, $t) === false) {
			$page_data->head->title .= $t;
		}
		if (strpos($page_data->head->description, $d) === false) {
			$page_data->head->description .= $d;
		}				
	}
}
