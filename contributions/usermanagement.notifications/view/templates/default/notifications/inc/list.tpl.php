<?php 
$page_data->head->add_css_file('css/notifications.css', true);
if (Load::is_module_loaded('javascript.jquery')) {
	$page_data->head->add_js_file('js/notifications.js', true);
}
?>
<?php 
foreach($notifications as $notification) {
	$templates = array(
		'notifications/inc/item_' . strtolower($notification->source),
		'notifications/inc/item'
	);
	$v = ViewFactory::create_view(IViewFactory::MESSAGE, $templates, false);
	$v->assign('notification', $notification);
	print $v->render();
}
