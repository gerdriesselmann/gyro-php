<?php
print $prefix;
if (!empty($menu_list)) {
	print html::li($menu_list);
}
print $postfix;