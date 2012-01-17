<?php
$nav = array();
$subpages = Common::flag_is_set($policy, WidgetAlphabeticalList::LINK_TO_SUBPAGES);

foreach($letters as $letter) {
	$letter_u = strtoupper($letter);
	$url = $subpages ? $base_url . $letter . '/' : '#letter_' . $letter . '_';
	$cls = ($selected == $letter_u) ? 'current' : '';
	$a = html::a($letter_u, $url, '', array('class' => $cls));
	$nav[] = $a;
}

print html::p(implode(' ', $nav), 'letterbar');
?>