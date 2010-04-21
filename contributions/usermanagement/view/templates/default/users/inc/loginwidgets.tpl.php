<?php print $form_validation; ?>

<?php print WidgetInput::output('name', tr('Username:', 'users'), $form_data); ?>
<?php print WidgetInput::output('password', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD); ?>

<?php if ($pwd_url = ActionMapper::get_path('lost_password')): ?>
<p><a href="<?=$pwd_url?>"><?=tr('Forgot password?', 'users')?></a></p>
<?php endif; ?>
<?php if ($resend_url = ActionMapper::get_path('resend_registration_mail')): ?>
<p><a href="<?=$resend_url?>"><?=tr('Registered, but got no confirmation mail?', 'users')?></a></p>
<?php endif; ?>
<br />		
<?php print WidgetInput::output('stayloggedin', tr('Stay logged in.', 'users'), $form_data, WidgetInput::CHECKBOX); ?> 
