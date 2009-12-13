Hello,

welcome to <?php print $appname; ?>. You registered with the following data:

Username: <?php print $user->name ?> 
E-Mail: <?php print $user->email; ?>

If you didn't register on <?php print $appname; ?>, please ignore this mail. Else 
please visit 

<?php print ActionMapper::get_url('confirm', $confirmation); ?>

to finish your registration.

Best regards,
Team <?php print $appname?>

