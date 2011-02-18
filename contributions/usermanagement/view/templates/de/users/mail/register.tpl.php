<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hallo,</b></p>

<p>
willkommen bei <?=$appname; ?>. Sie haben sich mit folgenden Daten registriert:
</p>
<p>
Benutzername: <b><?=$user->name ?></b> <br />
E-Mail: <b><?=$user->email; ?></b>
</p>
<p>
Falls Sie sich nicht auf <?=$appname; ?> registriert haben, ignorieren Sie 
bitte diese Nachricht oder kontaktieren Sie uns, um den Missbrauch unserer Dienste zu melden. 
Ansonsten besuchen Sie 
</p>
<p>
<a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a>
</p>
<p>
um Ihre Registrierung abzuschließen.
</p>
<p>
Mit freundlichen Grüßen,<br />
Das Team von <?=$appname?>
</p>
