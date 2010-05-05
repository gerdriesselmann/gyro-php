<?php
$title = tr('Your notifications', 'notifications');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output($title);
?>
<h1><?=$title?></h1>

<?php print WidgetFilter::output($filter_data)?>
<?php gyro_include_template('notifications/inc/list')?>
<?php print WidgetPager::output($pager_data)?>