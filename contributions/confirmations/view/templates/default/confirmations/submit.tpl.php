<?php
/**
 * @var $handler IConfirmationHandler
 * @var $form_validation $string
 */
// Wrapper around deletion approval dialog
$title = tr('Please confirm', 'confirmations');
if ($handler instanceof ISelfDescribing) {
	$title .= ': ' . $handler->get_title();
}
?>
<h1><?=$title ?></h1>

<form action="<?=Url::current()->build(Url::RELATIVE)?>" method="post">
	<?php print $form_validation ?>
	<?php print WidgetInput::output(
		'submit',
		'',
		tr('Confirm', 'confirmations'),
		WidgetInput::SUBMIT
	); ?>
</form>
