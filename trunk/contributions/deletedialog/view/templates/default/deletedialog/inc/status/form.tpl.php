<form action="<?=ActionMapper::get_path('deletedialog_approve_status', $instance)?>" method="post">
	<?php print $form_validation ?>
	<?php print WidgetInput::output('approve', '', tr('Yes, Delete', 'deletedialog'), WidgetInput::SUBMIT, array(), WidgetInput::NO_BREAK) ?>
	<?php print WidgetInput::output('cancel', '', tr('No, Cancel Deletion', 'deletedialog'), WidgetInput::SUBMIT) ?>
</form>

