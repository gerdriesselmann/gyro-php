<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
Hello,

You requested to be informed about some events at <?php print $appname ?>.

<?php if (count($notifications)): ?>
This is what happened since <?php print GyroDate::local_date($settings->digest_last_sent)?>:  

<?php 
	foreach($notifications as $n) {
		$templates = array(
			'notifications/mail/digest_item_' . strtolower($n->source),
			'notifications/mail/digest_item'
		);
		$v = ViewFactory::create_view(IViewFactory::MESSAGE, $templates, false);
		$v->assign('notification', $n);
		print $v->render();
	}
?>  
<?php else: ?>
Unfortunately, nothing has happened since <?php print GyroDate::local_date($settings->digest_last_sent)?>.
<?php endif?> 

To change your notification settings, log in to <?php print $appname ?> and visit
<?php print ActionMapper::get_url('notifications_settings')?>.  

Best regards,
The team of <?php print $appname?>

