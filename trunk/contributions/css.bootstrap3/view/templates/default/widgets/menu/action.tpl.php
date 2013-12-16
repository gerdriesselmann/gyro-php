<?php
print html::a(
	str_replace(' ', '&nbsp;', $action->get_description()), 
	ActionMapper::get_url($action->get_name(), $action->get_instance()), 
	'',
	array('class' => 'btn btn-default action_' . String::plain_ascii($action->get_name(), '_'))
);
