-- phpMyAdmin SQL Dump
-- version 2.11.5
-- http://www.phpmyadmin.net
--
-- 主机: localhost
-- 生成日期: 2011 年 02 月 10 日 14:42
-- 服务器版本: 5.0.51
-- PHP 版本: 5.2.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- 数据库: `lhc`
--

-- --------------------------------------------------------

--
-- 表的结构 `tbl_marquee`
--

DROP TABLE IF EXISTS `tbl_marquee`;
CREATE TABLE IF NOT EXISTS `tbl_marquee` (
  `id` int(11) NOT NULL auto_increment,
  `grants` smallint(6) NOT NULL,
  `message` text NOT NULL,
  `showLogon` tinyint(4) NOT NULL default '0',
  `showMar` tinyint(4) NOT NULL default '1',
  `updatedTime` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `tbl_marquee`
--


-- --------------------------------------------------------

--
-- 表的结构 `tbl_user`
--

DROP TABLE IF EXISTS `tbl_user`;
CREATE TABLE IF NOT EXISTS `tbl_user` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL COMMENT '名称',
  `creditSum` int(11) NOT NULL COMMENT '信用额度',
  `maxProrate` decimal(3,0) NOT NULL COMMENT '最大占成',
  `partner` tinyint(4) NOT NULL default '0' COMMENT '上级账目 0-禁止',
  `bh` tinyint(4) NOT NULL COMMENT '补货 0-禁止',
  `leis` smallint(6) NOT NULL COMMENT '盘权',
  `parentId` mediumint(9) NOT NULL,
  `isLeaf` tinyint(4) NOT NULL default '0',
  `role` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL default '0' COMMENT '0-正常,1-暂停,2-废弃',
  `count2` mediumint(9) NOT NULL default '0' COMMENT '股东数',
  `createdTime` datetime NOT NULL,
  `lastLoginTime` datetime NOT NULL,
  `lastLoginIP` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- 导出表中的数据 `tbl_user`
--

