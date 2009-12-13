<?php  		
$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
$title = tr('Login', 'users');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(
	$title
);
?>
<h1><?=$title?></h1>
<form class="has_focus" id="frmLogin" name="frmLogin" action="<?=ActionMapper::get_url('login'); ?>" method="post">
 	<?php print $form_validation; ?>
 	<p><?php print tr('Please enter your username and password and click <strong>Login</strong>.', 'users'); ?></p>

	<?php print WidgetInput::output('name', tr('Username:', 'users'), $form_data); ?>
	<?php print WidgetInput::output('password', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD); ?>

	<?php if ($pwd_url = ActionMapper::get_path('lost_password')): ?>
	<p><a href="<?=$pwd_url?>"><?=tr('Forgot password?', 'users')?></a></p>
	<?php endif; ?>
	<?php if ($resend_url = ActionMapper::get_path('resend_registration_mail')): ?>
	<p><a href="<?=$resend_url?>"><?=tr('Registered, but got no confirmation mail?', 'users')?></a></p>
	<?php endif; ?>
	<br />		
	<?php print WidgetInput::output('stayloggedin', tr('Stay loged in.', 'users'), $form_data, WidgetInput::CHECKBOX); ?> 

	<input class="button right" type="submit" name="submit" value="<?=tr('Login', 'users')?>" />&nbsp;
</form>
<br />
<?php if ($register_url = ActionMapper::get_path('register')): ?>
<h2><?=tr('Not registered yet?', 'users')?></h2>
<p><a href="<?=$register_url?>"><?=tr('Click here to become a member.', 'users')?></a></p>
<?php endif; ?>
