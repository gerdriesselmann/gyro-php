<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd">
<?php foreach ($files as $item): ?>
<sitemap>
	<loc><?=$baseurl_http?>sitemap.xml?i=<?=$item?></loc>	
</sitemap>
<?php endforeach; ?>
</sitemapindex>
