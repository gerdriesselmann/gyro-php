<?php
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
$link_settings = ActionMapper::get_url('notifications_settings')
?>
Hello,

Something has happened at <?php print $appname ?>.

-- <?php print $notification->get_title();?> --
<?php print wordwrap(ConverterFactory::decode($notification->get_message(Notifications::DELIVER_MAIL), ConverterFactory::HTML_EX), 65); ?> 

To change your notification settings, log in to <?php print $appname ?> and visit
<?php print ActionMapper::get_url('notifications_settings')?>.  

Best regards,
The team of <?php print $appname?>

