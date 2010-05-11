<?php
/* @var $page_data PageData */
$page_data->head->title = tr('Edit your account settings', 'users');
$page_data->breadcrumb = WidgetBreadcrumb::output(array(
	WidgetActionLink::output(tr('Users', 'users'), 'users_list_all'),
	$user,
	tr('Edit', 'users')
))
?>
<h1><?=tr('Change Account Data', 'users')?></h1>

<p><?php print tr('Fill out the fields below and click <strong>Save</strong> to save your data.', 'users');?></p>

<form class="has_focus" id="frmeditaccount" name="frmeditaccount" action="<?=ActionMapper::get_path('users_edit_self')?>" method="post">
 	<?php print $form_validation; ?>

	<fieldset>
	<legend><?=tr('User Data', 'users')?></legend>

	<?php print WidgetInput::output('name', tr('Username:', 'users'), $form_data) ?>
	<?php print WidgetInput::output('email', tr('E-mail:', 'users'), $form_data) ?>

	<p class="important">
	<?php print tr('If the e-mail changes, you will get a mail send to the new address to confirm this address exists.', 'users')?></p>

	<?php print WidgetInput::output('pwd1', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>
	<?php print WidgetInput::output('pwd2', tr('Repeat Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>

	<p><?php print tr('Leave these fields empty to not change the password.',  'users')?></p> 
	</fieldset>

	<br />
	
	<input class="button right" type="submit" name="submit" value="<?=tr('Save', 'users')?>" />
</form>
