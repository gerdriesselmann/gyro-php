<div class="notification-item notification-hide-menu <?=strtolower($notification->get_status())?>" id="notification-item-<?=$notification->id?>">
	<h3><?=$notification->get_title()?> - <?=GyroDate::local_date($notification->creationdate)?></h3>
	<div class="message">
		<?php print $notification->get_message(Notifications::READ_MARK_AUTO); ?>	
	</div>
	<?php print WidgetItemMenu::output($notification, 'list'); ?>
</div>

