CREATE TABLE IF NOT EXISTS `systemupdates` (
  `component` VARCHAR(50) NOT NULL,
  `version` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`component`)
)
ENGINE = InnoDB;
