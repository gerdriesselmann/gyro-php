ALTER TABLE `notifications` 
 MODIFY COLUMN `creationdate` DATETIME NOT NULL,
 ADD COLUMN `modificationdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `creationdate`;
 
UPDATE notifications SET modificationdate = creationdate;

