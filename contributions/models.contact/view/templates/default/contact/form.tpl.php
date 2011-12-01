<?php
/**
 * @var PageData $page_data
 * @var bool $is_logged_in
 * @var DAOUsers $current_user
 */
?>
<h1><?=$page_data->head->title?></h1>
<p>You can leave a message using the contact form below.</p>

<form name="frmcontact" class="has_focus" action="<?=ActionMapper::get_path('contact_form')?>" method="post">
	<?php gyro_include_template('contact/inc/widgets'); ?>
	<?php print WidgetInput::output('', '', tr('Send', 'contact'), WidgetInput::SUBMIT); ?>
</form>
