<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
<p><b>Hallo!</b></p>

<p>
Sie erhalten diese Benachrichtigung, weil sie tägliche E-Mail-Zusammenfassungen der 
Ereignisse auf <?=$appname?> angefordert haben.
</p>

<?php if (count($notifications)): ?>
	<p>
	Folgendes ist seit <?=GyroDate::local_date($settings->digest_last_sent)?> geschehen:
	</p>  
	
	<ul>
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
	</ul>
<?php else: ?>
	<p>
	Seit der letzen Benachrichtigung vom <?=GyroDate::local_date($settings->digest_last_sent)?> ist 
	jedoch nichts passiert.
	</p>
<?php endif?> 

<p>
Durch den folgenden Link können Sie Ihre Benachrichtigungseinstellungen ändern:
</p>

<p>
<a href="<?=$link_settings?>"><?=$link_settings?></a>
</p>

<p><br />Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>