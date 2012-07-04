CREATE TABLE `countries2polygons` (
  `id_country` CHAR(2) NOT NULL,
  `polygon` TEXT NOT NULL,
  `lat1` FLOAT NOT NULL,
  `lon1` FLOAT NOT NULL,
  `lat2` FLOAT NOT NULL,
  `lon2` FLOAT NOT NULL,
  CONSTRAINT `fk_countries2polygons_country` FOREIGN KEY `fk_countries2polygons_country` (`id_country`)
    REFERENCES `countries` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE = InnoDB DEFAULT CHARSET=utf8;
