<fieldset>
<legend><?=tr('Originating', 'postbase')?></legend>
<?php
print WidgetInput::output('originator', tr('Originator:', 'postbase'), $form_data);
print WidgetInput::output('originator_source', tr('Originator source:', 'postbase'), $form_data, WidgetInput::TEXT);
print WidgetInput::output('originator_url', tr('Originator URL:', 'postbase'), $form_data, WidgetInput::TEXT);
print WidgetInput::output('license', tr('License:', 'postbase'), $form_data);
?>
</fieldset>