<?php
$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;
$title = tr('Lost Password', 'users');
$page_data->head->title = $title;
$page_data->breadcrumb = WidgetBreadcrumb::output(
	$title
);
?>
<h1><?=$title?></h1>
<p class="info">
<?php print tr(
	'If you enter your e-mail address, the system will send you a link to log you in for once. You then will be able to change your password.',
	'users'
);
?>  
</p>
<br />

<form class="has_focus" id="frmlostpwd" name="frmlostpwd" action="<?ActionMapper::get_path('lost_password')?>" method="post">
 	<?php print $form_validation; ?>
 	<p>
 	<?php
 	print tr(
 		'Please enter your e-mail address and click <strong>Submit</strong> afterwards.',
 		'users'
 	); 
 	?>
 	</p>
 	  
 	<?php print WidgetInput::output('email', tr('E-Mail:', 'users'), $form_data); ?> 

	<br />
	<?php print WidgetInput::output('submit', '', tr('Submit', 'users'), WidgetInput::SUBMIT); ?>
</form>
