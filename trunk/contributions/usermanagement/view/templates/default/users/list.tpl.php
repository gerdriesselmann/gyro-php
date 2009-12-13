<?php
$page_data->head->title = tr('All users', 'users');
$page_data->head->description = tr('List of all users known to the system.', 'users');

$title = tr('Users List', 'users');
$page_data->breadcrumb = WidgetBreadcrumb::output(
	$title
);
?>
<h1><?=$title?></h1>

<?php gyro_include_template('users/inc/list')?>

<?php 
print WidgetActionLink::output(tr('Create new user', 'users'), 'users_create');
?>