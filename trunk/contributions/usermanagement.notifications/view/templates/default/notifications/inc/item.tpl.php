<div class="notification-item <?=strtolower($notification->status)?>" id="notification-item-<?=$notification->id?>">
	<h3><?=$notification->get_title()?> - <?=GyroDate::local_date($notification->creationdate)?></h3>
	<div class="message">
		<?php print $notification->message; ?>	
	</div>
	<?php print WidgetItemMenu::output($notification, 'list'); ?>
</div>

