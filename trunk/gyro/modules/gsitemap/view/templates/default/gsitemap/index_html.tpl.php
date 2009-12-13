<?php 
$title = tr('Sitemap Index', 'gsitemap');
$page_data->head->title = $title;
$page_data->head->robots = ROBOTS_NOINDEX_FOLLOW;
?>

<h1><?=$title?></h1>
<ul>
<?php foreach($files as $map): ?>
<li><a href="<?=ActionMapper::get_path('gsitemap_site_html') . '?i=' . $map?>"><?=ActionMapper::get_url('gsitemap_site_html') . '?i=' . $map?></a></li>
<?php endforeach; ?>
</ul>