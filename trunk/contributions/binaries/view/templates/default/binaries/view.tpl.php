<?php
/* @var $page_data PageData */
$page_data->in_history = false;
$page_data->get_cache_manager()->set_creation_datetime($binary->get_modification_date());
$self->assign(MimeView::MIMETYPE, $binary->mimetype);
print $binary->data;