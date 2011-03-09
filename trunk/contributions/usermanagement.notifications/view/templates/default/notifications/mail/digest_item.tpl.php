<li>
<?php
$l = ActionMapper::get_url('view', $notification) . '?src=' . Notifications::DELIVER_DIGEST;
print html::a(String::escape($notification->get_title()), $l, '');
?>
</li>
 
