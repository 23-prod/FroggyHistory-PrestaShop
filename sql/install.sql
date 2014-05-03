CREATE TABLE IF NOT EXISTS `@PREFIX@fhy_action` (
  `id_fhy_action` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_fhy_action`)
) ENGINE=@ENGINE@ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `@PREFIX@fhy_action` (`id_fhy_action`, `name`)
VALUES  (1, "ADD"), (2, "UPDATE"), (3, "DELETE"), (4, "DUPLICATE");

CREATE TABLE IF NOT EXISTS `@PREFIX@fhy_log` (
  `id_fhy_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  `id_fhy_action` int(10) unsigned NOT NULL,
  `admin_object` varchar(64) NOT NULL,
  `object` varchar(64) NOT NULL,
  `id_object` int(10) unsigned NOT NULL,
  `module` varchar(64) NOT NULL,
  `diff` text NOT NULL,
  `ip` varchar(32) NOT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_fhy_log`),
  KEY `id_employee` (`id_employee`),
  KEY `id_shop` (`id_shop`),
  KEY `object` (`object`),
  KEY `admin_object` (`admin_object`),
  KEY `id_object` (`id_object`),
  KEY `module` (`module`)
) ENGINE=@ENGINE@ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `@PREFIX@fhy_object_log` (
  `id_fhy_object_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_object` int(10) unsigned NOT NULL,
  `object` varchar(64) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY (`id_fhy_object_log`)
) ENGINE=@ENGINE@ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `@PREFIX@fhy_connection_log` (
  `id_fhy_connection_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  `browser` varchar(255) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_fhy_connection_log`),
  KEY `id_shop` (`id_shop`),
  KEY `id_employee` (`id_employee`)
) ENGINE=@ENGINE@ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;