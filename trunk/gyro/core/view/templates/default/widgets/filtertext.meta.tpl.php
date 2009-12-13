<?php
/**
 * Allows to change meta information for text filterd pages
 * 
 * This view can not return anything!
 */
if ($value !== '') {
	if (!Common::flag_is_set($policy, WidgetFilter::DONT_CHANGE_TITLE)) {
		$t = tr(
			' - filtered by »%value«', 
			'core', 
			array(
				'%filter' => $title,
				'%value' => $value
			)
		);
		$d = tr(
			' Filtered by »%value«.', 
			'core', 
			array(
				'%filter' => $title,
				'%value' => $value
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