<?php
// Wrapper around deleteion approval dialog
$inst_title = $instance->to_string();
if ($instance instanceof ISelfDescribing) {
	$inst_title = $instance->get_title();
}
$title = tr('Delete instance »%inst«', 'deletedialog', array('%inst' => $inst_title));
$page_data->head->title = $title;

$page_data->breadcrumb = WidgetBreadcrumb::output(
	array(
		$instance,
		GyroString::escape(tr('Delete', 'deletedialog'))
	)
);
?>
<h1><?=$title ?></h1>

<?php gyro_include_template('deletedialog/inc/message') ?>
<?php gyro_include_template('deletedialog/inc/form') ?>
