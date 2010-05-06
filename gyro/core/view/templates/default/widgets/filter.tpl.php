<?php
if ($filter_group->count() > 1) {
	$filter_current = $filter_group->get_current_filter();
	$values = '';

	// List of links
	$arr_links = array();
	foreach ($filter_group->get_filters() as $filter_item) {
		$arr_links[] = html::a(
			String::escape($filter_item->get_title()),
			$filter_url_builder->get_filter_link($filter_item, $filter_group->get_group_id()),
			tr(
				'Filter by %name', 
				array('app', 'core'),  
				array('%name' => String::escape($filter_item->get_title()))
			)
		);
	}
	$values = implode(', ', $arr_links);		
	
	$filter_html = tr(
		'<p>%name filtered by: <strong>%current</strong>. <span class="noprint">Filter by %values</span></p>', 
		array('app', 'core'), 
		array(
			'%name' => String::escape($filter_group->get_name()),
			'%current' => String::escape($filter_current->get_title()),
			'%values' => $values		
		)		
	);
	?>
	<div class="filter">
	<?php print $filter_html; ?>
	</div>
	<?php
}	
?>