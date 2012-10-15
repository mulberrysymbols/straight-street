-- phpMyAdmin SQL Dump
-- version 2.9.1.1-Debian-13
-- http://www.phpmyadmin.net
-- 
-- Host: localhost
-- Generation Time: Oct 14, 2012 at 10:47 PM
-- Server version: 5.0.32
-- PHP Version: 5.2.0-8+etch7
-- 
-- Database: `straight_street`
-- 
CREATE DATABASE `straight_street` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci;
USE `straight_street`;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_api_log`
-- 

CREATE TABLE `t_api_log` (
  `clientip` varchar(15) collate utf8_unicode_ci NOT NULL,
  `appid` varchar(10) collate utf8_unicode_ci NOT NULL,
  `count` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`clientip`,`appid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_app`
-- 

CREATE TABLE `t_app` (
  `id` int(3) NOT NULL auto_increment,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `brief` varchar(200) collate utf8_unicode_ci NOT NULL,
  `info` text collate utf8_unicode_ci NOT NULL,
  `status` int(1) default '1',
  `showfirst` int(1) NOT NULL default '0',
  `features` text collate utf8_unicode_ci,
  `sysreq` text collate utf8_unicode_ci,
  `other` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=18 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_authority`
-- 

CREATE TABLE `t_authority` (
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `id` char(1) collate utf8_unicode_ci NOT NULL,
  `display_ord` int(11) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_authority_function`
-- 

CREATE TABLE `t_authority_function` (
  `id` int(7) NOT NULL auto_increment,
  `auth_id` char(1) collate utf8_unicode_ci NOT NULL,
  `func_id` int(7) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `auth_id_func_id` (`auth_id`,`func_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=19 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_bundle_version`
-- 

CREATE TABLE `t_bundle_version` (
  `lang_id` char(2) collate utf8_unicode_ci NOT NULL,
  `version` varchar(5) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`lang_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_downloads`
-- 

CREATE TABLE `t_downloads` (
  `id` int(7) NOT NULL auto_increment,
  `userid` int(7) NOT NULL,
  `file` varchar(50) collate utf8_unicode_ci NOT NULL,
  `when` datetime NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6409 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_function`
-- 

CREATE TABLE `t_function` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_language`
-- 

CREATE TABLE `t_language` (
  `id` char(2) collate utf8_unicode_ci NOT NULL,
  `name` varchar(50) collate utf8_unicode_ci NOT NULL,
  `native_name` varchar(50) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_lic`
-- 

CREATE TABLE `t_lic` (
  `id` int(7) NOT NULL auto_increment,
  `long_caption` varchar(100) collate utf8_unicode_ci NOT NULL,
  `brief` text collate utf8_unicode_ci,
  `caption` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media`
-- 

CREATE TABLE `t_media` (
  `id` int(7) NOT NULL auto_increment,
  `mtype` int(1) NOT NULL,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `rated` tinyint(1) NOT NULL default '0',
  `Licid` int(7) NOT NULL,
  `sponid` int(3) NOT NULL default '0',
  `creation_date` date NOT NULL,
  `original_name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `category_id` int(7) NOT NULL,
  `designers_ref_id` int(7) NOT NULL,
  `author_id` int(7) NOT NULL,
  `wordlist_id` int(7) NOT NULL,
  `status_id` int(7) NOT NULL,
  `finishing_pool_id` int(7) default NULL,
  `finisher_id` int(7) default NULL,
  `version_id` int(7) default NULL,
  `comment` varchar(300) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`),
  KEY `sponid` (`sponid`),
  KEY `Licid` (`Licid`),
  KEY `category_id` (`category_id`),
  KEY `designers_ref_id` (`designers_ref_id`),
  KEY `author_id` (`author_id`),
  KEY `wordlist_id` (`wordlist_id`),
  KEY `status_id` (`status_id`),
  KEY `finishing_pool_id` (`finishing_pool_id`),
  KEY `finisher_id` (`finisher_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4001 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_category`
-- 

CREATE TABLE `t_media_category` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=161 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_designers_ref`
-- 

CREATE TABLE `t_media_designers_ref` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=218 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_finishing_pool`
-- 

CREATE TABLE `t_media_finishing_pool` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=89 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_grammar`
-- 

CREATE TABLE `t_media_grammar` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  `view_order` int(7) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=15 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_path`
-- 

CREATE TABLE `t_media_path` (
  `id` int(10) NOT NULL auto_increment,
  `mid` int(7) NOT NULL,
  `type` int(1) NOT NULL default '0',
  `filename` varchar(104) collate utf8_unicode_ci NOT NULL,
  `basename` varchar(100) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mid_type` (`mid`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11338 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_path_backup`
-- 

CREATE TABLE `t_media_path_backup` (
  `id` int(10) NOT NULL auto_increment,
  `mid` int(7) NOT NULL,
  `type` int(1) NOT NULL default '0',
  `filename` varchar(36) collate utf8_unicode_ci NOT NULL,
  `basename` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mid_type` (`mid`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3382 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_path_old`
-- 

CREATE TABLE `t_media_path_old` (
  `id` int(10) NOT NULL auto_increment,
  `mid` int(7) NOT NULL,
  `type` int(1) NOT NULL default '0',
  `filename` varchar(36) collate utf8_unicode_ci NOT NULL,
  `basename` varchar(40) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `mid_type` (`mid`,`type`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4174 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_person`
-- 

CREATE TABLE `t_media_person` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_status`
-- 

CREATE TABLE `t_media_status` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(20) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_status_transitions`
-- 

CREATE TABLE `t_media_status_transitions` (
  `status` varchar(11) collate utf8_unicode_ci NOT NULL,
  `Dev` tinyint(1) NOT NULL default '0',
  `Uploaded` tinyint(1) NOT NULL default '0',
  `Review` tinyint(1) NOT NULL default '0',
  `Rejected` tinyint(1) NOT NULL default '0',
  `Live` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_tags`
-- 

CREATE TABLE `t_media_tags` (
  `id` int(7) NOT NULL auto_increment,
  `mid` int(7) NOT NULL default '0',
  `tid` int(7) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `mid` (`mid`,`tid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4895 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_type`
-- 

CREATE TABLE `t_media_type` (
  `id` int(7) NOT NULL auto_increment,
  `caption` char(30) collate utf8_unicode_ci NOT NULL,
  `iconpath` char(100) collate utf8_unicode_ci default NULL,
  `brief` varchar(100) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_version`
-- 

CREATE TABLE `t_media_version` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=20 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_vocab`
-- 

CREATE TABLE `t_media_vocab` (
  `l_id` char(2) collate utf8_unicode_ci NOT NULL,
  `m_id` int(7) NOT NULL,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `g_id` int(7) NOT NULL,
  `synonym` tinyint(1) NOT NULL,
  PRIMARY KEY  (`l_id`,`m_id`,`name`),
  KEY `m_id` (`m_id`),
  KEY `g_id` (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_media_wordlist`
-- 

CREATE TABLE `t_media_wordlist` (
  `id` int(7) NOT NULL auto_increment,
  `name` varchar(30) collate utf8_unicode_ci NOT NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=33 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_review`
-- 

CREATE TABLE `t_review` (
  `id` int(3) NOT NULL auto_increment,
  `name` varchar(36) collate utf8_unicode_ci default NULL,
  `status` int(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=50 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_review_dataset`
-- 

CREATE TABLE `t_review_dataset` (
  `id` int(6) NOT NULL auto_increment,
  `rid` int(3) default NULL,
  `userid` int(7) default NULL,
  `status` int(1) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=161 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_review_media`
-- 

CREATE TABLE `t_review_media` (
  `id` int(4) NOT NULL auto_increment,
  `rid` int(3) NOT NULL default '0',
  `mid` int(7) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `mid` (`mid`),
  KEY `rid` (`rid`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=4248 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_review_results`
-- 

CREATE TABLE `t_review_results` (
  `id` int(6) NOT NULL auto_increment,
  `rdsid` int(4) default NULL,
  `rmid` int(3) default NULL,
  `decline` int(1) default NULL,
  `comments` varchar(500) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1110 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_sponsor`
-- 

CREATE TABLE `t_sponsor` (
  `id` int(7) NOT NULL auto_increment,
  `caption` varchar(100) collate utf8_unicode_ci NOT NULL,
  `brief` text collate utf8_unicode_ci,
  `url` varchar(200) collate utf8_unicode_ci default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=12 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_tag`
-- 

CREATE TABLE `t_tag` (
  `id` int(7) NOT NULL auto_increment,
  `tag` varchar(20) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=494 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_user`
-- 

CREATE TABLE `t_user` (
  `id` int(7) NOT NULL auto_increment,
  `authcode` varchar(32) collate utf8_unicode_ci NOT NULL,
  `username` varchar(50) collate utf8_unicode_ci NOT NULL,
  `pass` varchar(32) collate utf8_unicode_ci NOT NULL,
  `datereg` datetime default NULL,
  `auth` int(1) NOT NULL default '0',
  `email` varchar(50) collate utf8_unicode_ci default NULL,
  `fname` varchar(20) collate utf8_unicode_ci default NULL,
  `sname` varchar(20) collate utf8_unicode_ci default NULL,
  `dob` datetime default NULL,
  `role` varchar(40) collate utf8_unicode_ci default NULL,
  `last_access` datetime NOT NULL default '0000-00-00 00:00:00',
  `language_id` char(2) collate utf8_unicode_ci NOT NULL default 'EN',
  `cancontact` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3024 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_user_agr_lic`
-- 

CREATE TABLE `t_user_agr_lic` (
  `id` int(9) NOT NULL auto_increment,
  `uid` int(7) NOT NULL default '0',
  `lid` int(7) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2992 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_user_authority`
-- 

CREATE TABLE `t_user_authority` (
  `id` int(7) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `authority_id` char(1) collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `usr_ud_auth_id` (`user_id`,`authority_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2981 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `t_web_app`
-- 

CREATE TABLE `t_web_app` (
  `version` varchar(10) collate utf8_unicode_ci NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

