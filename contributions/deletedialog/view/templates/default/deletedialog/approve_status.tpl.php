<?php
// Wrapper around deletetion approval dialog

$inst_title = $instance->to_string();
if ($instance instanceof ISelfDescribing) {
	$inst_title = $instance->get_title();
}
$instance_string = 'instance';
$table_name = $instance->get_table_name();
$instance_string = substr($table_name, 0, strlen($table_name)-1);
$title = tr('Delete '.$instance_string.' »%inst«', 'deletedialog', array('%inst' => $inst_title));
$page_data->head->title = $title;

$page_data->breadcrumb = WidgetBreadcrumb::output(
	array(
		$instance,
		GyroString::escape(tr('Delete', 'deletedialog'))
	)
);
?>
<h1><?php print tr('Approve status', 'deletedialog'); ?>: <?=$title ?></h1>

<?php
$tpl = 'deletedialog/infos/'.$table_name;
if (TemplatePathResolver::exists($tpl)) {
	include($this->resolve_path($tpl));
} else {
	include($this->resolve_path('deletedialog/infos/default'));
}
?>

<?php gyro_include_template('deletedialog/inc/status/message') ?>
<?php gyro_include_template('deletedialog/inc/status/form') ?>

