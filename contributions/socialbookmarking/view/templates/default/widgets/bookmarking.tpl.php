<?php
$li = array();
$url = Url::current()->build();
foreach($services as $service) {
	$s_url = $service->get_url($page_data->head->title, $url);
	$li[] = html::a(
		html::img($service->get_image_path(), ''),
		$s_url,
		$service->service,
		array('target' => '_blank')	
	);
}

print html::div(html::li($li), 'socialbookmarking');
