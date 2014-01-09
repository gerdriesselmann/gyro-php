ALTER TABLE `tokens`
  ADD COLUMN `expirationdate` DATETIME NOT NULL  AFTER `token` ;

UPDATE tokens SET expirationdate = DATE_ADD(creationdate, INTERVAL 10 DAY);
