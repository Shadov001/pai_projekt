CREATE TABLE `companies` (
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
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8

CREATE TABLE `employees` (
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
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8

CREATE TABLE `entries` (
  `id_entry` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entry_date` datetime NOT NULL,
  `id_employee` int(11) NOT NULL,
  `isexit` tinyint(1) NOT NULL COMMENT '0 - entry, 1 - exit',
  PRIMARY KEY (`id_entry`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8
 PARTITION BY HASH (`id_entry`)
PARTITIONS 7

CREATE TABLE `professions` (
  `id_profession` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `id_company` int(11) NOT NULL,
  PRIMARY KEY (`id_profession`),
  KEY `professions_companies_fk` (`id_company`),
  CONSTRAINT `professions_companies_fk` FOREIGN KEY (`id_company`) REFERENCES `companies` (`id_company`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8

CREATE TABLE `users` (
  `email` varchar(255) NOT NULL,
  `password_hash` char(60) NOT NULL,
  `id_user` int(11) NOT NULL AUTO_INCREMENT,
  `attempts` smallint(5) unsigned DEFAULT 0,
  PRIMARY KEY (`id_user`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8

DELIMITER //
CREATE PROCEDURE find (IN query varchar(70), IN p_idc int(11))
begin
	declare id_l varchar(60);
	select e.id_employee, e.first_name, e.surname, p.title from employees e, professions p where e.id_profession=p.id_profession and p.id_company=p_idc and (e.first_name like query or e.surname like query or p.title like query);
end

DELIMITER //
CREATE FUNCTION duration (id_en int(11)) RETURNS TIME
begin
	declare start_date DATETIME;
	declare diff TIME;
	declare id_emp INT(11);
	select entry_date into @start_date from entries where id_entry = id_en;
	select id_employee into @id_emp from entries where id_entry = id_en;
	select timediff(entry_date, @start_date) into @diff from entries where id_employee=@id_emp and isexit=1 and entry_date>DATE '2019-02-18' order by entry_date limit 1;
	return (select @diff);
end; //
DELIMITER ;

CREATE EVENT resetAttempts
ON SCHEDULE EVERY 1 HOUR
STARTS '2019-02-11 17:15:41.000'
ON COMPLETION NOT PRESERVE
ENABLE
DO update users set attempts=0

create
or replace
view `working_time` as select
    `entries`.`id_employee` as `id_employee`,
    sum(time_to_sec(`duration`(`entries`.`id_entry`))) as `d_seconds`,
    cast(`entries`.`entry_date` as date) as `entry_day`
from
    `entries`
where
    `entries`.`isexit` = 0
group by
    `entries`.`id_employee`,
    cast(`entries`.`entry_date` as date)

create
or replace
view `daily_working_time` as select
    `e`.`id_employee` as `id_employee`,
    `p`.`id_company` as `id_company`,
    `e`.`first_name` as `first_name`,
    `e`.`surname` as `surname`,
    `t1`.`d_seconds` as `d_seconds`,
    `t1`.`entry_day` as `entry_day`
from
    ((`employees` `e`
join `professions` `p`)
join working_time `t1`)
where
    `e`.`id_employee` = `t1`.`id_employee`
    and `e`.`id_profession` = `p`.`id_profession`

create
or replace
view `days_of_work` as select
    `daily_working_time`.`id_employee` as `id_employee`,
    `daily_working_time`.`first_name` as `first_name`,
    `daily_working_time`.`surname` as `surname`,
    `daily_working_time`.`id_company` as `id_company`,
    count(`daily_working_time`.`entry_day`) as `work_days`
from
    `mjamroz`.`daily_working_time`
group by
    `daily_working_time`.`id_employee`