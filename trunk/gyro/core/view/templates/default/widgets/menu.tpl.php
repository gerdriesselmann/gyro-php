<?php
if (count($actions) || count($commands)) {
	$out = '';
	$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/items');

	$view->assign('actions', $actions);
	$view->assign('class', 'actions');
	$out .= $view->render();
	
	$view->assign('actions', $commands);
	$view->assign('class', 'commands');
	$out .= $view->render();
	
	print html::div(
		html::tag('ul', $out),			
		$css_class
	);
}
