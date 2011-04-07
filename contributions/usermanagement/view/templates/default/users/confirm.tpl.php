<?php
/* @var $page_data PageData */
$title = tr('Confirm your account settings', 'users');
$page_data->head->title = $title;
?>
<h1><?=$title?></h1>

<p><?php print tr('Fill out the fields below and click <strong>Save</strong> to save your data.', 'users');?></p>

<form class="has_focus" id="frmconfirmaccount" name="frmconfirmaccount" action="<?=ActionMapper::get_path('users_confirm')?>" method="post">
 	<?php print $form_validation; ?>

	<fieldset>
	<legend><?=tr('User Data', 'users')?></legend>

	<?php print WidgetInput::output('name', tr('Username:', 'users'), $form_data) ?>
	<?php print WidgetInput::output('email', tr('E-mail:', 'users'), $form_data) ?>

	<p class="important">
	<?php 
		switch($user->emailstatus) {
			case Users::EMAIL_STATUS_UNCONFIRMED:
				print tr('Your e-mail address is still unconfirmed. You will get a mail send to this address to confirm it exists.', 'users');
				break;
			case Users::EMAIL_STATUS_BOUNCED:
				print tr('There were problems sending mails to your e-mail address . You therefore will get a mail send to this address to confirm it exists.', 'users');
				break;
			case Users::EMAIL_STATUS_EXPIRED:
				print tr('It\'s been a while since your e-mail address got validated. You therefore will get a mail send to this address to confirm it still exists.', 'users');
				break;
			default:
				print tr('If the e-mail changes, you will get a mail send to the new address to confirm this address exists.', 'users');
				break;
	}
	?>
	</p>

	<?php print WidgetInput::output('pwd1', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>
	<?php print WidgetInput::output('pwd2', tr('Repeat Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>

	<p><?php print tr('Leave these fields empty to not change the password.',  'users')?></p>
	
	<?php if ($do_tos): 	?>
		<?php gyro_include_template('users/inc/toswidgets') ?>
	<?php endif; ?> 
	</fieldset>

	<br />
	
	<input class="button right" type="submit" name="submit" value="<?=tr('Save', 'users')?>" />
</form>
