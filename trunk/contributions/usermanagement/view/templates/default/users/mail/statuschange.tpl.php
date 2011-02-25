<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hello,</b></p>

<p>Your account on <?=$appname; ?> was set to "<?=tr($new_status, 'users'); ?>".</p>

<?php if ($new_status == Users::STATUS_ACTIVE): ?><p>You can log in here: <a href="<?=ActionMapper::get_url('login'); ?>"><?=ActionMapper::get_url('login'); ?></a></p><?php endif; ?>

<p>Best regards,<br />
The team of <?=$appname?></p>

