<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
<p><b>Hallo!</b></p>

<p>
bei <?=$appname?> ist etwas geschehen.
</p>

<p><b><?=$notification->get_title();?></b></p>

<?php print $notification->get_message(Notifications::DELIVER_MAIL); ?> 

<p>
Um Ihre Einstellungen für Benachrichtigungen zu ändern loggen Sie sich bei <?=$appname?> ein und besuchen Sie
</p>


<p>
<a href="<?=$link_settings?>"><?=$link_settings?></a>
</p>

<p><br />Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>