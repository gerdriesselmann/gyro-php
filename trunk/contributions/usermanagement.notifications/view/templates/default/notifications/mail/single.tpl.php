<?php
//$mailcmd->set_is_html(true);
//$mailcmd->set_alt_message($self);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
<p><b>Hello!</b></p>

<p>
Something has happened at <?=$appname?>.
</p>

<p><b><?=$notification->get_title()?></b></p>
<?php print $notification->get_message(Notifications::DELIVER_MAIL)?> 

<p>
To change your notification settings, log in to <?=$appname?> and visit
</p>

<p>
<a href="<?=$link_settings?>"><?=$link_settings?></a>
</p>

<p><br />Best regards,</p>
<p><b>The team of <?=$appname?></b></p>


