<?php  		
$page_data->head->title = tr('Delete Your Account', 'users');
?>
<h1><?tr('Delete Your Account', 'users')?></h1>

<p><?php print tr('When deleting your account, all personal data will be deleted.', 'users')?></p>
<p class="info"><?php print tr('This action cannot be undone!', 'users') ?></p>

<form class="has_focus" id="frmdeleteaccount" name="frmdeleteaccount" action="<?=ActionMapper::get_url('users_delete_account'); ?>?>" method="post">
 	<?php print $form_validation; ?>
	<input class="button right" type="submit" name="submit" value="<?=tr('Delete Account Now', 'users')?>" />&nbsp;
</form>
