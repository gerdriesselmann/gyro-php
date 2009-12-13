<?php 
$cls = array(
	'action_' . String::plain_ascii($action->get_name(), '_'),
	'action_' . $action->get_name_serialized(),
);
array_unique($cls);
print html::form(
	'',  
	ActionMapper::get_path('commands_post'), 
	html::submit($action->get_description(), $action->serialize(), $action->get_description(), array('id' => false, 'class' => implode(' ', $cls))),			
	'post', 
	array('id' => false, 'class' => 'commands_form')
);
