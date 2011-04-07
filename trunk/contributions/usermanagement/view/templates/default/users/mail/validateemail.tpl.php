<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hello,</b></p>

<p>We need to validate your e-mail address for your account on <?=$appname ?>.</p>

<p>Your current e-mail is</p>
  
<p><?=$confirmation->data; ?></p>

<p>Please visit</p>

<p><a href="<?=ActionMapper::get_url('confirm', $confirmation); ?>"><?=ActionMapper::get_url('confirm', $confirmation); ?></a></p>

<p>to confirm the address.</p>

<p>Please note that the url above is only valid for 24 hours.</p>

<p>Best regards,<br />
The team of <?=$appname?></p>

