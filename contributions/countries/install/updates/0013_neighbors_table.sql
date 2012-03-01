CREATE TABLE `countries2neighbors` (
  `id_country_1` CHAR(2) NOT NULL,
  `id_country_2` CHAR(2) NOT NULL,
  PRIMARY KEY (`id_country_1`, `id_country_2`),
  CONSTRAINT `fk_countries2neighbors_1` FOREIGN KEY `fk_countries2neighbors_1` (`id_country_1`)
    REFERENCES `countries` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_countries2neighbors_2` FOREIGN KEY `fk_countries2neighbors_2` (`id_country_2`)
    REFERENCES `countries` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE = InnoDB;
