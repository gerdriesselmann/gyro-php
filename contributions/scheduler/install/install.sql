CREATE TABLE IF NOT EXISTS `scheduler` (
  `id` integer unsigned NOT NULL auto_increment,
  `scheduledate` datetime NOT NULL,
  `reschedule_error` enum('TERMINATOR1','TERMINATOR2','TERMINATOR3','DIEHARD1','DIEHARD2','DIEHARD3','24HOURS') NOT NULL default 'TERMINATOR1',
  `reschedule_success` enum('TERMINATOR1','TERMINATOR2','TERMINATOR3','DIEHARD1','DIEHARD2','DIEHARD3','24HOURS') NOT NULL default 'TERMINATOR1',
  `runs_error` integer unsigned NOT NULL default '0',
  `runs_success` integer unsigned NOT NULL default '0',
  `action` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `error_message` TEXT,
  `status` enum('ACTIVE','DISABLED','ERROR','RESCHEDULED') NOT NULL default 'ACTIVE',
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

