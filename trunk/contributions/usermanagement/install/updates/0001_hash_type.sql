ALTER TABLE `users` 
 ADD COLUMN `hash_type` VARCHAR(5) NOT NULL DEFAULT 'md5' AFTER `password`,
 ADD INDEX `i_users_name`(`name`);

ALTER TABLE `users` 
 MODIFY COLUMN `name` VARCHAR(50) NOT NULL,
 MODIFY COLUMN `password` VARCHAR(50) NOT NULL,
 MODIFY COLUMN `email` VARCHAR(255) NOT NULL,
 MODIFY COLUMN `creationdate` DATETIME NOT NULL;