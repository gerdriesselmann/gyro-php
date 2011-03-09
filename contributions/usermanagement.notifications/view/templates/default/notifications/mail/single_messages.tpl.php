<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
Load::models('messages');
$msg = Messages::get(Arr::get_item($notification->source_data, 'id_message', false));
$link_msg = ActionMapper::get_url('view', $msg);
$link_settings = ActionMapper::get_url('notifications_settings');
$link_exclude = ActionMapper::get_url('notifications_exclude', $notification);
?>
<p><b>Hallo!</b></p>

<p>
Sie erhalten diese Benachrichtigung weil Sie eine neue private Nachricht  
von <?=$msg->get_userprofile()->get_title()?>  
erhalten haben. Durch folgenden Link können Sie direkt zur Nachricht gelangen:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_msg)?>"><?=$link_msg?></a>
</p>

<p>
Sie werden keine weiteren Benachrichtigungen zu dieser Unterhaltung erhalten, bis Sie diese besucht haben. Wenn Sie 
in Zukunft nicht mehr über Nachrichten zu dieser Unterhaltung benachrichtigt werden wollen, können Sie durch folgenden 
Link die Benachrichtigungen für diese Unterhaltung abbestellen:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_exclude)?>"><?=$link_exclude?></a>
</p>

<p>
Durch den folgenden Link können Sie Ihre Benachrichtigungseinstellungen ändern:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_settings)?>"><?=$link_settings?></a>
</p>

<p><br />Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>