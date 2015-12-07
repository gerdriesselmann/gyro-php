<?php
print html::a(
	str_replace(' ', '&nbsp;', $action->get_description()), 
	ActionMapper::get_url($action->get_name(), $action->get_instance()), 
	'',
	array('class' => 'action_' . GyroString::plain_ascii($action->get_name(), '_'))			
);
