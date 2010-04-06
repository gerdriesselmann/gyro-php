ALTER TABLE `formvalidations` 
  MODIFY COLUMN `token` CHAR(40) ASCII NOT NULL,
  ADD INDEX `i_formvalidations_expire`(`expirationdate`);
