<?php
$title = tr('Your Notifications', 'notifications');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(GyroString::escape($title));
?>
<h1><?=$title?></h1>

<?php
print WidgetItemMenu::output(
	array(
		new ActionBase('', 'notifications_settings', tr('Notification Settings', 'notifications')),
		CommandsFactory::create_command('notifications', 'markallasread', false),
	)
);
?>

<?php print WidgetFilter::output($filter_data)?>
<?php gyro_include_template('notifications/inc/list')?>
<?php print WidgetPager::output($pager_data)?>
