<?php
$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
$title = tr('Become a member', 'users');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(
	GyroString::escape($title)
);?>

<h1><?=$title;?></h1>

<p><?php print tr('Fill in the fields and click <strong>Register</strong> to become a member. You will get an e-mail with a confirmation link afterwards.', 'users'); ?></p>

<form class="has_focus" id="frmregister" name="frmregister" action="<?=ActionMapper::get_path('register')?>" method="post">
	<?php gyro_include_template('users/inc/signupwidgets')?>
	<?php print WidgetInput::output('submit', '', tr('Register', 'users'), WidgetInput::SUBMIT); ?>
</form>
