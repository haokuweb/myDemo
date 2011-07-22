/*
SQLyog Community Edition- MySQL GUI v7.11 
MySQL - 5.0.45-community-nt : Database - lhc
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

CREATE DATABASE /*!32312 IF NOT EXISTS*/`lhc` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `lhc`;

/*Table structure for table `authassignment` */

DROP TABLE IF EXISTS `authassignment`;

CREATE TABLE `authassignment` (
  `itemname` varchar(64) NOT NULL,
  `userid` varchar(64) NOT NULL,
  `bizrule` text,
  `data` text,
  PRIMARY KEY  (`itemname`,`userid`),
  CONSTRAINT `authassignment_ibfk_1` FOREIGN KEY (`itemname`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `authassignment` */

insert  into `authassignment`(`itemname`,`userid`,`bizrule`,`data`) values ('admin','1',NULL,'N;');

/*Table structure for table `authitem` */

DROP TABLE IF EXISTS `authitem`;

CREATE TABLE `authitem` (
  `name` varchar(64) NOT NULL,
  `type` int(11) NOT NULL,
  `description` text,
  `bizrule` text,
  `data` text,
  PRIMARY KEY  (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `authitem` */

insert  into `authitem`(`name`,`type`,`description`,`bizrule`,`data`) values ('addAdmin',0,'addAdmin',NULL,'N;'),('addUser',0,'addUser',NULL,'N;'),('admin',2,'',NULL,'N;'),('agent',2,'',NULL,'N;'),('monitor',0,'monitor',NULL,'N;');

/*Table structure for table `authitemchild` */

DROP TABLE IF EXISTS `authitemchild`;

CREATE TABLE `authitemchild` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL,
  PRIMARY KEY  (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `authitemchild_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `authitemchild_ibfk_2` FOREIGN KEY (`child`) REFERENCES `authitem` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `authitemchild` */

insert  into `authitemchild`(`parent`,`child`) values ('admin','addAdmin'),('agent','addUser'),('admin','agent'),('agent','monitor');

/*Table structure for table `tbl_default_rate` */

DROP TABLE IF EXISTS `tbl_default_rate`;

CREATE TABLE `tbl_default_rate` (
  `sid` mediumint(9) NOT NULL auto_increment,
  `id` varchar(10) NOT NULL default '' COMMENT 'tm_0',
  `name` varchar(20) NOT NULL COMMENT '特码A',
  `rate00` decimal(3,1) NOT NULL default '0.0' COMMENT '42.3',
  `rate01` decimal(3,1) NOT NULL default '0.0',
  `rate02` decimal(3,1) NOT NULL default '0.0',
  `rate10` decimal(3,1) NOT NULL default '0.0',
  `rate11` decimal(3,1) NOT NULL default '0.0',
  `rate12` decimal(3,1) NOT NULL default '0.0',
  PRIMARY KEY  (`sid`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_default_rate` */

insert  into `tbl_default_rate`(`sid`,`id`,`name`,`rate00`,`rate01`,`rate02`,`rate10`,`rate11`,`rate12`) values (1,'tm_0','特码A','42.3','41.8','41.3','0.0','0.0','0.0'),(2,'tm_1','特码B','47.2','46.7','46.2','0.0','0.0','0.0'),(3,'tmsm','特码两面','1.9','1.9','1.9','0.0','0.0','0.0'),(4,'tmsb_0','特红波','2.8','2.8','2.8','0.0','0.0','0.0'),(5,'tmsb_1','特蓝波','2.9','2.9','2.9','0.0','0.0','0.0'),(6,'tmsb_2','特绿波','2.9','2.9','2.9','0.0','0.0','0.0'),(7,'zm_0','正码A','7.2','6.9','6.6','0.0','0.0','0.0'),(8,'zm_1','正码B','7.7','7.1','6.9','0.0','0.0','0.0'),(9,'zmsm','正码两面','1.9','1.9','1.9','0.0','0.0','0.0'),(10,'zt','正特','42.0','41.5','41.0','0.0','0.0','0.0'),(11,'ztsm','正特两面','1.9','1.9','1.9','0.0','0.0','0.0'),(12,'ztsb_0','正特红波','2.7','2.7','2.7','0.0','0.0','0.0'),(13,'ztsb_1','正特蓝波','2.8','2.8','2.8','0.0','0.0','0.0'),(14,'ztsb_2','正特绿波','2.8','2.8','2.8','0.0','0.0','0.0'),(15,'lm_0','三中二','21.0','21.0','21.0','90.0','90.0','90.0'),(16,'lm_1','三全中','99.9','99.9','99.9','0.0','0.0','0.0'),(17,'lm_2','二全中','64.0','64.0','64.0','0.0','0.0','0.0'),(18,'lm_3','二中特','32.0','32.0','32.0','50.0','50.0','50.0'),(19,'lm_4','特串','99.9','99.9','99.9','0.0','0.0','0.0'),(20,'txbx','本命特肖','9.2','9.2','9.2','0.0','0.0','0.0'),(21,'txfbx','非本命特肖','11.2','11.2','11.2','0.0','0.0','0.0'),(22,'bbds_0','半波红单','5.6','5.6','5.6','0.0','0.0','0.0'),(23,'bbds_1','半波红双','5.1','5.1','5.1','0.0','0.0','0.0'),(24,'bbds_2','半波蓝单','5.6','5.6','5.6','0.0','0.0','0.0'),(25,'bbds_3','半波蓝双','5.6','5.6','5.6','0.0','0.0','0.0'),(26,'bbds_4','半波绿单','5.6','5.6','5.6','0.0','0.0','0.0'),(27,'bbds_5','半波绿双','6.5','6.5','6.5','0.0','0.0','0.0'),(28,'bbdx_0','半波红大','6.5','6.5','6.5','0.0','0.0','0.0'),(29,'bbdx_1','半波红小','4.5','4.5','4.5','0.0','0.0','0.0'),(30,'bbdx_2','半波蓝大','5.1','5.1','5.1','0.0','0.0','0.0'),(31,'bbdx_3','半波蓝小','6.5','6.5','6.5','0.0','0.0','0.0'),(32,'bbdx_4','半波绿大','5.6','5.6','5.6','0.0','0.0','0.0'),(33,'bbdx_5','半波绿小','6.5','6.5','6.5','0.0','0.0','0.0'),(34,'mx_0','六肖','1.9','1.9','1.9','0.0','0.0','0.0'),(35,'sxbx','本命生肖','1.8','1.8','1.8','0.0','0.0','0.0'),(36,'sxfbx','非本命生肖','2.1','2.1','2.1','0.0','0.0','0.0'),(37,'ws0','0尾','2.0','2.0','2.0','0.0','0.0','0.0'),(38,'wsf0','非0尾','1.8','1.8','1.8','0.0','0.0','0.0'),(39,'sxl_0','二肖连-中','4.3','4.3','4.3','3.5','3.5','3.5'),(40,'sxl_1','二肖连-不中','3.0','3.0','3.0','3.5','3.5','3.5'),(41,'sxl_2','三肖连-中','11.0','11.0','11.0','8.5','8.5','8.5'),(42,'sxl_3','三肖连-不中','7.2','7.2','72.0','8.2','8.2','8.2'),(43,'sxl_4','四肖连-中','33.0','33.0','33.0','25.0','25.0','25.0'),(44,'sxl_5','四肖连-不中','16.0','16.0','16.0','20.0','20.0','20.0'),(45,'wsl_0','二尾连-中','3.2','3.2','3.2','3.4','3.4','3.4'),(46,'wsl_1','二尾连-不中','4.6','4.6','4.6','3.6','3.6','3.6'),(47,'wsl_2','三尾连-中','6.6','6.6','6.6','7.2','7.2','7.2'),(48,'wsl_3','三尾连-不中','14.0','14.0','14.0','11.0','11.0','14.0'),(49,'wsl_4','四尾连-中','14.5','14.5','14.5','17.0','17.0','17.0'),(50,'wsl_5','四尾连-不中','46.0','46.0','46.0','36.0','36.0','36.0'),(51,'bz_0','五不中','2.1','2.1','2.1','0.0','0.0','0.0');

/*Table structure for table `tbl_marquee` */

DROP TABLE IF EXISTS `tbl_marquee`;

CREATE TABLE `tbl_marquee` (
  `id` int(11) NOT NULL auto_increment,
  `grants` smallint(6) NOT NULL,
  `message` text NOT NULL,
  `showLogon` tinyint(4) NOT NULL default '0',
  `showMar` tinyint(4) NOT NULL default '1',
  `updatedTime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tbl_marquee` */

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `id` varchar(20) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL COMMENT '名称',
  `creditSum` int(11) NOT NULL COMMENT '信用额度',
  `maxProrate` decimal(3,2) NOT NULL COMMENT '最大占成',
  `partner` tinyint(4) NOT NULL default '0' COMMENT '上级账目 0-禁止',
  `bh` tinyint(4) NOT NULL COMMENT '补货 0-禁止',
  `leis` smallint(6) NOT NULL COMMENT '盘权',
  `parentId` mediumint(9) NOT NULL,
  `isLeaf` tinyint(4) NOT NULL default '0',
  `role` tinyint(4) NOT NULL default '6' COMMENT '1-公司管理员,2-大股东,3,4,5,6-会员,6-操盘,63:1+2+4+8+16+32',
  `status` tinyint(4) NOT NULL default '0' COMMENT '0-正常,1-暂停,2-废弃',
  `count2` mediumint(9) NOT NULL default '0' COMMENT '股东数',
  `createdTime` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastLoginIP` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `tbl_user` */

insert  into `tbl_user`(`id`,`username`,`password`,`email`,`name`,`creditSum`,`maxProrate`,`partner`,`bh`,`leis`,`parentId`,`isLeaf`,`role`,`status`,`count2`,`createdTime`,`lastLoginTime`,`lastLoginIP`) values ('qq133','admin','96e79218965eb72c92a549dd5a330112','','',2000,'0.90',1,1,7,0,0,35,0,0,'2011-02-16 09:18:20','0000-00-00 00:00:00',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
