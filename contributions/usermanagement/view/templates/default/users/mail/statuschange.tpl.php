Hello,

Your account on <?php print $appname; ?> was set to "<?php print tr($new_status, 'users'); ?>".

<?php if ($new_status == Users::STATUS_ACTIVE): ?>You can log in here: <?php print ActionMapper::get_url('login'); ?><?php endif; ?>

Best regards,
Team <?php print $appname?>