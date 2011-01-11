CREATE TABLE IF NOT EXISTS `notificationsexceptions` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_user` INTEGER UNSIGNED NOT NULL,
  `source` VARCHAR(100) NOT NULL,
  `source_id` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `i_notificationsexceptions_source`(`id_user`, `source`, `source_id`),
  CONSTRAINT `fk_notificationsexceptions_user` FOREIGN KEY `fk_notificationsexceptions_user` (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
