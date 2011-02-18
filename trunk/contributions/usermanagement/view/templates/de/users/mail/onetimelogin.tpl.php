<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hallo,</b></p>
<p>
Sie haben einen automatischen Einmallogin für <?=$appname?> beantragt. 
Vielleicht weil Sie Ihr Passwort vergessen haben. 
</p>
<p>
Bitte besuchen Sie
</p>
<p>
<a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a>
</p>
<p>
um einmalig automatisch eingeloggt zu werden. Sie können dann - zum Beispiel - Ihr Passwort ändern.
</p>
<p>
Bitte beachten Sie das obige Seite nur 24 Stunden gültig ist.
</p>
<p>
Mit freundlichen Grüßen,<br />
Das Team von <?=$appname?>
</p>
