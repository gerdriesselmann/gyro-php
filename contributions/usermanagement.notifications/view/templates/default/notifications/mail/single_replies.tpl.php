<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
Load::models('topics', 'posts');
$topic = Topics::get($notification->source_id);
$post = Posts::get(Arr::get_item($notification->source_data, 'id_post', false));
$link_post = ActionMapper::get_url('view', $post);
$link_settings = ActionMapper::get_url('notifications_settings');
$link_exclude = ActionMapper::get_url('notifications_exclude', $notification);
?>
<p><b>Hallo!</b></p>

<p>
Sie erhalten diese Benachrichtigung weil eine neue Antwort zum Thema 
»<?=$topic->get_title()?>« geschrieben wurde. Dies ist nach Ihrem 
letzten Besuch geschehen. Durch folgenden Link können Sie direkt 
zum neuen Beitrag gelangen:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_post)?>"><?=$link_post?></a>
</p>

<p>
Sie werden keine weiteren Benachrichtigungen zu diesem Thema erhalten, bis Sie das Thema besucht haben. Wenn Sie 
in Zukunft nicht mehr über Antworten zu diesem Thema benachrichtigt werden wollen, können Sie durch folgenden Link 
die Benachrichtigungen für dieses Thema abbestellen:
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