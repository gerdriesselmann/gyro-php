CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(60) NOT NULL,
  `data` longtext,
  `modificationdate` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `cache` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `key0` varchar(255) default NULL,
  `key1` varchar(255) default NULL,
  `key2` varchar(255) default NULL,
  `content` longtext,
  `content_gzip` longblob,
  `data` text,
  `creationdate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `expirationdate` datetime default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `KEYS` (`key0`,`key1`,`key2`),
  KEY `KEY0` (`key0`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `formvalidations` (
  `token` varchar(35) NOT NULL,
  `name` varchar(35) NOT NULL,
  `expirationdate` datetime default NULL,
  PRIMARY KEY  (`token`,`name`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
