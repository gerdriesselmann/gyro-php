ALTER TABLE `notifications` 
 ADD COLUMN `source_id` INTEGER AFTER `source`,
 ADD COLUMN `source_data` TEXT AFTER `source_id`,
 ADD COLUMN `sent_as` SET('MAIL','FEED','DIGEST') AFTER `source_data`,
 ADD COLUMN `read_through` ENUM('UNKNOWN','MANUALLY','AUTO','ALL','MAIL','FEED','DIGEST','CONTENT') NOT NULL DEFAULT 'UNKNOWN' AFTER `sent_as`,
 ADD COLUMN `read_action` VARCHAR(30) AFTER `read_through`;
 
ALTER TABLE `notifications` 
 ADD INDEX `i_user_status_source`(`id_user`, `status`, `source`);