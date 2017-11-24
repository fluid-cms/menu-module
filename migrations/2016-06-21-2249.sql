DROP TABLE IF EXISTS `menu_item`;
CREATE TABLE IF NOT EXISTS `menu_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `menu_id` int(11) NOT NULL,
  `label` varchar(25) COLLATE utf8_czech_ci NOT NULL,
  `url` varchar(255) COLLATE utf8_czech_ci NOT NULL,
  `params` varchar(255) COLLATE utf8_czech_ci DEFAULT '{}',
  `main` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `menu_id` (`menu_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

DROP TABLE IF EXISTS `menu_menus`;
CREATE TABLE IF NOT EXISTS `menu_menus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) COLLATE utf8_czech_ci NOT NULL,
  `name` varchar(20) COLLATE utf8_czech_ci NOT NULL,
  `class_parent` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `class_child` varchar(100) COLLATE utf8_czech_ci DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

ALTER TABLE `menu_item`
  ADD CONSTRAINT `menu_item_ibfk_1` FOREIGN KEY (`menu_id`) REFERENCES `menu_menus` (`id`) ON DELETE CASCADE;