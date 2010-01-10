<?php if (Config::has_feature(Config::TESTMODE)): ?>
User-agent: *
Disallow: /	
<?php else: ?>
User-agent: *
Disallow: /css/
Disallow: /js/
Disallow: /images/
<?php if (Load::is_module_loaded('gsitemap')):?>
Sitemap: <?=ActionMapper::get_url('gsitemap_index')?>
<?php endif;?>
<?php endif; ?>

