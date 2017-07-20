SET foreign_key_checks = 0;

DROP TABLE IF EXISTS `authTokens`;
CREATE TABLE `authTokens` (
  `name` varchar(128) NOT NULL DEFAULT '',
  `token` varchar(128) NOT NULL DEFAULT '',
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `jobId` int(10) unsigned NOT NULL DEFAULT '0',
  `pid` varchar(64) DEFAULT '',
  `projectId` int(10) unsigned NOT NULL,
  `authToken` varchar(64) DEFAULT NULL,
  `status` enum('waiting','ready','error','deleted') NOT NULL DEFAULT 'waiting',
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdById` int(10) unsigned NOT NULL,
  `createdByName` varchar(128) NOT NULL,
  `deletedOn` datetime DEFAULT NULL,
  `deletedById` int(10) unsigned DEFAULT NULL,
  `deletedByName` varchar(128) DEFAULT NULL,
  `error` text,
  PRIMARY KEY (`jobId`),
  UNIQUE KEY `idx_pid` (`pid`) USING BTREE,
  KEY `idx_projectId` (`projectId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `jobId` int(10) unsigned NOT NULL DEFAULT '0',
  `uid` varchar(64) NOT NULL DEFAULT '',
  `login` varchar(255) NOT NULL DEFAULT '',
  `projectId` int(10) unsigned NOT NULL,
  `status` enum('waiting','ready','error','deleted') NOT NULL DEFAULT 'waiting',
  `createdOn` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `createdById` int(10) unsigned NOT NULL,
  `createdByName` varchar(128) NOT NULL DEFAULT '',
  `deletedOn` datetime DEFAULT NULL,
  `deletedById` int(10) unsigned DEFAULT NULL,
  `deletedByName` varchar(128) DEFAULT NULL,
  `error` text,
  PRIMARY KEY (`jobId`),
  UNIQUE KEY `idx_login` (`login`) USING BTREE,
  KEY `idx_projectId` (`projectId`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

SET foreign_key_checks = 1;