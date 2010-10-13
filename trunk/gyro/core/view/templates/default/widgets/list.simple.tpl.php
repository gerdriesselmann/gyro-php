<?php if (count($items)):?>
	<?php gyro_include_template('widgets/list/items'); ?>
<?php else: ?>
	<?php gyro_include_template('widgets/list/empty'); ?>
<?php endif; ?>
