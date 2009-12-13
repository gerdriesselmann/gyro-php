<form action="<?=$action?>" method="post">
	<?php
	$options = array(
		'100' => tr('Very Good', 'voting'),
		'80' => tr('Good', 'voting'),
		'60' => tr('OK', 'voting'),
		'40' => tr('Bad', 'voting'),
		'20' => tr('Terribly Bad', 'voting'),
	);
	$default = '60';
	print WidgetInput::output(
		'value',
		tr('Your vote:', 'voting'),		
		$default,
		WidgetInput::RADIO,
		array(
			'options' => $options, 
			'id' => false
		)
	);
	print WidgetInput::output('', '', tr('Submit', 'voting'), WidgetInput::SUBMIT);
	?>
	
</form>

