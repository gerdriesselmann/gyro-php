<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hallo!</b></p>

<p>Sie erhalten diese Mail damit Sie ein neues Passwort für Ihr Benutzerkonto vergeben können. 
Durch den folgenden Link werden Sie einmalig angemeldet und können dadurch ein neues Passwort setzen:</p>

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>Falls Sie diese Mail nicht angefordert haben, so ignorieren Sie bitte diese Nachricht oder kontaktieren Sie uns, um den Missbrauch unserer Dienste zu melden.</p>

<p>Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>