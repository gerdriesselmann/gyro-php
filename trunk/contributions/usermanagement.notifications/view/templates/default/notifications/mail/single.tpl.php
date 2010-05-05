Hello,

Something has happened at <?php print $appname ?>.

-- <?php print $notification->get_title();?> --
<?php print wordwrap(ConverterFactory::decode($notification->message, ConverterFactory::HTML), 65); ?> 

To change your notification settings, log in to <?php print $appname ?> and visit
<?php print ActionMapper::get_url('notifications_settings')?>.  

Best regards,
The team of <?php print $appname?>

