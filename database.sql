CREATE TABLE `log` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `message` TEXT collate utf8_unicode_ci NOT NULL,
  `date` DATETIME NOT NULL,
  `class` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `method` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `linenumber` INT NOT NULL,
  `level` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `file` TEXT collate utf8_unicode_ci NOT NULL,
  `username` TINYTEXT collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `user` (
  `id` SMALLINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `authkey` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `level` TINYINT unsigned default NULL,
  `ver` INT unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`, `ver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `serie` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `year` SMALLINT,
  `url` TINYTEXT collate utf8_unicode_ci,
  `file` TINYTEXT collate utf8_unicode_ci,
  `torrent` TINYTEXT collate utf8_unicode_ci,
  `ver` INT unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`, `ver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `season` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `serieid` INT UNSIGNED NOT NULL,
  `season` INT UNSIGNED NOT NULL,
  `state` TINYTEXT collate utf8_unicode_ci,
  `watched` INT(1) NOT NULL DEFAULT '0',
  `file` TINYTEXT collate utf8_unicode_ci,
  `torrent` TINYTEXT collate utf8_unicode_ci,
  `ver` INT unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`, `ver`),
  FOREIGN KEY (`serieid`) references `serie`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `episode` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `serieid` INT UNSIGNED NOT NULL,
  `seasonid` INT UNSIGNED NOT NULL,
  `episode` INT UNSIGNED NOT NULL,
  `title` TINYTEXT collate utf8_unicode_ci,
  `format` TINYTEXT collate utf8_unicode_ci,
  `watched` INT(1) NOT NULL DEFAULT '0',
  `file` TINYTEXT collate utf8_unicode_ci,
  `torrent` TINYTEXT collate utf8_unicode_ci,
  `ver` INT unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`, `ver`),
  FOREIGN KEY (`serieid`) references `serie`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  FOREIGN KEY (`seasonid`) references `season`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `movie` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `year` SMALLINT,
  `url` TINYTEXT collate utf8_unicode_ci,
  `format` TINYTEXT collate utf8_unicode_ci,
  `watched` INT(1) NOT NULL DEFAULT '0',
  `file` TINYTEXT collate utf8_unicode_ci,
  `torrent` TINYTEXT collate utf8_unicode_ci,
  `ver` INT unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`, `ver`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `unhandled` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file` TINYTEXT collate utf8_unicode_ci,
  `torrent` TINYTEXT collate utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `feed` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `url` TINYTEXT collate utf8_unicode_ci,
  `data` MEDIUMTEXT collate utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `usersetting` (
  `id` SMALLINT UNSIGNED NOT NULL,
  `filter` TINYTEXT collate utf8_unicode_ci,
  `timelimit` DATETIME,
  PRIMARY KEY (`id`),
  FOREIGN KEY (`id`) references `user`(`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `seek` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` TINYTEXT collate utf8_unicode_ci NOT NULL,
  `year` SMALLINT,
  `url` TINYTEXT collate utf8_unicode_ci,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `fsdata` (
  `filesystem` TEXT collate utf8_unicode_ci NOT NULL,
  `date` DATETIME NOT NULL,
  `free` BIGINT NOT NULL,
  `total` BIGINT NOT NULL,
  `used` BIGINT NOT NULL,
  INDEX (`filesystem`(8))
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;