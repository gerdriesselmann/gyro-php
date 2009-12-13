<?php
$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
$page_data->head->title = tr('Become a member', 'users');
?>

<h1><?=tr('Become a member', 'users');?></h1>

<p><?php print tr('Fill in the fields and click <strong>Register</strong> to become a member. You will get an e-mail with a confirmation link afterwards.', 'users'); ?></p>

<form class="has_focus" id="frmregister" name="frmregister" action="<?=ActionMapper::get_path('register')?>" method="post">
 	<?php print $form_validation; ?>

	<fieldset>
	<legend><?=tr('Account data', 'users')?></legend>

	<?php print WidgetInput::output('name', tr('Username:', 'users'), $form_data); ?>

	<?php print WidgetInput::output('pwd1', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD); ?>
	<?php print WidgetInput::output('pwd2', tr('Retype password:', 'users'), $form_data, WidgetInput::PASSWORD); ?>
	</fieldset>
	<fieldset>
	<legend><?=tr('E-mail', 'users')?></legend>	

	<?php print WidgetInput::output('email', tr('E-mail:', 'users'), $form_data); ?>

	<p><?php print tr('Please provide a valid e-mail, so we can send an activation key to you.', 'users'); ?></p>
		
	<?php if ($feature_resend): ?>
	<p><?print WidgetActionLink::output(tr('Registered but got no e-mail?', 'users'), 'resend_registration_mail'); ?></p>
	<?php endif; ?>
	</fieldset>

	<br />
	
	<input class="button right" type="submit" name="submit" value="<?=tr('Register', 'users');?>" />&nbsp;
</form>
