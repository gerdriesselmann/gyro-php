<?php
$title = tr('Resend Activation Mail', 'users');
$page_data->head->title = $title;
$page_data->head->robots_index = ROBOTS_NOINDEX_FOLLOW;

$page_data->breadcrumb = WidgetBreadcrumb::output(
	$title
);
?>
<h1><?=$title?></h1>
<?php print html::p(tr('If you registered but did not receive an activation e-mail, you can force the mail to be resend by filling out the form and clicking on <strong>Resend</strong> below', 'users'));?>
<?php print html::info(tr('Please check, if the mail has been blocked as spam, before trying to resend.')); ?>

<form class="has_focus" id="frmresregmail" name="frmresregmail" action="<?=$url_self?>" method="post">
 	<?php print $form_validation; ?>
	<?php print WidgetInput::output('email', tr('E-Mail:', 'users'), $form_data); ?>

	<br />
	<input class="button right" type="submit" name="submit" value="<?=tr('Resend', 'users')?>" />
</form>
