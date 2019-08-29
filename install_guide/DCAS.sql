# ************************************************************
# Sequel Pro SQL dump
# Version 4529
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.42)
# Database: dcas
# Generation Time: 2016-03-02 12:53:51 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table cbn_appointment
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_appointment`;

CREATE TABLE `cbn_appointment` (
  `appointment_id` int(11) NOT NULL AUTO_INCREMENT,
  `dentist_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT '',
  `appointment_date` date DEFAULT NULL,
  `appointment_time` varchar(100) DEFAULT NULL,
  `start` varchar(255) NOT NULL DEFAULT '',
  `end` varchar(255) NOT NULL DEFAULT '',
  `notes` text,
  `notes_dentist` text,
  `cancelled` int(1) NOT NULL DEFAULT '0',
  `finished` int(1) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  PRIMARY KEY (`appointment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table cbn_audit
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_audit`;

CREATE TABLE `cbn_audit` (
  `audit_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created_date` datetime DEFAULT NULL,
  `created_user` int(11) DEFAULT NULL,
  `content_audit` text,
  PRIMARY KEY (`audit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cbn_audit` WRITE;
/*!40000 ALTER TABLE `cbn_audit` DISABLE KEYS */;

INSERT INTO `cbn_audit` (`audit_id`, `created_date`, `created_user`, `content_audit`)
VALUES
	(1,'2016-03-02 19:49:07',1,'Demo Was Successfully logged in From : ::1'),
	(2,'2016-03-02 19:53:37',1,'Demo Was Successfully logged out From : ::1');

/*!40000 ALTER TABLE `cbn_audit` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_browser_statistics
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_browser_statistics`;

CREATE TABLE `cbn_browser_statistics` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `browser` varchar(200) DEFAULT NULL,
  `total` varchar(100) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cbn_browser_statistics` WRITE;
/*!40000 ALTER TABLE `cbn_browser_statistics` DISABLE KEYS */;

INSERT INTO `cbn_browser_statistics` (`id`, `browser`, `total`)
VALUES
	(1,'Google Chrome','23'),
	(2,'Mozilla Firefox','14'),
	(3,'Internet Explorer','1'),
	(4,'Apple Safari','7'),
	(5,'Opera','2'),
	(6,'Netscape','1'),
	(7,'Microsoft Edge','1'),
	(8,'Unknown Browser','1');

/*!40000 ALTER TABLE `cbn_browser_statistics` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_dentists
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_dentists`;

CREATE TABLE `cbn_dentists` (
  `dentist_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `user_email` varchar(64) NOT NULL DEFAULT '',
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `address` text NOT NULL,
  `bio` text NOT NULL,
  `avatar` text NOT NULL,
  `specialties` varchar(100) NOT NULL,
  `timetable` text,
  `phone` varchar(30) NOT NULL,
  `cellphone` varchar(30) NOT NULL,
  `gender` int(1) NOT NULL,
  `birthdate` date NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `can_login` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`dentist_id`),
  UNIQUE KEY `user_name` (`user_name`,`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cbn_dentists` WRITE;
/*!40000 ALTER TABLE `cbn_dentists` DISABLE KEYS */;

INSERT INTO `cbn_dentists` (`dentist_id`, `user_name`, `password`, `user_email`, `first_name`, `last_name`, `address`, `bio`, `avatar`, `specialties`, `timetable`, `phone`, `cellphone`, `gender`, `birthdate`, `created`, `updated`, `can_login`)
VALUES
	(8,'','','greg_white79@gmail.com','Greg','White','Bandung, West Java, ID','Bio about Greg White','','Endodontics','{\"sunday\":\"Appointment Only\",\"monday\":{\"start\":\"09:00\",\"end\":\"15:00\"},\"tuesday\":{\"start\":\"15:00\",\"end\":\"19:00\"},\"wednesday\":{\"start\":\"14:00\",\"end\":\"20:00\"},\"thursday\":{\"start\":\"10:00\",\"end\":\"14:00\"},\"friday\":\"Appointment Only\",\"saturday\":\"Appointment Only\"}','','',2,'1979-03-31','2016-03-02 19:52:10',NULL,0);

/*!40000 ALTER TABLE `cbn_dentists` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_mailbox
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_mailbox`;

CREATE TABLE `cbn_mailbox` (
  `mailbox_id` int(11) NOT NULL AUTO_INCREMENT,
  `sender_id` int(11) NOT NULL DEFAULT '0',
  `receiver_id` int(5) NOT NULL DEFAULT '0',
  `mail_title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `mail_content` text CHARACTER SET utf8 COLLATE utf8_bin,
  `sent_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_read` int(1) NOT NULL DEFAULT '0',
  `is_archived` int(1) NOT NULL DEFAULT '0',
  `is_deleted` int(1) NOT NULL DEFAULT '0',
  `deleted_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `last_updated_date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`mailbox_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table cbn_patients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_patients`;

CREATE TABLE `cbn_patients` (
  `patient_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_name` varchar(64) NOT NULL,
  `password` varchar(255) NOT NULL DEFAULT '',
  `user_email` varchar(64) NOT NULL DEFAULT '',
  `first_name` varchar(30) NOT NULL,
  `last_name` varchar(30) NOT NULL,
  `avatar` text,
  `address` text NOT NULL,
  `phone` varchar(30) NOT NULL,
  `cellphone` varchar(30) NOT NULL,
  `gender` int(1) NOT NULL,
  `bio` text,
  `birthdate` date NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime DEFAULT NULL,
  `can_login` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`patient_id`),
  UNIQUE KEY `user_name` (`user_name`,`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cbn_patients` WRITE;
/*!40000 ALTER TABLE `cbn_patients` DISABLE KEYS */;

INSERT INTO `cbn_patients` (`patient_id`, `user_name`, `password`, `user_email`, `first_name`, `last_name`, `avatar`, `address`, `phone`, `cellphone`, `gender`, `bio`, `birthdate`, `created`, `updated`, `can_login`)
VALUES
	(17,'','','jane@doe.com','Jane','Doe',NULL,'Bandung, West Java, ID','','',1,'Bio about Jane Doe','1987-01-16','2016-03-02 19:50:02',NULL,0);

/*!40000 ALTER TABLE `cbn_patients` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_roles`;

CREATE TABLE `cbn_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL,
  `role_desc` varchar(255) NOT NULL,
  `role_permission` text,
  `register_default` int(1) NOT NULL DEFAULT '0',
  `dentist_default` int(1) NOT NULL DEFAULT '0',
  `patient_default` int(1) NOT NULL DEFAULT '0',
  `admin_default` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

LOCK TABLES `cbn_roles` WRITE;
/*!40000 ALTER TABLE `cbn_roles` DISABLE KEYS */;

INSERT INTO `cbn_roles` (`role_id`, `role_name`, `role_desc`, `role_permission`, `register_default`, `dentist_default`, `patient_default`, `admin_default`)
VALUES
	(1,'Super Admin','Super Admin','{\"dashboard\":\"1\",\"patient\":\"1\",\"dentist\":\"1\",\"appointment\":\"1\",\"global_settings\":\"1\",\"user_managers\":\"1\",\"audit_trails\":\"1\"}',0,0,0,1),
	(2,'Normal Admin','Normal Admin','{\"dashboard\":\"1\",\"patient\":\"0\",\"dentist\":\"0\",\"global_settings\":\"0\",\"user_managers\":\"1\",\"audit_trails\":\"0\"}',0,0,0,1),
	(5,'Dentist','Dentist Permissions','{\"dashboard\":\"1\",\"patient\":\"0\",\"dentist\":\"0\",\"appointment\":\"1\",\"global_settings\":\"0\",\"user_managers\":\"0\",\"audit_trails\":\"0\"}',0,1,0,0),
	(7,'Guest','Guest','{\"dashboard\":\"1\",\"global_settings\":\"0\",\"user_managers\":\"0\",\"audit_trails\":\"0\"}',1,0,0,0),
	(8,'Patient','Patient Permissions','{\"dashboard\":\"1\",\"global_settings\":\"0\",\"user_managers\":\"0\",\"audit_trails\":\"0\"}',0,0,1,0),
	(9,'Custom Role','Custom Role','{\"dashboard\":\"1\",\"global_settings\":\"0\",\"user_managers\":\"0\",\"audit_trails\":\"1\"}',0,0,0,0),
	(10,'Receptionist','Receptionist','{\"dashboard\":\"1\",\"patient\":\"0\",\"appointment\":\"1\",\"dentist\":\"0\",\"global_settings\":\"0\",\"user_managers\":\"0\",\"audit_trails\":\"0\"}',0,0,0,0);

/*!40000 ALTER TABLE `cbn_roles` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_settings`;

CREATE TABLE `cbn_settings` (
  `id` int(3) unsigned NOT NULL,
  `site_url` varchar(100) NOT NULL,
  `site_name` varchar(255) NOT NULL,
  `site_title` varchar(255) DEFAULT NULL,
  `meta_desc` text,
  `meta_key` varchar(255) DEFAULT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_address` longtext NOT NULL,
  `owner_email` varchar(255) NOT NULL,
  `owner_phone` varchar(255) NOT NULL,
  `upload_path` varchar(255) NOT NULL,
  `files_allowed` varchar(255) NOT NULL,
  `app_logo` varchar(255) DEFAULT NULL,
  `app_favicon` varchar(255) DEFAULT NULL,
  `mail_engine` varchar(255) NOT NULL,
  `mail_used_smtp` int(1) NOT NULL,
  `mail_smtp_host` varchar(255) DEFAULT NULL,
  `mail_smtp_auth` varchar(255) DEFAULT NULL,
  `mail_smtp_username` varchar(255) DEFAULT NULL,
  `mail_smtp_password` varchar(255) DEFAULT NULL,
  `mail_smtp_port` varchar(255) DEFAULT NULL,
  `mail_smtp_encryption` varchar(255) DEFAULT NULL,
  `mail_sendgrid_api` varchar(255) DEFAULT NULL,
  `last_update_date` datetime NOT NULL,
  `last_update_user` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `cbn_settings` WRITE;
/*!40000 ALTER TABLE `cbn_settings` DISABLE KEYS */;

INSERT INTO `cbn_settings` (`id`, `site_url`, `site_name`, `site_title`, `meta_desc`, `meta_key`, `owner_name`, `owner_address`, `owner_email`, `owner_phone`, `upload_path`, `files_allowed`, `app_logo`, `app_favicon`, `mail_engine`, `mail_used_smtp`, `mail_smtp_host`, `mail_smtp_auth`, `mail_smtp_username`, `mail_smtp_password`, `mail_smtp_port`, `mail_smtp_encryption`, `mail_sendgrid_api`, `last_update_date`, `last_update_user`)
VALUES
	(1,'http://cbn-demo.tk/dcas/','DCAS','Dental Clinic Appointment System','DCAS','it, visual communication, dental clinic, appointment, dentistry, cms, system','comestoarralabs','Bandung, West Java, Indonesia','comestoarralabs@gmail.com','','public/logo/','image/png,image/gif,image/jpeg,image/jpg,image/x-icon','logo_562bb0f35dbe8.png','favicon_56c30c05e7d7b.ico','PHP Mailer',1,'mailtrap.io','true','512829f5d84dcc596','2b6e100cdbb801','2525','tls','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','2016-03-02 18:45:34','1');

/*!40000 ALTER TABLE `cbn_settings` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_user_profile
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_user_profile`;

CREATE TABLE `cbn_user_profile` (
  `user_id` int(11) NOT NULL DEFAULT '0',
  `first_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `birth_date` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `phone` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `bio` text COLLATE utf8_unicode_ci,
  `address` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`user_id`),
  CONSTRAINT `cbn_user_profile_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `cbn_users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

LOCK TABLES `cbn_user_profile` WRITE;
/*!40000 ALTER TABLE `cbn_user_profile` DISABLE KEYS */;

INSERT INTO `cbn_user_profile` (`user_id`, `first_name`, `last_name`, `birth_date`, `phone`, `bio`, `address`)
VALUES
	(1,'Comestoarra','Labs','11-12-1987','(+62) 022 2506902','Make IT Easier','Bandung');

/*!40000 ALTER TABLE `cbn_user_profile` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table cbn_users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `cbn_users`;

CREATE TABLE `cbn_users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `session_id` varchar(48) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'stores session cookie id to prevent session concurrency',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `user_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s activation status',
  `user_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s deletion status',
  `user_account_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'user''s account type (basic, premium, etc)',
  `user_has_avatar` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if user has a local avatar, 0 if not',
  `user_remember_me_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s remember-me cookie token',
  `user_creation_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the creation of user''s account',
  `user_suspension_timestamp` bigint(20) DEFAULT NULL COMMENT 'Timestamp till the end of a user suspension',
  `user_last_login_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of user''s last login',
  `user_failed_logins` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s failed login attempts',
  `user_last_failed_login` varchar(100) DEFAULT NULL COMMENT 'unix timestamp of last failed login attempt',
  `user_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s email verification hash string',
  `user_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password reset code',
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
  `user_provider_type` text COLLATE utf8_unicode_ci,
  `role_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';

LOCK TABLES `cbn_users` WRITE;
/*!40000 ALTER TABLE `cbn_users` DISABLE KEYS */;

INSERT INTO `cbn_users` (`user_id`, `session_id`, `user_name`, `user_password_hash`, `user_email`, `user_active`, `user_deleted`, `user_account_type`, `user_has_avatar`, `user_remember_me_token`, `user_creation_timestamp`, `user_suspension_timestamp`, `user_last_login_timestamp`, `user_failed_logins`, `user_last_failed_login`, `user_activation_hash`, `user_password_reset_hash`, `user_password_reset_timestamp`, `user_provider_type`, `role_id`)
VALUES
	(1,NULL,'demo','$2y$10$OvprunjvKOOhM1h9bzMPs.vuwGIsOqZbw88rzSyGCTJTcE61g5WXi','labs@comestoarra.com',1,0,7,1,NULL,1422205178,NULL,1456922947,0,NULL,NULL,NULL,NULL,'DEFAULT',1);

/*!40000 ALTER TABLE `cbn_users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
