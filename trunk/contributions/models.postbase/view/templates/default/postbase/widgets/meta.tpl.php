<fieldset>
<legend><?=tr('Meta Information', 'postbase')?></legend>
<?php
print WidgetInput::output('meta_title', tr('Meta title:', 'postbase'), $form_data);
print WidgetInput::output('meta_description', tr('Meta description:', 'postbase'), $form_data, WidgetInput::TEXTAREA);
print WidgetInput::output('meta_keywords', tr('Meta keywords:', 'postbase'), $form_data, WidgetInput::TEXT);
?>
</fieldset>