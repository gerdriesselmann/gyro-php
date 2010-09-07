<?php
foreach ($blocks as $block) {
	$view = ViewFactory::create_view(IViewFactory::MESSAGE, 'widgets/block');
	$view->assign('block', $block);
	print $view->render();
}
