CREATE TABLE IF NOT EXISTS `users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(50) default NULL,
  `password` varchar(50) default NULL,
  `email` varchar(50) default NULL,
  `status` enum('UNCONFIRMED','ACTIVE','DELETED','DISABLED') NOT NULL default 'UNCONFIRMED',
  `creationdate` datetime default NULL,
  `modificationdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `permanentlogins` (
  `code` varchar(50) NOT NULL,
  `id_user` int(10) unsigned NOT NULL,
  `expirationdate` datetime default NULL,
  PRIMARY KEY  (`code`),
  KEY `fk_permanentlogins_user` (`id_user`),
  CONSTRAINT `fk_permanentlogins_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `userroles` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

CREATE TABLE IF NOT EXISTS `users2userroles` (
  `id_user` INTEGER UNSIGNED NOT NULL,
  `id_role` INTEGER UNSIGNED NOT NULL,
  PRIMARY KEY (`id_user`, `id_role`),
  CONSTRAINT `fk_users2userroles_user` FOREIGN KEY `fk_users2userroles_user` (`id_user`)
    REFERENCES `users` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_users2userroles_role` FOREIGN KEY `fk_users2userroles_role` (`id_role`)
    REFERENCES `userroles` (`id`)
    ON DELETE CASCADE
    ON UPDATE RESTRICT
)
ENGINE = InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

INSERT IGNORE INTO userroles (id, name) VALUES 
	(1, 'admin'),
	(2, 'editor'),
	(3, 'user');

