<?php 
$title = tr('Sitemap Set', 'gsitemap');
$page_data->head->title = $title;
$page_data->head->robots = ROBOTS_NOINDEX_FOLLOW;
?>

<h1><?=$title?></h1>

<ul>
<?php foreach ($items as $map): ?>
<li><?php print $map->as_html();?></li>
<?php endforeach; ?>
</ul>