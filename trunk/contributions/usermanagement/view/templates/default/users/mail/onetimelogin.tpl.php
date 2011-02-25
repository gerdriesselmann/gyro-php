<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hello,</b></p>

<p>You requested an automatic one time login on <?=$appname?>. Perhaps because 
you forgot your password. </p>

<p>Please visit</p>

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>to automatically get logged in once. You then can - for example - change your password.</p>

<p>Please note that the web site above is only valid for 24 hours.</p>

<p>Best regards,<br />
The team of <?=$appname?></p>

