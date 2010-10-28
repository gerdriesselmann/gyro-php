<?php
$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/tree/nodes');
$view->assign('nodes', $tree);
$view->assign('level', 0);
print html::div($view->render(), 'tree');
