<?php 
print WidgetSearchIndexSearchBox::output(WidgetSearchIndexSearchBox::CONTEXT_CONTENT);

if ($terms !== '') {
	print html::h(String::escape($page_data->head->title), 1);
	print WidgetList::output($page_data, $self, $result, tr('No results were found', 'searchindex'));
}
else {
	print html::info(tr('Please enter a search query.', 'searchindex'));
}
