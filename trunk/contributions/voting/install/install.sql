CREATE TABLE IF NOT EXISTS `votes` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance` VARCHAR(255) NOT NULL,
  `value` INTEGER UNSIGNED NOT NULL,
  `weight` INTEGER UNSIGNED DEFAULT 1,
  `voterid` VARCHAR(30),
  `creationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `i_votes_instance`(`instance`)
)
ENGINE = InnoDB;

