<?php
/**
 * @var $self ContentViewBase
 */
$link_subpage = Common::flag_is_set($policy, WidgetAlphabeticalList::LINK_TO_SUBPAGE_BELOW_SECTION);
$list = array();
foreach($data as $letter => $elements) {
	$content = '';
	$content .= html::tag('h3', GyroString::to_upper($letter), array('id' => 'letter_' . strtolower($letter) . '_'));
	foreach($elements as $item) {
		$subview = $self->create_child_view('widgets/alphalist/item');
		$subview->assign('item', $item);
		$content .= html::div($subview->render($policy), Arr::get_item($params, 'css_class'));
	}

	if ($link_subpage && count($elements) > 0) {
		$url = '/' . rtrim(Url::current()->get_path(), '/') . '/' . strtolower($letter) . '/';
		$link_title = Arr::get_item($params, 'more_title', tr('More...', 'alphabeticallist'));
		$link_title = str_replace('%letter', strtoupper($letter), $link_title);
		$content .= html::p(html::a(GyroString::escape($link_title), $url, ''));
	}
}
$list[] = $content;

$subview = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/alphalist/list');
$subview->assign('list', $list);
print $subview->render($policy);
?>
