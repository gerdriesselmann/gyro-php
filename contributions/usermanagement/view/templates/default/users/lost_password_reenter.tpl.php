<?php
/* @var $page_data PageData */
$title = tr('Change Password', 'users');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(array(String::escape($title)));
?>
<h1><?=$title?></h1>

<form class="has_focus" id="frmeditaccount" name="frmeditaccount" action="<?=ActionMapper::get_path('lost_password_Change', array('token' => $token))?>" method="post">
 	<?php print $form_validation; ?>

	<?php print WidgetInput::output('pwd1', tr('Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>
	<?php print WidgetInput::output('pwd2', tr('Repeat Password:', 'users'), $form_data, WidgetInput::PASSWORD) ?>

	<?php print WidgetInput::output('submit', '', tr('Save', 'users'), WidgetInput::SUBMIT); ?>
</form>
