<?php 
$title = tr('Create new user', 'users');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(
	array(
		WidgetActionLink::output('Users', 'users_list_all'),
		GyroString::escape($title)
	)
);
?>
<h1><?=$title?></h1>

<p><?php print tr('Fill out the fields below and click <strong>Create</strong> to create a new user', 'users');?></p>

<form class="has_focus" name="frmcreateaccount" action="<?=$url_self?>" method="post">
	<?php $context = 'create'; ?>
	<?php gyro_include_template('users/inc/editwidgets'); ?>

	<br />	
	<input class="button right" type="submit" name="submit" value="<?=tr('Create', 'users')?>" />
</form>
