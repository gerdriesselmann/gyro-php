<?php 
$cls = array(
	'action_' . GyroString::plain_ascii($action->get_name(), '_'),
	'action_' . $action->get_name_serialized(),
);
array_unique($cls);

$btn = '';
$btn .= html::submit($action->get_description(), $action->serialize(), $action->get_description(), array('id' => false, 'class' => implode(' ', $cls)));
$btn .= $form_validation;  

print html::form(
	'',  
	ActionMapper::get_path('commands_post'), 
	$btn,			
	'post', 
	array('id' => false, 'class' => 'commands_form')
);
