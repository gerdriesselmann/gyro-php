<?php 
$cls = array(
	'btn btn-default',
	'action_' . String::plain_ascii($action->get_name(), '_'),
	'action_' . $action->get_name_serialized(),
);
array_unique($cls);

$btn = '';
$btn .= html::tag(
	'button',
	$action->get_description(),
	array(
		'name' => $action->serialize(),
		'type' => 'submit',
		'id' => false,
		'class' => implode(' ', $cls)
	)
);
$btn .= $form_validation;

print html::form(
	'',  
	ActionMapper::get_path('commands_post'), 
	$btn,			
	'post', 
	array('id' => false, 'class' => 'commands_form')
);
