<?php
print html::p(WidgetActionLink::output($item, 'view', $item), 'title');
$desc = $item->get_description();
if ($desc) {
	print html::div($desc, 'description');
} 
print WidgetItemMenu::output($item, 'list');
?>
