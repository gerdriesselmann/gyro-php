Hello,

You requested to change your e-mail address for your account on <?php print $appname ?>.

Your new e-mail will be  

{$to}

Please visit

<?php print ActionMapper::get_url('confirm', $confirmation); ?>

to confirm the new address.

Please note that the web site above is valid only for 24 hours.

Best regards,
Team <?php print $appname?>
