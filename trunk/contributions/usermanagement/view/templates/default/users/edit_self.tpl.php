<?php
/* @var $page_data PageData */
$page_data->head->title = tr('Edit your account settings', 'users');
$page_data->breadcrumb = WidgetBreadcrumb::output(array(
	tr('Change Account Data', 'users')
))
?>
<h1><?=tr('Change Account Data', 'users')?></h1>

<p><?php print tr('Fill out the fields below and click <strong>Save</strong> to save your data.', 'users');?></p>

<form class="has_focus" id="frmeditaccount" name="frmeditaccount" action="<?=ActionMapper::get_path('users_edit_self')?>" method="post">
 	<?php print $form_validation; ?>
	<?php gyro_include_template('users/inc/editselfwidgets'); ?>

	<?php print WidgetInput::output('submit', '', tr('Save', 'users'), WidgetInput::SUBMIT); ?>
</form>
