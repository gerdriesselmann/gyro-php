CREATE TABLE IF NOT EXISTS `votesaggregates` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `instance` VARCHAR(255) NOT NULL,
  `average` FLOAT NOT NULL,
  `numtotal` INTEGER UNSIGNED NOT NULL,
  `modificationdate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `i_votesaggregates_instance`(`instance`)
)
ENGINE = InnoDB;

DELETE FROM votesaggregates;

INSERT INTO votesaggregates 
	(instance, average, numtotal)
	SELECT 
		instance, SUM(value * weight)/SUM(weight) AS average, SUM(weight) as numtotal	
	FROM 
		votes
	GROUP BY instance;
