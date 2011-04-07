ALTER TABLE `users` 
 ADD COLUMN `emailconfirmationdate` DATETIME AFTER `hash_type`,
 ADD COLUMN `emailstatus` ENUM('UNCONFIRMED','CONFIRMED','EXPIRED','BOUNCED') NOT NULL DEFAULT 'UNCONFIRMED' AFTER `emailconfirmationdate`,
 ADD COLUMN `tos_version` INTEGER UNSIGNED NOT NULL DEFAULT 0 AFTER `emailstatus`;
 
-- Double opt-in on registration... --
UPDATE users SET emailstatus = 'CONFIRMED', emailconfirmationdate = creationdate WHERE status = 'ACTIVE';

 
