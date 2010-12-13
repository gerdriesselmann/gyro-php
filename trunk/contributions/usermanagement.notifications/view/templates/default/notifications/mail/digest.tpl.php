Hello,

You requested to be informed about some events at <?php print $appname ?>.

<?php if (count($notifications)): ?>
This is what happened since <?php print GyroDate::local_date($settings->digest_last_sent)?>:  

<?php foreach($notifications as $n):?>
-- <?php print $n->get_title();?> --
<?php print wordwrap(ConverterFactory::decode($n->get_message(Notifications::DELIVER_DIGEST), ConverterFactory::HTML_EX), 65); ?>
  
<?php endforeach;?>
<?php else: ?>
Unfortunately, nothing has happened since <?php print GyroDate::local_date($settings->digest_last_sent)?>.
<?php endif?> 

To change your notification settings, log in to <?php print $appname ?> and visit
<?php print ActionMapper::get_url('notifications_settings')?>.  

Best regards,
The team of <?php print $appname?>

