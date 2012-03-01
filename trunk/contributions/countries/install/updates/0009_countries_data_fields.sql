ALTER TABLE `countries`
 ADD COLUMN `capital` VARCHAR(50) AFTER `name`,
 ADD COLUMN `area` FLOAT UNSIGNED AFTER `capital`,
 ADD COLUMN `population` INTEGER UNSIGNED AFTER `area`,
 ADD COLUMN `currency` CHAR(3) AFTER `population`,
 ADD COLUMN `lat1` FLOAT AFTER `currency`,
 ADD COLUMN `lon1` FLOAT AFTER `lat1`,
 ADD COLUMN `lat2` FLOAT AFTER `lon1`,
 ADD COLUMN `lon2` FLOAT AFTER `lat2`;

