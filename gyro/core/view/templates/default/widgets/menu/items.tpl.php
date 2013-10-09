<?php
/**
 * @var $action IActionBase
 */
$action_view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/action');
$command_view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/command');
// Output Actions
$items = array();
foreach($actions as $action) {
	// commands
	$view = ($action instanceof ICommand) ? $command_view : $action_view;
	$view->assign('action', $action);
	$view->assign('form_validation', $form_validation);
	$items[] = array($action->get_name() => $view->render());
}
$_menu_group_view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/menu/item.list');
$_menu_group_view->assign('items', $items);
$_menu_group_view->assign('class', $class);
print $_menu_group_view->render();
