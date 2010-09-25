<?php
$page_data->in_history = false;
$self->assign(MimeView::MIMETYPE, $binary->mimetype);
print $binary->data;