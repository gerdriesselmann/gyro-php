<?php 
$page_data->head->add_css_file('css/notifications.css');
if (Load::is_module_loaded('javascript.jquery')) {
	$page_data->head->add_js_file('js/notifications.js');
}
?>
<?php foreach($notifications as $notification): ?>
	<?php gyro_include_template('notifications/inc/item')?>
<?php endforeach; ?> 