<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
Load::models('abuses');
$abuse = Abuses::get($notification->source_id);
$link_abuse = ActionMapper::get_url('edit', $abuse);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
<p><b>Hallo!</b></p>

<p>
Sie erhalten diese Benachrichtigung weil ein Missbrauch gemeldet worden ist.  
Durch folgenden Link können Sie direkt zur Meldung gelangen:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_abuse)?>"><?=$link_abuse?></a>
</p>

<p>
Durch den folgenden Link können Sie Ihre Benachrichtigungseinstellungen ändern:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_settings)?>"><?=$link_settings?></a>
</p>

<p><br />Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>
