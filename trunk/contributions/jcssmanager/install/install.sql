CREATE TABLE IF NOT EXISTS `jcsscompressedfiles` (
  `type` enum('JS','CSS','CSS_IE50','CSS_IE6','CSS_IE55','CSS_IE7') NOT NULL,
  `filename` varchar(255) NOT NULL,
  `hash` varchar(255) NOT NULL,
  `sources` text NOT NULL,
  `version` int(10) unsigned NOT NULL default '1',
  PRIMARY KEY  (`type`)
) ENGINE=InnoDB;