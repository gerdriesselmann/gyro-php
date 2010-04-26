Hallo,

Ihr Account bei <?php print $appname; ?> wurde auf "<?php print tr($new_status, 'users'); ?>" gesetzt.

<?php if ($new_status == Users::STATUS_ACTIVE): ?>Sie können sich hier einloggen: <?php print ActionMapper::get_url('login'); ?><?php endif; ?>

Mit freundlichen Grüßen,
Das Team von <?php print $appname?>

