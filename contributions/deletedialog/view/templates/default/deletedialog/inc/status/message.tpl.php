<?php
$test = 'deletedialog/messages/' . $instance->get_table_name();
If (TemplatePathResolver::exists($test)) {
	include($this->resolve_path($test));
}
else {
	include($this->resolve_path('deletedialog/messages/default'));
}
