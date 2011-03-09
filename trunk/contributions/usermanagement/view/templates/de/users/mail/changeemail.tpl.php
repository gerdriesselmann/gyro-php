<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>

<p><b>Hallo!</b></p>

<p>Sie erhalten diese Mail weil Sie Ihre E-Mail-Adresse geändert haben. Durch den folgenden Link können Sie Ihre neue E-Mail-Adresse bestätigen und die Änderung abschließen:</p>

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>Bitte beachten Sie, dass der obige Link nach 24 Stunden ungültig wird.</p>

<p>Ihre neue E-Mail Adresse lautet</p>

<p><?=$confirmation->data; ?><p>

<p>Falls Sie diese Mail nicht angefordert haben, so ignorieren Sie bitte diese Nachricht oder kontaktieren Sie uns, um den Missbrauch unserer Dienste zu melden.</p>

<p>Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>