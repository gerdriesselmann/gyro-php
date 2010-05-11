<?php 
$page_data->head->title = tr('Edit user %name%', 'users', array('%name%' => $user->name));
$page_data->breadcrumb = WidgetBreadcrumb::output(
	array(
		WidgetActionLink::output(tr('Users', 'users'), 'users_list_all'),
		$user,
		'Edit'
	)
);
?>
<h1><?=tr('Change Account Data', 'users')?></h1>

<p><?php print tr('Fill out the fields below and click <strong>Save</strong> to save your data.', 'users');?></p>

<form class="has_focus" id="frmeditaccount" name="frmeditaccount" action="<?=$url_self?>" method="post">
	<?php $context = 'edit'; ?>
	<?php gyro_include_template('users/inc/editwidgets'); ?>

	<br />	
	<input class="button right" type="submit" name="submit" value="<?=tr('Save', 'users')?>" />
</form>

<?php print WidgetItemMenu::output($user, 'edit') ?>
