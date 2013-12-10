CREATE TABLE IF NOT EXISTS `@PREFIX@ghy_action` (
  `id_ghy_action` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  PRIMARY KEY (`id_ghy_action`)
) ENGINE=@ENGINE@ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;

INSERT INTO `@PREFIX@ghy_action` (`id_ghy_action`, `name`)
VALUES  (1, "ADD"), (2, "UPDATE"), (3, "DELETE"), (4, "DUPLICATE");

CREATE TABLE IF NOT EXISTS `@PREFIX@ghy_log` (
  `id_ghy_log` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_shop` int(10) unsigned NOT NULL,
  `id_employee` int(10) unsigned NOT NULL,
  `id_ghy_action` int(10) unsigned NOT NULL,
  `admin_object` varchar(64) NOT NULL,
  `object` varchar(64) NOT NULL,
  `id_object` int(10) unsigned NOT NULL,
  `module` varchar(64) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `date_add` datetime DEFAULT NULL,
  PRIMARY KEY (`id_ghy_log`),
  KEY `id_employee` (`id_employee`),
  KEY `id_shop` (`id_shop`),
  KEY `object` (`object`),
  KEY `admin_object` (`admin_object`),
  KEY `id_object` (`id_object`),
  KEY `module` (`module`)
) ENGINE=@ENGINE@ DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;
