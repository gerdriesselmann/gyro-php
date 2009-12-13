<div class="sorter">
<form action="." method="get">
<?php
$js_function = 'window.location=this.options[this.selectedIndex].value;';
$options = array();
$sorter_current_column = $sorter_data['current_column'];

foreach($sorter_data['columns'] as $sorter_loop_column) {
	$both_directions = isset($sorter_loop_column['other_sort_link']);
	
	$sorter_option_key = $sorter_loop_column['link'];
	$sorter_option_value = $sorter_loop_column['title'];
	if ($both_directions) {
		$sorter_option_value .= ' - ' . $sorter_loop_column['sort_title'];
	}
	$options[$sorter_option_key] = $sorter_option_value;

	if ($both_directions) {
		$sorter_option_key = $sorter_loop_column['other_sort_link'];
		$sorter_option_value = $sorter_loop_column['title'] . ' - ' . $sorter_loop_column['other_sort_title'];
		$options[$sorter_option_key] = $sorter_option_value;
	} 
}
print html::label(
	tr(
		'Sort by: %select', 
		'dropdownsorter', 
		array('%select' => html::select('', $options, $sorter_current_column['link'], array('onchange' => $js_function)))
	),
	'',
	'sorter'
);
?>
</form>
</div>