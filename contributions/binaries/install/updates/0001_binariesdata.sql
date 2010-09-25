CREATE TABLE IF NOT EXISTS `binariesdata` (
  `id_binary` int(10) unsigned NOT NULL,
  `data` longblob NOT NULL,
  PRIMARY KEY  (`id_binary`),
  CONSTRAINT `fk_binariesdata_binaries` FOREIGN KEY (`id_binary`) REFERENCES `binaries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO binariesdata (id_binary, data) SELECT id, data FROM binaries;

ALTER TABLE `binaries` DROP COLUMN `data`;
