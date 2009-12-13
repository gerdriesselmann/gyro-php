<?php 
$title = tr('Sitemap Set', 'gsitemap');
$page_data->head->title = $title;
$page_data->head->robots = ROBOTS_NOINDEX_FOLLOW;
?>

<h1><?=$title?></h1>

<ul>
<?php foreach ($files as $map): ?>
<li><a href="<?=$map['url']?>"><?=$map['url']?></a></li>
<?php endforeach; ?>
</ul>