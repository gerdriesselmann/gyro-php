<?php
/**
 * Allows to chaneg breadcrumb for paged pages
 * 
 * gets $page_data and $pager_calculator sets
 * 
 * This view can not return anything!
 */

/** @var $pager_calculator WidgetPagerCalculator */
/** @var $page_data PageData */
$page = $pager_calculator->get_data_item('page', 1);
if ($page > 1) {
	if (!$pager_calculator->has_policy(WidgetPager::DONT_ADD_BREADCRUMB)) {
		if (!is_string($page_data->breadcrumb)) {
			$t = tr('Page %page', 'core', array('%page' => $page));
			$breadcrumb = Arr::force($page_data->breadcrumb, false);
			$last = array_pop($breadcrumb);
			if ($last !== $t) {
				// First call!
				if (is_string($last) && strpos($last, '<a ', 0) === false) {
					// last item is not a link => Link it to current
					$last = html::a($last, $pager_calculator->get_page_url(1), '');
				}
				$breadcrumb[] = $last;
				$breadcrumb[] = $t;
				
				$page_data->breadcrumb = $breadcrumb;
			}
		}
	}
}
