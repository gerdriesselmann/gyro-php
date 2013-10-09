<?php
$param = $adapter->get_param();
$reset = $adapter->get_reset_param();
$param_submit = $param . '_submit';
?>
<div class="textfilter">
	<form class="has_focus" name="frm_<?=$param?>" action="<?=$url_self?>" method="get">
		<?php
		foreach($page_data->get_get()->get_array() as $key => $v) {
			if ($key != $param && $key != $reset && $key != $param_submit) {
				print WidgetInput::output($key, '', $v, WidgetInput::HIDDEN, array('id' => ''));
			}		
		}
		?>
		<?php print WidgetInput::output($param, tr('Filter by %title', array('app', 'core'), array('%title' => $title)), $value, WidgetInput::TEXT, array(), WidgetInput::NO_BREAK) ?>
		<?php print WidgetInput::output($param_submit, '', tr('Filter', array('app', 'core')), WidgetInput::SUBMIT, array(), WidgetInput::NO_BREAK) ?>
		<?php print WidgetInput::output($reset, '', tr('No filter', array('app', 'core')), WidgetInput::SUBMIT, array(), WidgetInput::NO_BREAK) ?>
	</form>
</div>	
