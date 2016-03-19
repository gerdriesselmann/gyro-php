<?php if ($filter_group->count() > 1): ?>
	<form action="." method="get">
	<?php
	$js_function = 'window.location=this.options[this.selectedIndex].value;';
	$options = array();
	
	$filter_current = $filter_group->get_current_filter();
	$url_current = $filter_url_builder->get_filter_link($filter_current, $filter_group->get_group_id());
	foreach ($filter_group->get_filters() as $filter_item) {
		$url = $filter_url_builder->get_filter_link($filter_item, $filter_group->get_group_id());
		$options[$url] = $filter_item->get_title();
	}
	
	print html::label(
		tr(
			'Filter by %topic: %select', 
			'dropdownsorter', 
			array(
				'%topic' => GyroString::escape($filter_group->get_name()),
				'%select' => html::select('', $options, $url_current, array('onchange' => $js_function)))
		),
		'',
		'filter'
	);
	?>
	</form>
<?php endif; ?>