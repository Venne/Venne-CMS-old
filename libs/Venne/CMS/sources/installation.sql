-- phpMyAdmin SQL Dump
-- version 3.4.0
-- http://www.phpmyadmin.net
--
-- Počítač: localhost
-- Vygenerováno: Úte 05. čec 2011, 17:13
-- Verze MySQL: 5.5.13
-- Verze PHP: 5.3.6

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Databáze: `venne-ng`
--

-- --------------------------------------------------------

--
-- Struktura tabulky `alias`
--

CREATE TABLE IF NOT EXISTS `alias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moduleName` varchar(255) NOT NULL,
  `moduleItemId` int(11) NOT NULL,
  `url` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `aliasKey`
--

CREATE TABLE IF NOT EXISTS `aliasKey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `alias_id` int(11) NOT NULL,
  `key` varchar(255) NOT NULL,
  `val` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `alias_id` (`alias_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `comments`
--

CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `moduleName` varchar(255) NOT NULL,
  `moduleItemId` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `commentsItem`
--

CREATE TABLE IF NOT EXISTS `commentsItem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) NOT NULL,
  `order` int(11) NOT NULL,
  `created` datetime NOT NULL,
  `author` varchar(255) NOT NULL,
  `text` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `language`
--

CREATE TABLE IF NOT EXISTS `language` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `lang` varchar(30) NOT NULL,
  `name` varchar(30) NOT NULL,
  `alias` varchar(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `language`
--

INSERT INTO `language` (`id`, `website_id`, `lang`, `name`, `alias`) VALUES
(1, -1, 'cs', 'čeština', 'cs'),
(2, -1, 'en', 'english', 'en');

-- --------------------------------------------------------

--
-- Struktura tabulky `navigation`
--

CREATE TABLE IF NOT EXISTS `navigation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `website_id` int(11) NOT NULL,
  `navigation_id` int(11) DEFAULT NULL,
  `active` smallint(1) NOT NULL,
  `name` varchar(300) NOT NULL,
  `type` varchar(300) NOT NULL,
  `order` int(11) NOT NULL,
  `moduleName` varchar(300) DEFAULT NULL,
  `moduleItemId` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`,`navigation_id`),
  KEY `navigation_id` (`navigation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `navigation`
--

