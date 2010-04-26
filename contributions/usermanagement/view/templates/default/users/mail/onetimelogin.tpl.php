Hello,

You requested an automatic one time login on <?php print $appname?>. Perhaps because 
you forgot your password. 

Please visit

<?php print ActionMapper::get_url('confirm', $confirmation); ?>

to automatically get logged in once. You then can - for example - change your password.

Please note that the web site above is only valid for 24 hours.

Best regards,
The team of <?php print $appname?>

