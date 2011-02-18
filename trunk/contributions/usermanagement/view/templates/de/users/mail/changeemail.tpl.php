<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>

<p><b>Hallo,</b></p>

<p>Sie haben eine Änderung Ihrer E-Mail Adresse für Ihr Benutzerkonto bei <?=$appname ?> beantragt.</p>

<p>Ihre neue E-Mail Adresse lautet</p>

<p><b><?=$confirmation->data; ?></b><p>

<p>Bitte besuchen Sie</p>

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>um Ihre neue Adresse zu bestätigen.</p>

<p>Bitte beachten Sie das obige Seite nur 24 Stunden gültig ist.</p>

<p>
Mit freundlichen Grüßen,<br />
Das Team von <?=$appname?>
</p>
