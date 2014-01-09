<fieldset>
<legend><?=tr('User Data', 'users')?></legend>

<?php
print WidgetInput::output('name', tr('Username:', 'users'), $form_data);
print WidgetInput::output('email', tr('E-mail:', 'users'), $form_data);
if (Config::has_feature(ConfigUsermanagement::ENABLE_PWD_ON_EMAILCHANGE)) {
	print WidgetInput::output('pwd_mail',
		tr('Please confirm changes of the email address by entering the password:','users'), '',
		WidgetInput::PASSWORD, array('autocomplete' => 'off')
	);
}
?>

<p class="important">
<?php print tr('If the e-mail changes, you will get a mail send to the new address to confirm this address exists.', 'users')?></p>

<?php print WidgetInput::output('pwd1', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>
<?php print WidgetInput::output('pwd2', tr('Repeat Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>

<p><?php print tr('Leave these fields empty to not change the password.',  'users')?></p>
<?php if (Config::has_feature(ConfigUsermanagement::ENABLE_MAIL_ON_PWD_CHANGE)): ?>
	<p class="important">
		<?php print tr('If the password changes, you will get a mail send to you to confirm this change.', 'users')?>
	</p>
<?php else: ?>
	<?php print WidgetInput::output('pwd_pwd', tr('Please confirm password change by entering the old password:','users'), '', WidgetInput::PASSWORD, array('autocomplete' => 'off')); ?>
<?php endif; ?>
</fieldset>
