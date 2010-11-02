<ul class="l<?=$level?>">
<?php
$v = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/tree/node');
$l = count($nodes) - 1;
$i = 0;
foreach($nodes as $node) {
	$cls = array('l' . $level);
	if ($i == 0) { $cls[] = 'first'; }
	if ($i == $l) { $cls[] = 'last'; }
	if ($i % 2) { $cls[] = 'even'; } else { $cls[] = 'uneven'; }
	
	if ($node['is_branch']) { $cls[] = 'branch'; }
	
	$txt = '';
	$v->assign('node', $node['item']);
	$txt .= $v->render();
	
	$childs = $node['childs'];
	if (count($childs)) {
		$cls[] = 'open';
		if ($i == 0) { $cls[] = 'first_open'; } // IE 6 does not have multiple selectors like .first.open
		if ($i == $l) { $cls[] = 'last_open'; } // IE 6 does not have multiple selectors like .last.open
		$child_view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/tree/nodes');
		$child_view->assign('nodes', $childs);
		$child_view->assign('level', $level + 1);
		$txt .= $child_view->render();
	}
	
	print html::tag('li', $txt, array('class' => implode(' ', $cls)));
	$i++;
}
?>
</ul>