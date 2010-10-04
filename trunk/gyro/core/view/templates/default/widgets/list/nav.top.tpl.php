<?php 
/* @var $parent_view IView */
print WidgetSorter::output($parent_view->retrieve('sorter_data'));
print WidgetFilter::output($parent_view->retrieve('filter_data'));
print WidgetFilterText::output($parent_view->retrieve('fitertext_data'));
print WidgetPager::output($parent_view->retrieve('pager_data'));
?>
