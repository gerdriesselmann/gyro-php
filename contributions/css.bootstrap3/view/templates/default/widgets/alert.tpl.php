<?php
/**
 * Render a message, e.g as error, or warning
 */
$cls = '';
switch ($policy) {
	case WidgetAlert::ERROR:
		$cls = 'danger';
		break;
	case WidgetAlert::SUCCESS:
		$cls = 'success';
		break;
	case WidgetAlert::INFO:
		$cls = 'info';
		break;
	case WidgetAlert::NOTE:
		$cls = 'info';
		break;
	case WidgetAlert::WARNING:
		$cls = 'warning';
		break;
}
$attr = array('id' => $id);
if ($cls) {
	$attr['class'] = "alert alert-$cls";
}
print html::tag('div', $content, $attr);
