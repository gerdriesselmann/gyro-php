<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hello,</b></p>

<p>welcome to <?=$appname; ?>. You registered with the following data:</p>

<p>Username: <b><?=$user->name ?></b><br/> 
E-Mail: <b><?=$user->email; ?></b></p>

<p>If you didn't register on <?=$appname; ?>, please ignore this mail 
or contact us to report the abusing of our services. Else please visit</p> 

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>to finish your registration.</p>

<p>Best regards,<br />
The team of <?=$appname?></p>

