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
