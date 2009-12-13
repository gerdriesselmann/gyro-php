<?php 
$title = tr('Page not found', 'core');
$page_data->head->title = $title;
?>

<h1><?=$title?></h1>

<p class="error"><?=tr('Sorry, but the page you were looking for does not exist.', 'core')?></p>
