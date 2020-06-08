-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `albums`;
CREATE TABLE `albums` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `label` tinytext COLLATE utf8_slovak_ci NOT NULL,
                          `is_present` tinyint(4) NOT NULL DEFAULT '1',
                          `thumbnail` varchar(255) COLLATE utf8_slovak_ci DEFAULT 'logo.svg',
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `answers`;
CREATE TABLE `answers` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `question_id` int(11) NOT NULL,
                           `label` tinytext COLLATE utf8_slovak_ci NOT NULL,
                           `correct` tinyint(4) NOT NULL DEFAULT '0',
                           `is_present` tinyint(4) NOT NULL DEFAULT '1',
                           `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                           PRIMARY KEY (`id`),
                           KEY `question_id` (`question_id`),
                           CONSTRAINT `answers_ibfk_1` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `application_forms`;
CREATE TABLE `application_forms` (
                                     `id` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `title_bn` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                                     `title_an` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
                                     `street_address` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `city` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
                                     `zipcode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
                                     `birthdate` date NOT NULL,
                                     `birthplace` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `id_number` varchar(15) COLLATE utf8_unicode_ci NOT NULL,
                                     `nationality` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                                     `email` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                                     `phone` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
                                     `employment` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `prev_course` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                                     `consent_personal_data` tinyint(2) NOT NULL,
                                     `consent_photo` tinyint(2) NOT NULL,
                                     `courses_levels` int(11) NOT NULL,
                                     `status` enum('pending','cancelled','finished','archived') COLLATE utf8_unicode_ci DEFAULT NULL,
                                     KEY `courses_levels` (`courses_levels`),
                                     CONSTRAINT `application_forms_ibfk_1` FOREIGN KEY (`courses_levels`) REFERENCES `courses_levels` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `contents`;
CREATE TABLE `contents` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `section_id` int(11) NOT NULL,
                            `title` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL,
                            `text` text COLLATE utf8_slovak_ci,
                            `type` enum('file','image','text','video') COLLATE utf8_slovak_ci NOT NULL,
                            `priority` tinyint(4) NOT NULL DEFAULT '50',
                            `is_present` tinyint(4) NOT NULL DEFAULT '1',
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`),
                            KEY `section_id` (`section_id`),
                            CONSTRAINT `contents_ibfk_1` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `courses`;
CREATE TABLE `courses` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `name` varchar(255) CHARACTER SET utf8 COLLATE utf8_slovak_ci NOT NULL,
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `courses_levels`;
CREATE TABLE `courses_levels` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `course_id` int(11) NOT NULL,
                                  `course_level_id` int(11) NOT NULL,
                                  PRIMARY KEY (`id`),
                                  KEY `course_id` (`course_id`),
                                  KEY `course_level_id` (`course_level_id`),
                                  CONSTRAINT `courses_levels_ibfk_1` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE NO ACTION,
                                  CONSTRAINT `courses_levels_ibfk_2` FOREIGN KEY (`course_level_id`) REFERENCES `course_levels` (`id`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `course_levels`;
CREATE TABLE `course_levels` (
                                 `id` int(11) NOT NULL AUTO_INCREMENT,
                                 `label` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
                                 PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `images`;
CREATE TABLE `images` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `album_id` int(11) NOT NULL,
                          `name` tinytext COLLATE utf8_slovak_ci NOT NULL,
                          `is_present` tinyint(4) NOT NULL DEFAULT '1',
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`),
                          KEY `album_id` (`album_id`),
                          CONSTRAINT `images_ibfk_1` FOREIGN KEY (`album_id`) REFERENCES `albums` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `levels`;
