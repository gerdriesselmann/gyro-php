CREATE TABLE IF NOT EXISTS `confirmations` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_item` INTEGER UNSIGNED NOT NULL,
  `code` VARCHAR(50) NOT NULL,
  `data` TEXT,
  `action` VARCHAR(20) NOT NULL,
  `expirationdate` DATETIME NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `iu_confirmations`(`id_item`, `code`)
)
ENGINE = InnoDB CHARSET=utf8;
