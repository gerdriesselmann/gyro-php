<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hallo,</b></p>

<p>Sie erhalten diese Mail weil Sie sich gerade registriert haben. Durch den folgenden Link können Sie Ihre Registrierung bestätigen und die 
Registrierung abschließen:</p>

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>Die Registrierung erfolgte mit folgenden Daten:</p>

<p>Benutzername: <b><?=$user->name ?></b> <br />
E-Mail: <b><?=$user->email; ?></b></p>

<p>Falls Sie sich nicht registriert haben, so ignorieren Sie bitte diese Nachricht oder kontaktieren Sie uns, um den Missbrauch unserer Dienste zu melden.</p>

<p>Mit freundlichen Grüßen,</p>

<p><?=$appname?></p>