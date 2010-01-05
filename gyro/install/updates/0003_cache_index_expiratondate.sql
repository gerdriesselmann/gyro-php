ALTER TABLE `cache` 
 DROP INDEX `KEY0`,
 ADD INDEX `i_cache_expirationdate`(`expirationdate`);
 
