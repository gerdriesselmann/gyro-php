<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($files as $item): ?>
<sitemap>
	<loc><?=$baseurl_http?>sitemap.xml?i=<?=$item?></loc>	
</sitemap>
<?php endforeach; ?>
</sitemapindex>