INSERT INTO `navigation` (`id`, `website_id`, `navigation_id`, `active`, `name`, `type`, `order`, `moduleName`, `moduleItemId`) VALUES
(1, -1, NULL, 1, 'Navigation', 'link', 1, NULL, NULL),
(3, -1, NULL, 1, 'Pages', 'link', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Struktura tabulky `navigationKey`
--

CREATE TABLE IF NOT EXISTS `navigationKey` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `navigation_id` int(11) NOT NULL,
  `key` varchar(300) NOT NULL,
  `val` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `navigation_id` (`navigation_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `navigationKey`
--

INSERT INTO `navigationKey` (`id`, `navigation_id`, `key`, `val`) VALUES
(1, 1, 'module', 'Navigation'),
(2, 1, 'presenter', 'Default'),
(5, 3, 'module', 'Pages');

-- --------------------------------------------------------

--
-- Struktura tabulky `pages`
--

CREATE TABLE IF NOT EXISTS `pages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(300) NOT NULL,
  `url` varchar(255) NOT NULL,
  `keywords` varchar(300) NOT NULL,
  `description` varchar(300) NOT NULL,
  `text` text NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `website_id` int(11) NOT NULL,
  `mainPage` smallint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `website_id` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `permission`
--

CREATE TABLE IF NOT EXISTS `permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `allow` smallint(1) NOT NULL,
  `privilege` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `permission`
--

INSERT INTO `permission` (`id`, `resource_id`, `role_id`, `allow`, `privilege`) VALUES
(39, 18, 2, 1, ''),
(40, 19, 2, 1, '');

-- --------------------------------------------------------

--
-- Struktura tabulky `privilege`
--

CREATE TABLE IF NOT EXISTS `privilege` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `resource_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=21 ;

--
-- Vypisuji data pro tabulku `resource`
--

INSERT INTO `resource` (`id`, `resource_id`, `name`) VALUES
(1, NULL, 'adminpanel'),
(2, 1, 'administration'),
(3, 2, 'administration-websites'),
(4, 2, 'administration-modules'),
(5, 2, 'administration-security'),
(6, 2, 'administration-system'),
(7, 5, 'administration-security-users'),
(8, 5, 'administration-security-roles'),
(9, 5, 'administration-security-permissions'),
(10, 2, 'administration-navigation'),
(11, 2, 'administration-pages'),
(12, 11, 'administration-pages-edit'),
(13, 10, 'administration-navigation-edit'),
(14, 3, 'administration-websites-edit'),
(15, 7, 'administration-security-users-edit'),
(16, 8, 'administration-security-roles-edit'),
(17, 9, 'administration-security-permissions-edit'),
(18, NULL, 'module-pages'),
(19, NULL, 'element-comments'),
(20, 19, 'element-comments-edit');

-- --------------------------------------------------------

--
-- Struktura tabulky `role`
--

CREATE TABLE IF NOT EXISTS `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `role`
--

INSERT INTO `role` (`id`, `name`, `role_id`) VALUES
(1, 'admin', NULL),
(2, 'guest', NULL),
(3, 'logged', 2);

-- --------------------------------------------------------

--
-- Struktura tabulky `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `password` varchar(300) NOT NULL,
  `salt` varchar(30) NOT NULL,
  `email` varchar(300) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `users_roles`
--

CREATE TABLE IF NOT EXISTS `users_roles` (
  `user_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`role_id`),
  KEY `role_id` (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktura tabulky `website`
--

CREATE TABLE IF NOT EXISTS `website` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(300) NOT NULL,
  `regex` varchar(300) NOT NULL,
  `skin` varchar(300) NOT NULL,
  `langType` varchar(30) NOT NULL,
  `langValue` varchar(30) NOT NULL,
  `langDefault` int(11) DEFAULT NULL,
  `routePrefix` varchar(300) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `langDefault` (`langDefault`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Vypisuji data pro tabulku `website`
--

INSERT INTO `website` (`id`, `name`, `regex`, `skin`, `langType`, `langValue`, `langDefault`, `routePrefix`) VALUES
(-1, 'admin', '*', 'admin', 'get', 'lang', 1, '');

--
-- Omezení pro exportované tabulky
--

--
-- Omezení pro tabulku `aliasKey`
--
ALTER TABLE `aliasKey`
  ADD CONSTRAINT `aliasKey_ibfk_1` FOREIGN KEY (`alias_id`) REFERENCES `alias` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `language`
--
ALTER TABLE `language`
  ADD CONSTRAINT `language_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `navigation`
--
ALTER TABLE `navigation`
  ADD CONSTRAINT `navigation_ibfk_1` FOREIGN KEY (`website_id`) REFERENCES `website` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `navigation_ibfk_2` FOREIGN KEY (`navigation_id`) REFERENCES `navigation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `navigationKey`
--
ALTER TABLE `navigationKey`
  ADD CONSTRAINT `navigationKey_ibfk_1` FOREIGN KEY (`navigation_id`) REFERENCES `navigation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `permission`
--
ALTER TABLE `permission`
  ADD CONSTRAINT `permission_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `privilege`
--
ALTER TABLE `privilege`
  ADD CONSTRAINT `privilege_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `resource`
--
ALTER TABLE `resource`
  ADD CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`resource_id`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `role`
--
ALTER TABLE `role`
  ADD CONSTRAINT `role_ibfk_1` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Omezení pro tabulku `users_roles`
--
ALTER TABLE `users_roles`
  ADD CONSTRAINT `users_roles_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `users_roles_ibfk_2` FOREIGN KEY (`role_id`) REFERENCES `role` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
SET FOREIGN_KEY_CHECKS=1;
