-- --------------------------------------------------------
-- Host:                         localhost
-- Wersja serwera:               5.5.54 - MySQL Community Server (GPL)
-- Serwer OS:                    Linux
-- HeidiSQL Wersja:              9.5.0.5196
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych information_schema

-- Zrzut struktury bazy danych mjamroz
CREATE DATABASE IF NOT EXISTS `mjamroz` /*!40100 DEFAULT CHARACTER SET utf8 */;
USE `mjamroz`;

-- Zrzut struktury tabela mjamroz.companies
CREATE TABLE IF NOT EXISTS `companies` (
  `id_company` int(11) NOT NULL AUTO_INCREMENT,
  `phone` varchar(15) DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `street` varchar(150) DEFAULT NULL,
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(70) DEFAULT NULL,
  `id_user` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_company`),
  KEY `companies_users_fk` (`id_user`),
  CONSTRAINT `companies_users_fk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Zrzut struktury widok mjamroz.daily_working_time
-- Tworzenie tymczasowej tabeli aby przezwyciężyć błędy z zależnościami w WIDOKU
CREATE TABLE `daily_working_time` (
	`id_employee` INT(11) NOT NULL,
	`id_company` INT(11) NOT NULL,
	`first_name` VARCHAR(60) NULL COLLATE 'utf8_general_ci',
	`surname` VARCHAR(70) NULL COLLATE 'utf8_general_ci',
	`d_seconds` DECIMAL(31,0) NULL,
	`entry_day` DATE NULL
) ENGINE=MyISAM;

-- Zrzut struktury widok mjamroz.days_of_work
-- Tworzenie tymczasowej tabeli aby przezwyciężyć błędy z zależnościami w WIDOKU
CREATE TABLE `days_of_work` (
	`id_employee` INT(11) NOT NULL,
	`first_name` VARCHAR(60) NULL COLLATE 'utf8_general_ci',
	`surname` VARCHAR(70) NULL COLLATE 'utf8_general_ci',
	`id_company` INT(11) NOT NULL,
	`work_days` BIGINT(21) NOT NULL
) ENGINE=MyISAM;

-- Zrzut struktury funkcja mjamroz.duration
DELIMITER //
CREATE DEFINER=`mjamroz`@`%` FUNCTION `duration`(
	`id_en` int(11)
) RETURNS time
begin
	declare start_date DATETIME;
	declare diff TIME;
	declare id_emp INT(11);
	select entry_date into @start_date from entries where id_entry = id_en;
	select id_employee into @id_emp from entries where id_entry = id_en;
	select timediff(entry_date, @start_date) into @diff from entries where id_employee=@id_emp and isexit=1 and entry_date>@start_date order by entry_date limit 1;
	return (select @diff);
end//
DELIMITER ;

-- Zrzut struktury tabela mjamroz.employees
CREATE TABLE IF NOT EXISTS `employees` (
  `id_employee` int(11) NOT NULL AUTO_INCREMENT,
  `first_name` varchar(60) DEFAULT NULL,
  `surname` varchar(70) DEFAULT NULL,
  `id_profession` int(11) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `street` varchar(150) DEFAULT NULL COMMENT 'first line of address',
  `postal_code` varchar(10) DEFAULT NULL,
  `city` varchar(70) DEFAULT NULL,
  `salary_standard` double DEFAULT NULL,
  `salary_overtime` double DEFAULT NULL,
  `id_user` int(11) NOT NULL,
  PRIMARY KEY (`id_employee`),
  KEY `employees_professions_fk` (`id_profession`),
  KEY `employees_users_fk` (`id_user`),
  CONSTRAINT `employees_professions_fk` FOREIGN KEY (`id_profession`) REFERENCES `professions` (`id_profession`),
  CONSTRAINT `employees_users_fk` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Zrzut struktury tabela mjamroz.entries
CREATE TABLE IF NOT EXISTS `entries` (
  `id_entry` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entry_date` datetime NOT NULL,
  `id_employee` int(11) NOT NULL,
  `isexit` tinyint(1) NOT NULL COMMENT '0 - entry, 1 - exit',
  PRIMARY KEY (`id_entry`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=utf8
/*!50100 PARTITION BY HASH (`id_entry`)
PARTITIONS 7 */;

-- Data exporting was unselected.
-- Zrzut struktury procedura mjamroz.find
DELIMITER //
CREATE DEFINER=`mjamroz`@`%` PROCEDURE `find`(IN query varchar(70), IN p_idc int(11))
begin
	declare id_l varchar(60);
	select e.id_employee, e.first_name, e.surname, p.title from employees e, professions p where e.id_profession=p.id_profession and p.id_company=p_idc and (e.first_name like query or e.surname like query or p.title like query);
end//
DELIMITER ;

-- Zrzut struktury tabela mjamroz.professions
CREATE TABLE IF NOT EXISTS `professions` (
  `id_profession` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `id_company` int(11) NOT NULL,
  PRIMARY KEY (`id_profession`),
  KEY `professions_companies_fk` (`id_company`),
  CONSTRAINT `professions_companies_fk` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id_company`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Zrzut struktury zdarzenie mjamroz.resetAttempts
DELIMITER //
CREATE DEFINER=`mjamroz`@`%` EVENT `resetAttempts` ON SCHEDULE EVERY 1 HOUR STARTS '2019-02-11 17:15:41' ON COMPLETION NOT PRESERVE ENABLE DO update users set attempts=0//
DELIMITER ;

-- Zrzut struktury tabela mjamroz.users
CREATE TABLE IF NOT EXISTS `users` (
  `email` varchar(255) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `attempts` smallint(5) unsigned DEFAULT '0',
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8;

-- Data exporting was unselected.
-- Zrzut struktury widok mjamroz.working_time
-- Tworzenie tymczasowej tabeli aby przezwyciężyć błędy z zależnościami w WIDOKU
CREATE TABLE `working_time` (
	`id_employee` INT(11) NOT NULL,
	`d_seconds` DECIMAL(31,0) NULL,
	`entry_day` DATE NULL
) ENGINE=MyISAM;

-- Zrzut struktury widok mjamroz.daily_working_time
-- Usuwanie tabeli tymczasowej i tworzenie ostatecznej struktury WIDOKU
DROP TABLE IF EXISTS `daily_working_time`;
CREATE ALGORITHM=UNDEFINED DEFINER=`mjamroz`@`%` SQL SECURITY DEFINER VIEW `daily_working_time` AS select `e`.`id_employee` AS `id_employee`,`p`.`id_company` AS `id_company`,`e`.`first_name` AS `first_name`,`e`.`surname` AS `surname`,`t1`.`d_seconds` AS `d_seconds`,`t1`.`entry_day` AS `entry_day` from ((`employees` `e` join `professions` `p`) join `working_time` `t1`) where ((`e`.`id_employee` = `t1`.`id_employee`) and (`e`.`id_profession` = `p`.`id_profession`));

-- Zrzut struktury widok mjamroz.days_of_work
-- Usuwanie tabeli tymczasowej i tworzenie ostatecznej struktury WIDOKU
DROP TABLE IF EXISTS `days_of_work`;
CREATE ALGORITHM=UNDEFINED DEFINER=`mjamroz`@`%` SQL SECURITY DEFINER VIEW `days_of_work` AS select `daily_working_time`.`id_employee` AS `id_employee`,`daily_working_time`.`first_name` AS `first_name`,`daily_working_time`.`surname` AS `surname`,`daily_working_time`.`id_company` AS `id_company`,count(`daily_working_time`.`entry_day`) AS `work_days` from `daily_working_time` group by `daily_working_time`.`id_employee`;

-- Zrzut struktury widok mjamroz.working_time
-- Usuwanie tabeli tymczasowej i tworzenie ostatecznej struktury WIDOKU
DROP TABLE IF EXISTS `working_time`;
CREATE ALGORITHM=UNDEFINED DEFINER=`mjamroz`@`%` SQL SECURITY DEFINER VIEW `working_time` AS select `entries`.`id_employee` AS `id_employee`,sum(time_to_sec(`duration`(`entries`.`id_entry`))) AS `d_seconds`,cast(`entries`.`entry_date` as date) AS `entry_day` from `entries` where (`entries`.`isexit` = 0) group by `entries`.`id_employee`,cast(`entries`.`entry_date` as date);

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IF(@OLD_FOREIGN_KEY_CHECKS IS NULL, 1, @OLD_FOREIGN_KEY_CHECKS) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
