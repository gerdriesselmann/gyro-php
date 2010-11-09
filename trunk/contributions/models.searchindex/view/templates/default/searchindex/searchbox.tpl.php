<form action="<?=ActionMapper::get_path('searchindex_search')?>" method="get">
	<?php print WidgetInput::output('q', tr('Search:', 'searchindex'), $_GET, WidgetInput::TEXT, array('id' => ''))?>
	<?php print WidgetInput::output('', '', tr('Find', 'searchindex'), WidgetInput::SUBMIT)?>
</form>
