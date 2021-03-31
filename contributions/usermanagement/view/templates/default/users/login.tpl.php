<?php  		
$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
$title = tr('Login', 'users');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(
	GyroString::escape($title)
);
?>
<h1><?=$title?></h1>
<form class="has_focus" id="frmLogin" name="frmLogin" action="<?=ActionMapper::get_url('login'); ?>" method="post">
 	<p><?php print tr('Please enter your username and password and click <strong>Login</strong>.', 'users'); ?></p>
	
	<?php gyro_include_template('users/inc/loginwidgets'); ?>
	<?php print WidgetInput::output('submit', '', tr('Login', 'users'), WidgetInput::SUBMIT); ?>
</form>


<?php if ($register_url = ActionMapper::get_path('register')): ?>
<h2><?=tr('Not registered yet?', 'users')?></h2>
<p><a href="<?=$register_url?>"><?=tr('Click here to become a member.', 'users')?></a></p>
<?php endif; ?>
