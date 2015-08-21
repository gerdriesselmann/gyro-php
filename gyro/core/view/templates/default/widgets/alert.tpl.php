<?php
/**
 * Render a message, e.g as error, or warning
 */
$cls = '';
switch ($policy) {
	case WidgetAlert::ERROR:
		$cls = 'error';
		break;
	case WidgetAlert::SUCCESS:
		$cls = 'success';
		break;
	case WidgetAlert::INFO:
		$cls = 'info';
		break;
	case WidgetAlert::NOTE:
		$cls = 'note';
		break;
	case WidgetAlert::WARNING:
		$cls = 'warning';
		break;
}
print html::tag('p', $content, array(
	'class' => $cls,
	'id' => $id
));