<h1><?=tr('Please authentify', 'systemupdate')?></h1>

<form action="<?=$url_self?>" method="post">
<?php print $form_validation ?>
<?php print WidgetInput::output('a', '', $form_data, WidgetInput::PASSWORD) ?>
<?php print WidgetInput::output('sumbit', '', tr('Submit', 'systemupdate'), WidgetInput::SUBMIT) ?>
</form>
