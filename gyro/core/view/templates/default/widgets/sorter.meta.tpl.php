<?php
/**
 * Allows to change meta information for paged pages
 * 
 * gets $page_data and $sorter_data sets
 * 
 * This view can not return anything!
 */

if (!$sorter_data['is_default']) {
	if (!Common::flag_is_set($policy, WidgetSorter::DONT_CHANGE_TITLE)) {
		$current_column = $sorter_data['current_column'];
		$column_title = GyroString::escape($current_column['title']);
		
		$t = tr(
			' - sorted by %col %dir', 
			'core', 
			array(
				'%col' => $column_title,
				'%dir' => GyroString::escape(tr($sorter_data['order'], 'core'))
			)
		);
		$d = tr(
			' Sorted by %col, %dir.', 
			'core', 
			array(
				'%col' => $column_title,
				'%dir' => GyroString::escape($current_column['sort_title'])
			)
		);
		
		if (strpos($page_data->head->title, $t) === false) {
			$page_data->head->title .= $t;
		}
		if (strpos($page_data->head->description, $d) === false) {
			$page_data->head->description .= $d;
		}						
	}
	if (Common::flag_is_set($policy, WidgetSorter::DONT_INDEX_SORTED)) {
		$page_data->head->robots_index = ROBOTS_NOINDEX_NOFOLLOW;
	}
}
