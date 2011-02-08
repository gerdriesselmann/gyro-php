<?php
$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/tree/nodes');
$view->assign('nodes', $tree);
$view->assign('level', 0);
$view->assign('action', $action);
$view->assign('params', $params);
print html::div($view->render(), 'tree');
