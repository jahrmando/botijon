-- MySQL dump 10.11
--
-- Host: localhost    Database: botijon
-- ------------------------------------------------------
-- Server version	5.0.75-0ubuntu10

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;



create database if not exists `your db name here`  default character set 'utf8' default collate 'utf8_general_ci';

grant all privileges on `your db name here`.* to 'your db username here'@'localhost' identified by 'the db user password here';

flush privileges;

use `your db name here`;


--
-- Table structure for table `functions`
--


SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
CREATE TABLE  if not exists `functions` (
  `functionid` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  `version` varchar(20) default NULL,
  `functiongroup` varchar(100) default NULL,
  `signature` varchar(1000) NOT NULL,
  `description` varchar(4000) NOT NULL,
  `samplecode` varchar(4000) default NULL,
  `relatedfunctions` varchar(1000) default NULL,
  PRIMARY KEY  (`functionid`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=4213 DEFAULT CHARSET=latin1;
SET character_set_client = @saved_cs_client;



create table if not exists `chatlogins` (
	`loginid` int unsigned NOT NULL auto_increment,
	`nick` varchar(20) not null,
	`ip` varchar(15) not null,
	`logindate` timestamp NOT NULL default current_timestamp,
	`logoutdate` timestamp,
	primary key (`loginid`)
) type = innodb;

create index `idx_chatlogins_ip` on `chatlogins`(`ip`);
create index `idx_chatlogins_nick` on `chatlogins`(`nick`);



create table if not exists `chatbans` (
	`banid` int unsigned NOT NULL auto_increment,
	`ip` varchar(15),
	`mask` varchar(100),
	`nick` varchar(20),
	`bandate` timestamp default current_timestamp,
	`datebanexpires` timestamp,
	`reason` varchar(200),
	`ispermanent` tinyint unsigned default 0,
	primary key(`banid`)
) type = innodb;


create index `idx_chatbans_ip` on `chatbans`(`ip`);
create index `idx_chatbans_mask` on `chatbans`(`mask`);
create index `idx_chatbans_nick` on `chatbans`(`nick`);


create table if not exists `banhistory` (
	`banid` int unsigned NOT NULL auto_increment,
	`ip` varchar(15),
	`mask` varchar(100),
	`nick` varchar(20),
	`bandate` timestamp default current_timestamp,
	`datebanexpires` timestamp,
	`reason` varchar(200),
	`ispermanent` tinyint unsigned default 0,
	primary key(`banid`)
) type = innodb;

create index `idx_banhistory_ip` on `banhistory`(`ip`);
create index `idx_banhistory_mask` on `banhistory`(`mask`);
create index `idx_banhistory_nick` on `banhistory`(`nick`);

create table if not exists `chatflood` (
	`id` int unsigned NOT NULL auto_increment,
	`channel` varchar(30) NOT NULL,
	`nick` varchar(15) NOT NULL,
	`unixtime` int unsigned,
	primary key(`id`)
) type = innodb;

create index `idx_chatflood_unixtime` on `chatflood`(`unixtime`);
create index `idx_chatflood_nick` on `chatflood`(`nick`);

create table if not exists `chatlastseen` (
	`nick` varchar(15) NOT NULL,
	`channel` varchar(30) NOT NULL,
	`message` varchar(500) NOT NULL,
	`messagetime` timestamp NOT NULL default current_timestamp,
	primary key(`nick`, `channel`)
) type = innodb;

create table if not exists `hashes` (
	string varchar(100) NOT NULL,
	md5 varchar(32) NOT NULL,
	sha1 varchar(40) NOT NULL,
	primary key(`string`)
) CHARACTER SET utf8 COLLATE utf8_bin type = innodb;

create index `idx_hashes_md5` on `hashes`(`md5`);
create index `idx_hashes_sha1` on `hashes`(`sha1`);


