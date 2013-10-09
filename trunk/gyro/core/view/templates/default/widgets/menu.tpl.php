<?php
if (count($actions) || count($commands)) {
	$out = '';
	$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/items');
	$view->assign('form_validation', $form_validation);

	$view->assign('actions', $actions);
	$view->assign('class', 'actions');
	$out .= $view->render();
	
	$view->assign('actions', $commands);
	$view->assign('class', 'commands');
	$out .= $view->render();

	$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/group');
	$view->assign('content', $out);
	$view->assign('css_class', $css_class);
	print $view->render();
}
