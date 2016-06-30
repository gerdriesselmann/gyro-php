<urlset xmlns="http://www.google.com/schemas/sitemap/0.84" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">
<?php foreach($items as $item): ?>
   <url>
      <loc><?=ActionMapper::get_url('view', $item)?></loc>
      <news:news>
         <news:publication_date><?=GyroDate::iso_date($item->get_publication_date())?></news:publication_date>
         <news:keywords><?php print GyroString::clear_html(implode(', ', $item->get_publication_keywords()))?></news:keywords>         
      </news:news>
   </url>
<?php endforeach; ?>
</urlset>