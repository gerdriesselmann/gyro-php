CREATE TABLE IF NOT EXISTS `notifications` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` INTEGER UNSIGNED,
  `title` VARCHAR(200) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('NEW','READ') NOT NULL DEFAULT 'NEW',
  `creationdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_notifications_user` FOREIGN KEY `fk_notifications_user` (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
