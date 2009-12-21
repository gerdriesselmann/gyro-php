<?php if (Config::has_feature(Config::TESTMODE)): ?>
User-agent: *
Disallow: /	
<?php else: ?>
User-agent: *
Disallow: /css/
Disallow: /js/
Disallow: /images/
<?php endif; ?>