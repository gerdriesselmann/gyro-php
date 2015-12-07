<?php
$title = tr('Notification Settings', 'notifications');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(array(
	WidgetActionLink::output(tr('Your Notifications', 'notifications'), 'users_notifications'),
	tr('Settings', 'notifications')
));
?>
<h1><?=$title?></h1>

<form name="frmnotificationssettings" class="has_focus" action="<?=ActionMapper::get_path('notifications_settings')?>" method="post">
	<?php print $form_validation; ?>
	
	<fieldset>
	<legend><?=tr('Immediate e-mail', 'notifications')?></legend>
	<?php 
	print WidgetInput::output('mail_enable', tr('Enable', 'notifications'), $form_data, WidgetInput::CHECKBOX);
	print WidgetInput::output('mail_settings', tr('Choose sources', 'notifications'), $form_data, WidgetInput::MULTISELECT, array('options' => $sources), WidgetInput::FORCE_CHECKBOXES);
	?>
	</fieldset>

	<fieldset>
	<legend><?=tr('E-mail digest', 'notifications')?></legend>
	<?php 
	print WidgetInput::output('digest_enable', tr('Enable', 'notifications'), $form_data, WidgetInput::CHECKBOX);
	print WidgetInput::output('digest_settings', tr('Choose sources', 'notifications'), $form_data, WidgetInput::MULTISELECT, array('options' => $sources), WidgetInput::FORCE_CHECKBOXES);
	?>
	</fieldset>

	<fieldset>
	<legend><?=tr('Feed', 'notifications')?></legend>
	<?php
	print WidgetInput::output('feed_enable', tr('Enable', 'notifications'), $form_data, WidgetInput::CHECKBOX);
	if ($settings && $settings->is_feed_enabled()) {
		print html::info(GyroString::escape(
			tr('Your feed url is %url', 'notifications', array('%url' => ActionMapper::get_url('notifications_feed', $settings)))
		));
	}	
	
	print WidgetInput::output('feed_settings', tr('Choose sources', 'notifications'), $form_data, WidgetInput::MULTISELECT, array('options' => $sources), WidgetInput::FORCE_CHECKBOXES);
	?>
	</fieldset>
	
	<?php print WidgetInput::output('submit', '', tr('Save', 'notifications'), WidgetInput::SUBMIT)?>
</form>
