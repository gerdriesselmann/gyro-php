ALTER TABLE `notifications` 
  ADD COLUMN `source` VARCHAR(100) NOT NULL DEFAULT 'app' AFTER `message`;
