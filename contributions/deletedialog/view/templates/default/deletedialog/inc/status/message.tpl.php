<?php
$safe_table_name = basename(str_replace(array('/', '\\', '..'), '', $instance->get_table_name()));
$test = 'deletedialog/messages/' . $safe_table_name;
If (TemplatePathResolver::exists($test)) {
	include($this->resolve_path($test));
}
else {
	include($this->resolve_path('deletedialog/messages/default'));
}
