Hallo,

willkommen bei <?php print $appname; ?>. Sie haben sich mit folgenden Daten registriert:

Benutzername: <?php print $user->name ?> 
E-Mail: <?php print $user->email; ?>

Falls Sie sich nicht auf <?php print $appname; ?> registriert haben, ignorieren Sie 
bitte diese Nachricht oder kontaktieren Sie uns, um den Missbrauch unserer Dienste zu melden. 
Ansonsten besuchen Sie 

<?php print ActionMapper::get_url('confirm', $confirmation); ?>

um Ihre Registrierung abzuschließen.

Mit freundlichen Grüßen,
Das Team von <?php print $appname?>

