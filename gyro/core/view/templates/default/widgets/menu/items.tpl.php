<?php
$action_view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/action');
$command_view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/command');
// Output Actions
$items = array();
foreach($actions as $action) {
	// commands
	$view = ($action instanceof ICommand) ? $command_view : $action_view; 
	$view->assign('action', $action);
	$view->assign('form_validation', $form_validation);
	$items[$action->get_name()] = $view->render();
}
$c = count($items) - 1;
$i = 0;
foreach($items as $name => $item) {
	$name = String::plain_ascii($name);
	$cls = array($class);
	if ($i === 0) {
		$cls[] = "{$class}_first";
	}
	if ($i === $c) {
		$cls[] = "{$class}_last";
	}
	$cls[] = "{$class}_{$name}"; 
	print html::tag('li', $item, array('class' => implode(' ', $cls)));
	$i++;
}
