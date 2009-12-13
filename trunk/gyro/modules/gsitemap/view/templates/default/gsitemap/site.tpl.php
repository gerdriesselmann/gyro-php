<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">
<?php foreach ($files as $item): ?>
<url>
	<loc><?=$item['url']?></loc>
	<?php if ($item['lastmod'] > 0): ?><lastmod><?php print GyroDate::iso_date($item['lastmod']); ?></lastmod><?php endif; ?>
</url>
<?php endforeach; ?>
</urlset>