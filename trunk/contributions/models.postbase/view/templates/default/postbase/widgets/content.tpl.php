<fieldset>
<legend><?=tr('Content', 'postbase')?></legend>
<?php
print WidgetInput::output('title', tr('Title:', 'postbase'), $form_data);
print WidgetInput::output('teaser', tr('Teaser:', 'postbase'), $form_data, WidgetInput::TEXTAREA);
print WidgetInput::output('text', tr('Description:', 'postbase'), $form_data, HtmlText::WIDGET, array('model' => $model));
?>
</fieldset>