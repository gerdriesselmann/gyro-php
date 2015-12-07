<?php
/**
 * Allows to change meta information for filterd pages
 * 
 * gets $page_data and $filter_group set
 * 
 * This view can not return anything!
 */

/* @var $filter_group DBFilterGroup */
$filter_current = $filter_group->get_current_filter();
if ($filter_current && $filter_current->get_key() != $filter_group->get_default_key()) {
	if (!Common::flag_is_set($policy, WidgetFilter::DONT_CHANGE_TITLE)) {
		$filter_value = GyroString::escape($filter_current->get_title());
		$filter_name = GyroString::escape($filter_group->get_name());
		
		$t = tr(
			' - filtered by %filter: %value', 
			'core', 
			array(
				'%filter' => $filter_name,
				'%value' => $filter_value
			)
		);
		$d = tr(
			' Filtered by %filter: %value', 
			'core', 
			array(
				'%filter' => $filter_name,
				'%value' => $filter_value
			)
		);

		if (strpos($page_data->head->title, $t) === false) {
			$page_data->head->title .= $t;
		}
		if (strpos($page_data->head->description, $d) === false) {
			$page_data->head->description .= $d;
		}						
	}
	if (Common::flag_is_set($policy, WidgetFilter::DONT_INDEX_FILTERED)) {
		$page_data->head->robots_index = ROBOTS_NOINDEX_NOFOLLOW;
	}
}
