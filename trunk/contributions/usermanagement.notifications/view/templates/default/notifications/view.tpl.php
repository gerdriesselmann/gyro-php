<?php
$title = $notification->get_title();
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(array(
	WidgetActionLink::output(tr('Your Notifications', 'notifications'), 'users_notifications'),
	$title
));

$page_data->head->add_css_file('css/notifications.css');
if (Load::is_module_loaded('javascript.jquery')) {
	$page_data->head->add_js_file('js/notifications.js');
}
?>
<h1><?=$title?></h1>

<?php gyro_include_template('notifications/inc/item')?>
