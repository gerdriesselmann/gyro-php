CREATE TABLE `notificationssettings` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` INTEGER UNSIGNED NOT NULL,
  `mail_enable` ENUM('FALSE','TRUE') NOT NULL DEFAULT 'TRUE',
  `mail_settings` TEXT DEFAULT NULL,
  `digest_enable` ENUM('FALSE','TRUE') NOT NULL DEFAULT 'FALSE',
  `digest_settings` TEXT DEFAULT NULL,
  `digest_last_sent` DATETIME,
  `feed_enable` ENUM('FALSE','TRUE') NOT NULL DEFAULT 'FALSE',
  `feed_settings` TEXT,
  `feed_token` VARCHAR(40),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_notificationssettings_users` FOREIGN KEY `fk_notificationssettings_users` (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
