/*
SQLyog Community v9.0 Beta1
MySQL - 5.1.36-community : Database - lhc
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
USE `lhc`;

/*Table structure for table `tbl_user` */

DROP TABLE IF EXISTS `tbl_user`;

CREATE TABLE `tbl_user` (
  `id` varchar(20) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL COMMENT '名称',
  `creditSum` int(11) NOT NULL COMMENT '信用额度',
  `usedSum` int(11) NOT NULL DEFAULT '0',
  `maxProrate` decimal(3,2) NOT NULL DEFAULT '0.00' COMMENT '最大占成',
  `parentProrate` decimal(3,2) DEFAULT '0.00' COMMENT '上级占成,总代理最大占成+ 股东占成 < 股东最大占成，新建个帐号指定他的最大占成和上级对他的占成',
  `parent_0` varchar(20) DEFAULT NULL COMMENT '公司',
  `parent_1` varchar(20) DEFAULT NULL COMMENT '大股东',
  `parent_2` varchar(20) DEFAULT NULL COMMENT '股东',
  `parent_3` varchar(20) DEFAULT NULL COMMENT '大代理',
  `parent_4` varchar(20) DEFAULT NULL COMMENT '代理',
  `partner` tinyint(4) NOT NULL DEFAULT '0' COMMENT '上级账目 0-禁止',
  `bh` tinyint(4) NOT NULL COMMENT '补货 0-禁止',
  `lei` smallint(6) DEFAULT '0',
  `leis` smallint(6) NOT NULL COMMENT '盘权',
  `parentId` varchar(20) NOT NULL,
  `isLeaf` tinyint(4) NOT NULL DEFAULT '0',
  `role` tinyint(4) NOT NULL DEFAULT '5' COMMENT '0-公司管理员,1-大股东,2,3,4,5-会员',
  `status` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-正常,1-暂停,2-废弃',
  `count2` mediumint(9) NOT NULL DEFAULT '0' COMMENT '股东数',
  `createdTime` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastLoginIP` int(11) NOT NULL,
  `level` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0-default,1-操盘手,2-子账号',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

/*Data for the table `tbl_user` */

insert  into `tbl_user`(`id`,`username`,`password`,`email`,`name`,`creditSum`,`usedSum`,`maxProrate`,`parentProrate`,`parent_0`,`parent_1`,`parent_2`,`parent_3`,`parent_4`,`partner`,`bh`,`lei`,`leis`,`parentId`,`isLeaf`,`role`,`status`,`count2`,`createdTime`,`lastLoginTime`,`lastLoginIP`,`level`) values ('qq133','admin','e10adc3949ba59abbe56e057f20f883e','','',2000000000,0,'1.00','0.00',NULL,NULL,NULL,NULL,NULL,1,1,0,7,'',0,0,0,0,'2011-02-16 09:18:20','0000-00-00 00:00:00',0,0),('xcf6','xcf6name','e10adc3949ba59abbe56e057f20f883e','','',5000,500,'0.00','0.20','qq133','xcf1','xcf2','xcf3','xcf4',0,2,0,1,'xcf4',1,5,0,0,'2011-03-06 13:14:02','0000-00-00 00:00:00',0,0),('xcf1','xcf1大股东','e10adc3949ba59abbe56e057f20f883e','','',10000,0,'0.95','0.05','qq133',NULL,NULL,NULL,NULL,0,2,0,7,'qq133',0,1,0,0,'2011-03-06 13:14:02','0000-00-00 00:00:00',0,0),('xcf2','xcf2股东','e10adc3949ba59abbe56e057f20f883e','','',9000,0,'0.75','0.20','qq133','xcf1',NULL,NULL,NULL,0,2,0,7,'xcf1',0,2,0,0,'2011-03-06 13:14:02','0000-00-00 00:00:00',0,0),('xcf3','xcf3大代理','e10adc3949ba59abbe56e057f20f883e','','',8000,0,'0.55','0.20','qq133','xcf1','xcf2',NULL,NULL,1,1,0,7,'xcf2',0,3,0,0,'2011-02-16 09:18:20','0000-00-00 00:00:00',0,0),('xcf4','xcf4代理','e10adc3949ba59abbe56e057f20f883e','','',7000,0,'0.50','0.05','qq133','xcf1','xcf2','xcf3',NULL,1,1,0,7,'xcf3',0,4,0,0,'2011-02-16 09:18:20','0000-00-00 00:00:00',0,0),('xcf5','xcf5name','e10adc3949ba59abbe56e057f20f883e','','',2000,0,'0.00','0.20','qq133','xcf1','xcf2','xcf3','xcf4',1,1,1,2,'xcf4',0,5,0,0,'2011-02-16 09:18:20','0000-00-00 00:00:00',0,0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


SELECT t.id,t.username,t.role,t.creditSum,t.parentProrate,t.maxProrate,t.parentId,p.username,
(SELECT COUNT(*) FROM tbl_user c2 WHERE c2.parent_3=t.id AND c2.role=2) AS count2, 
(SELECT COUNT(*) FROM tbl_user c3 WHERE c3.parent_3=t.id AND c3.role=3) AS count3, 
(SELECT COUNT(*) FROM tbl_user c4 WHERE c4.parent_3=t.id AND c4.role=4) AS count4, 
(SELECT COUNT(*) FROM tbl_user c5 WHERE c5.parent_3=t.id AND c5.role=5) AS count5, 
t.partner,t.bh,t.leis,t.`status`,
p.leis AS parentLeis,p.maxProrate AS parentMaxProrate,
(SELECT MAX(c.maxProrate) FROM tbl_user c WHERE c.parentId=t.id) AS maxChildProrate
FROM tbl_user t LEFT JOIN tbl_user p ON p.id=t.parentId
WHERE t.role=3 AND t.`status`=0 AND t.parent_0='qq133';
