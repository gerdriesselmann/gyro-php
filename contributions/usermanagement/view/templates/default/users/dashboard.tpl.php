<?php 
$page_title = tr('Welcome %name', 'users', array('%name' => $current_user->name));
$page_data->head->robots_index = ROBOTS_NOINDEX;
$page_data->head->title = $page_title; 
$page_data->breadcrumb = WidgetBreadcrumb::output(
	tr('Your personal site', 'users')
);
?>

<h1><?=$page_title?></h1>

<?php
$content = '';
$section_links = array();
foreach($dashboards as $dashboard) {
	$id = get_class($dashboard);
	$title = $dashboard->get_title();
	$dashboard_content = $dashboard->get_content($page_data);
	if ($dashboard_content) {
		$section_links[] = html::a($title, '#' . $id, $dashboard->get_description());
		$content .= html::tag(
			'div',
			html::h($title, 2) . html::div($dashboard_content, 'content'),
			array(
				'class' => 'entry',
				'id' => $id
			)
		);
	}
}
if (count($section_links) > 1) {
	print html::li($section_links);
}
print $content;
