<?php
/**
 * Sets a Content-Disposition header, so a filename can be provided
 *
 * @var $page_data PageData
 * @var $binary DAOBinaries
 */
$page_data->in_history = false;
$page_data->get_cache_manager()->set_creation_datetime($binary->get_modification_date());
$self->assign(MimeView::MIMETYPE, $binary->mimetype);
Common::header('Content-Disposition', 'attachment; filename="' . $binary->name . '"');
print $binary->data;