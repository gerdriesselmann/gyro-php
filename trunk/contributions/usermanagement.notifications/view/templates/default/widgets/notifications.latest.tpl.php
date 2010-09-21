<?php
Load::tools('filter'); 
$c = count($notifications);
$url = Url::create(ActionMapper::get_url('notifications', $current_user));
FilterDefaultAdapter::apply_to_url($url, 'all', 'status');
$url_title = tr('Show all', 'notifications');
?>

<div class="notifications-latest">
<p class="summary">
	<?php 
	if ($c) {
		print tr(
			'Last <strong>%num</strong> out of <strong>%total</strong> unread messages.', 
			'notifications', 
			array(
				'%num' => String::number($c, 0),
				'%total' => $total
			)
		);
		if ($c < $total) {
			$url_title = tr('Show more', 'notifications');
			FilterDefaultAdapter::apply_to_url($url, 'new', 'status');
		}
	}
	else {
		print tr('You have no unread messages.', 'notifications');			
	}
	?>
	<a href="<?=$url->build()?>"><?=$url_title?></a>	
</p>
<?php
print WidgetItemMenu::output(
	array(
		new ActionBase('', 'notifications_settings', tr('Notification Settings', 'notifications')),
		CommandsFactory::create_command('notifications', 'markallasread', false),
	)
);
?>
<?php if ($c): ?>
	<?php gyro_include_template('notifications/inc/list')?>
<?php endif; ?>
</div>
