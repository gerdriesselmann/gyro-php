<?php
/**
 * @var bool $is_logged_in
 * @var DAOUsers $current_user
 * @var string $form_validation
 * @var array $form_data
 */
print $form_validation;
if ($is_logged_in) {
	print WidgetInput::output('name' ,'', $current_user->name, WidgetInput::HIDDEN);
	print WidgetInput::output('email' ,'', $current_user->email, WidgetInput::HIDDEN);
} else {
	print WidgetInput::output('name', tr('Your Name:', 'contact'), $form_data, WidgetInput::TEXT);
	print WidgetInput::output('email', tr('Your E-Mail Address:', 'contact'), $form_data, WidgetInput::TEXT);
}
print WidgetInput::output('subject', tr('Subject:', 'contact'), $form_data, WidgetInput::TEXT);
print WidgetInput::output('message', tr('Your Message:', 'contact'), $form_data, WidgetInput::TEXTAREA);
?>

