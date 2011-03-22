<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
<p><b>Hello!</b></p>

<p>
You requested to be informed about some events at <?=$appname?>.
</p>

<?php if (count($notifications)): ?>
	<p>
	This is what happened since <?=GyroDate::local_date($settings->digest_last_sent)?>:
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
	Unfortunately, nothing has happened since <?=GyroDate::local_date($settings->digest_last_sent)?>.
	</p>
<?php endif?> 

<p>
To change your notification settings, log in to <?=$appname?> and visit
</p>

<p>
<a href="<?=$link_settings?>"><?=$link_settings?></a>
</p>

<p><br />Best regards,</p>
<p><b>The team of <?=$appname?></b></p>
