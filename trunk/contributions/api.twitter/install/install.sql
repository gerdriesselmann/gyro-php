CREATE TABLE `tweets` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_twitter` VARCHAR(20) NOT NULL,
  `username` VARCHAR(20) NOT NULL,
  `title` VARCHAR(140) NOT NULL,
  `message` VARCHAR(140) NOT NULL,
  `message_html` TEXT NOT NULL,
  `creationdate` DATETIME NOT NULL,
  `modificationdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `i_tweets_id_twitter`(id_twitter)
)
ENGINE = InnoDB;
