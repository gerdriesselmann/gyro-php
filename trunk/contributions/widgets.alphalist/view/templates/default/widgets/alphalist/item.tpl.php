<?php
/**
 *
 */
if ($item instanceof IDataObject) {
	print WidgetListItem::output($item, $policy);
} else {
	print Cast::string($item);
}