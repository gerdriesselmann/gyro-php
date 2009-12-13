<?php
?>
<h1><?=tr('Your notifications', 'notifications')?></h1>

<?php print WidgetFilter::output($filter_data)?>
<?php gyro_include_template('notifications/inc/list')?>
<?php print WidgetPager::output($pager_data)?>