DROP TABLE IF EXISTS `hijackaccountsavedsessions`;
CREATE TABLE  `hijackaccountsavedsessions` (
  `id` VARCHAR(40) NOT NULL,
  `id_user` INTEGER UNSIGNED NOT NULL,
  `data` longtext,
  `expirationdate` DATETIME NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `hijackaccountsavedsessions`
 ADD CONSTRAINT `fk_hijackaccountsavedsessions_user` FOREIGN KEY `fk_hijackaccountsavedsessions_user` (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT;
