<?php 
$mailcmd->set_is_html(true);
$mailcmd->set_alt_message($self);
?>
<p><b>Hallo!</b></p>

<p>Ihr Account bei <?=$appname; ?> wurde auf "<?=tr($new_status, 'users'); ?>" gesetzt.</p>

<?php if ($new_status == Users::STATUS_ACTIVE): ?><p>Sie können sich hier einloggen: <a href="<?=ActionMapper::get_url('login');?>"><?=ActionMapper::get_url('login');?></a></p><?php endif; ?>

<p>Mit freundlichen Grüßen,</p>

<p><b><?=$appname?></b></p>

