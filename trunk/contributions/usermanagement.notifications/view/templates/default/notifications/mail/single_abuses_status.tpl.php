<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
Load::models('abuses');
$abuse = Abuses::get($notification->source_id);
$link_settings = ActionMapper::get_url('notifications_settings');
$link_exclude = ActionMapper::get_url('notifications_exclude', $notification);
$link_content = ActionMapper::get_url('view', $abuse->instance);
?>
<p><b>Hallo!</b></p>

<p>
Vielen Dank für Ihre aktive Mitarbeit. Die von Ihnen eingesendete Missbrauchsmeldung wurde von uns bearbeitet.
</p>

<p>
Bitte haben Sie Verständnis dafür, dass wir keine genauen Angaben über unsere Maßnahmen machen können. 
Folgende Informationen hatten Sie uns zukommen lassen:
</p>

<blockquote>
<p><?=tr($abuse->instance->get_table_name())?> &quot;<?=$abuse->instance->get_title()?>&quot;</p>
<p><?=$abuse->instance->get_description()?></p>
<p>Begründung: <?=tr($abuse->reason, 'supportforum')?></p>
</blockquote>

<p>
Hier können Sie sich den gemeldeten Inhalt ansehen:
</p>

<p>
<a href="<?=$notification->create_click_track_link(Notifications::DELIVER_MAIL, $link_content)?>"><?=$link_content?></a>
</p>

<p>
Wenn Sie in Zukunft nicht mehr über den Verlauf dieser Missbrauchsmeldung benachrichtigt werden wollen, können Sie durch folgenden 
Link die Benachrichtigungen für diese Meldung abbestellen:
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
