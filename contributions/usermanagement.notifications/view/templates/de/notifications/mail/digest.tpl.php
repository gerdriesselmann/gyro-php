Hallo,

Sie haben regelmäßige Benachrichtigungen für Ereignisse auf <?php print $appname ?> angefordert.

<?php if (count($notifications)): ?>
Folgendes ist seit <?php print GyroDate::local_date($settings->digest_last_sent)?> geschehen:  

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
Leider ist seit <?php print GyroDate::local_date($settings->digest_last_sent)?> nichts geschehen.
<?php endif?> 

Um Ihre Einstellungen für Benachrichtigungen zu ändern loggen Sie sich bei <?php print $appname ?> ein und besuchen Sie
<?php print ActionMapper::get_url('notifications_settings')?>.

Mit freundlichen Grüßen,
Das Team von <?php print $appname?>