CREATE TABLE `levels` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `label` tinytext COLLATE utf8_slovak_ci NOT NULL,
                          `is_present` tinyint(4) NOT NULL DEFAULT '1',
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `levels_results`;
CREATE TABLE `levels_results` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `result_id` int(11) NOT NULL,
                                  `level_id` int(11) DEFAULT NULL,
                                  `score` int(11) NOT NULL,
                                  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                                  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                  `is_present` tinyint(4) DEFAULT '1',
                                  PRIMARY KEY (`id`),
                                  KEY `result_id` (`result_id`),
                                  KEY `level_id` (`level_id`),
                                  CONSTRAINT `levels_results_ibfk_1` FOREIGN KEY (`result_id`) REFERENCES `results` (`id`),
                                  CONSTRAINT `levels_results_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `notices`;
CREATE TABLE `notices` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `title` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
                           `content` text COLLATE utf8_slovak_ci,
                           `type` varchar(50) COLLATE utf8_slovak_ci NOT NULL DEFAULT 'success',
                           `on_homepage` tinyint(4) NOT NULL DEFAULT '0',
                           `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                           `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                           `is_present` tinyint(4) NOT NULL DEFAULT '1',
                           PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `questions`;
CREATE TABLE `questions` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `test_id` int(11) NOT NULL,
                             `level_id` int(11) DEFAULT '1',
                             `label` tinytext COLLATE utf8_slovak_ci NOT NULL,
                             `value` tinyint(4) NOT NULL DEFAULT '1',
                             `is_present` tinyint(4) NOT NULL DEFAULT '1',
                             `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                             `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                             PRIMARY KEY (`id`),
                             KEY `test_id` (`test_id`),
                             KEY `part_id` (`level_id`),
                             CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`),
                             CONSTRAINT `questions_ibfk_2` FOREIGN KEY (`level_id`) REFERENCES `levels` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `results`;
CREATE TABLE `results` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `test_id` int(11) NOT NULL,
                           `email` tinytext COLLATE utf8_slovak_ci NOT NULL,
                           `score` int(11) NOT NULL,
                           `is_present` tinyint(4) NOT NULL DEFAULT '1',
                           PRIMARY KEY (`id`),
                           KEY `test_id` (`test_id`),
                           CONSTRAINT `results_ibfk_1` FOREIGN KEY (`test_id`) REFERENCES `tests` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `sections`;
CREATE TABLE `sections` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `section_id` int(10) unsigned DEFAULT NULL,
                            `name` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
                            `url` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL,
                            `order` tinyint(4) NOT NULL DEFAULT '0',
                            `sliding` tinyint(4) NOT NULL DEFAULT '0',
                            `visible` tinyint(4) NOT NULL DEFAULT '1',
                            `home_url` tinyint(4) NOT NULL DEFAULT '0',
                            `on_homepage` tinyint(4) NOT NULL DEFAULT '1',
                            `is_present` tinyint(4) NOT NULL DEFAULT '1',
                            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `slides`;
CREATE TABLE `slides` (
                          `id` int(11) NOT NULL AUTO_INCREMENT,
                          `title` varchar(255) COLLATE utf8_slovak_ci NOT NULL,
                          `message` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL,
                          `img` varchar(255) COLLATE utf8_slovak_ci NOT NULL DEFAULT 'logo.svg',
                          `link` varchar(255) COLLATE utf8_slovak_ci DEFAULT NULL,
                          `is_present` tinyint(4) NOT NULL DEFAULT '1',
                          `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                          `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                          PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `tests`;
CREATE TABLE `tests` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `label` tinytext COLLATE utf8_slovak_ci NOT NULL,
                         `high_score` tinyint(4) NOT NULL DEFAULT '0',
                         `is_present` tinyint(4) NOT NULL DEFAULT '1',
                         `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `username` varchar(50) COLLATE utf8_slovak_ci NOT NULL,
                         `password` varchar(100) COLLATE utf8_slovak_ci NOT NULL,
                         `role` enum('admin','user','powerUser') COLLATE utf8_slovak_ci NOT NULL,
                         `is_present` tinyint(4) NOT NULL DEFAULT '1',
                         `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                         `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_slovak_ci;


-- 2020-06-08 11:49:49nature