/*
天天团购数据库结构
*/

DROP TABLE IF EXISTS `{prefix}system_failedlogins`;

CREATE TABLE `{prefix}system_failedlogins` (
 `ip` char(15) NOT NULL default '',
 `count` tinyint(1) unsigned NOT NULL default '0',
 `lastupdate` int(10) unsigned NOT NULL default '0',
 PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_log`;

CREATE TABLE `{prefix}system_log` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `uid` int(10) unsigned NOT NULL default '0',
 `username` char(15) NOT NULL default '游客',
 `action_id` smallint(4) unsigned NOT NULL default '0',
 `module` char(50) NOT NULL default 'index',
 `action` char(100) NOT NULL default '',
 `item_id` int(10) unsigned NOT NULL default '0',
 `item_title` char(100) NOT NULL default '',
 `ip` char(15) NOT NULL default '',
 `time` int(10) unsigned NOT NULL default '0',
 `uri` char(100) NOT NULL default '',
 `extcredits1` smallint(4) NOT NULL default '0',
 `extcredits2` smallint(4) NOT NULL default '0',
 `extcredits3` smallint(4) NOT NULL default '0',
 PRIMARY KEY (`id`),
 KEY `action_id` (`action_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_memberfields`;

CREATE TABLE `{prefix}system_memberfields` (
 `uid` mediumint(8) unsigned NOT NULL default '0',
 `nickname` varchar(30) NOT NULL default '',
 `site` varchar(75) NOT NULL default '',
 `alipay` varchar(50) NOT NULL default '',
 `icq` varchar(12) NOT NULL default '',
 `yahoo` varchar(40) NOT NULL default '',
 `taobao` varchar(40) NOT NULL default '',
 `location` varchar(30) NOT NULL default '',
 `customstatus` varchar(30) NOT NULL default '',
 `medals` varchar(255) NOT NULL default '',
 `avatar` varchar(255) NOT NULL default '',
 `avatarwidth` tinyint(1) unsigned NOT NULL default '0',
 `avatarheight` tinyint(1) unsigned NOT NULL default '0',
 `bio` text NOT NULL,
 `signature` text NOT NULL,
 `sightml` text NOT NULL,
 `ignorepm` text NOT NULL,
 `groupterms` text NOT NULL,
 `authstr` varchar(50) NOT NULL default '',
 `auth_try_times` TINYINT(1) UNSIGNED NOT NULL,
 `question` varchar(255) NOT NULL default '',
 `answer` varchar(255) NOT NULL default '',
 `address` varchar(40) NOT NULL default '',
 `postcode` varchar(6) NOT NULL default '',
 `validate_true_name` varchar(50) NOT NULL default '',
 `validate_card_type` varchar(10) NOT NULL default '',
 `validate_card_id` varchar(50) NOT NULL default '',
 `validate_remark` varchar(100) NOT NULL default '',
 PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_members`;

CREATE TABLE `{prefix}system_members` (
 `uid` int(10) NOT NULL AUTO_INCREMENT,
 `username` varchar(45) NOT NULL,
 `password` varchar(32) NOT NULL DEFAULT '',
 `secques` varchar(24) NOT NULL,
 `gender` tinyint(1) NOT NULL DEFAULT '0',
 `adminid` tinyint(1) NOT NULL DEFAULT '0',
 `regip` varchar(45) NOT NULL,
 `regdate` int(10) NOT NULL DEFAULT '0',
 `lastip` varchar(45) NOT NULL,
 `lastvisit` int(10) NOT NULL DEFAULT '0',
 `lastactivity` int(10) NOT NULL DEFAULT '0',
 `lastpost` int(10) NOT NULL DEFAULT '0',
 `oltime` int(10) NOT NULL DEFAULT '0',
 `pageviews` int(10) NOT NULL DEFAULT '0',
 `credits` int(10) NOT NULL DEFAULT '0',
 `extcredits1` int(10) NOT NULL DEFAULT '0',
 `extcredits2` int(10) NOT NULL DEFAULT '0',
 `email` varchar(150) NOT NULL,
 `bday` date NOT NULL DEFAULT '0000-00-00',
 `sigstatus` tinyint(1) NOT NULL DEFAULT '0',
 `tpp` tinyint(1) NOT NULL DEFAULT '0',
 `ppp` tinyint(1) NOT NULL DEFAULT '0',
 `styleid` int(10) NOT NULL DEFAULT '0',
 `dateformat` varchar(30) NOT NULL,
 `timeformat` tinyint(1) NOT NULL DEFAULT '0',
 `pmsound` tinyint(1) NOT NULL DEFAULT '0',
 `showemail` tinyint(1) DEFAULT '0',
 `newsletter` tinyint(1) NOT NULL DEFAULT '0',
 `invisible` tinyint(1) NOT NULL DEFAULT '0',
 `timeoffset` varchar(12) NOT NULL DEFAULT '',
 `newpm` tinyint(1) NOT NULL DEFAULT '0',
 `accessmasks` tinyint(1) NOT NULL DEFAULT '0',
 `face` varchar(180) NOT NULL DEFAULT '',
 `tag_count` int(10) NOT NULL DEFAULT '0',
 `role_id` tinyint(1) NOT NULL DEFAULT '0',
 `role_type` varchar(18) NOT NULL DEFAULT 'normal',
 `new_msg_count` tinyint(1) NOT NULL DEFAULT '0',
 `tag` varchar(255) NOT NULL DEFAULT '',
 `own_tags` int(10) NOT NULL DEFAULT '0',
 `login_count` int(10) NOT NULL DEFAULT '0',
 `truename` varchar(48) NOT NULL DEFAULT '',
 `phone` varchar(45) NOT NULL DEFAULT '',
 `phone_validate` tinyint(1) unsigned NOT NULL,
 `last_year_rank` int(10) NOT NULL DEFAULT '0',
 `last_month_rank` int(10) NOT NULL DEFAULT '0',
 `last_week_rank` int(10) NOT NULL DEFAULT '0',
 `this_year_rank` int(10) NOT NULL DEFAULT '0',
 `this_month_rank` int(10) NOT NULL DEFAULT '0',
 `this_week_rank` int(10) NOT NULL DEFAULT '0',
 `last_year_credit` int(10) NOT NULL DEFAULT '0',
 `last_month_credit` int(10) NOT NULL DEFAULT '0',
 `last_week_credit` int(10) NOT NULL DEFAULT '0',
 `this_year_credit` int(10) NOT NULL DEFAULT '0',
 `this_month_credit` int(10) NOT NULL DEFAULT '0',
 `this_week_credit` int(10) NOT NULL DEFAULT '0',
 `view_times` int(10) NOT NULL DEFAULT '0',
 `use_tag_count` int(10) NOT NULL DEFAULT '0',
 `create_tag_count` int(10) NOT NULL DEFAULT '0',
 `image_count` int(10) NOT NULL DEFAULT '0',
 `noticenum` int(10) NOT NULL DEFAULT '0',
 `ucuid` int(10) NOT NULL DEFAULT '0',
 `invite_count` int(10) NOT NULL DEFAULT '0',
 `invitecode` varchar(48) NOT NULL DEFAULT '',
 `province` varchar(48) NOT NULL DEFAULT '',
 `city` varchar(48) NOT NULL DEFAULT '',
 `topic_count` int(10) NOT NULL DEFAULT '0',
 `at_count` int(10) NOT NULL DEFAULT '0',
 `follow_count` int(10) NOT NULL DEFAULT '0',
 `fans_count` int(10) NOT NULL DEFAULT '0',
 `email2` varchar(150) NOT NULL DEFAULT '',
 `qq` varchar(30) NOT NULL DEFAULT '',
 `msn` varchar(150) NOT NULL DEFAULT '',
 `aboutme` varchar(255) NOT NULL DEFAULT '',
 `at_new` int(10) NOT NULL DEFAULT '0',
 `comment_new` int(10) NOT NULL DEFAULT '0',
 `fans_new` int(10) NOT NULL DEFAULT '0',
 `topic_favorite_count` int(10) NOT NULL DEFAULT '0',
 `tag_favorite_count` int(10) NOT NULL DEFAULT '0',
 `disallow_beiguanzhu` tinyint(1) NOT NULL DEFAULT '0',
 `validate` tinyint(1) NOT NULL DEFAULT '0',
 `favoritemy_new` int(10) NOT NULL DEFAULT '0',
 `money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `forbid_money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `checked` tinyint(1) NOT NULL DEFAULT '0',
 `finder` int(10) NOT NULL DEFAULT '0',
 `findtime` int(10) NOT NULL DEFAULT '0',
 `totalpay` decimal(10,2) DEFAULT '0.00',
 `home_uid` int(10) unsigned NOT NULL DEFAULT '0',
 `buy_pre` decimal(6,2) NOT NULL DEFAULT '0.00',
 `sell_pre` decimal(6,2) NOT NULL DEFAULT '0.00',
 `salary_number` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
 `total_cost` decimal(10,2) NOT NULL DEFAULT '0.00',
 `scores` int(10) NOT NULL DEFAULT '0',
 `privs` text,
 `area` smallint(6) NOT NULL DEFAULT '0',
 `city_place_region` int(10) NOT NULL DEFAULT '0',
 `get_share_rebate` tinyint(2) NOT NULL DEFAULT '0' comment '返利权限 1开启 0关闭',
 PRIMARY KEY (`uid`),
 KEY `username` (`username`),
 KEY `email` (`email`),
 KEY `email2` (`email2`),
 KEY `phone` (`phone`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}system_onlinetime`;

CREATE TABLE `{prefix}system_onlinetime` (
 `uid` mediumint(8) unsigned NOT NULL default '0',
 `thismonth` smallint(4) unsigned NOT NULL default '0',
 `total` mediumint(8) unsigned NOT NULL default '0',
 `lastupdate` int(10) unsigned NOT NULL default '0',
 PRIMARY KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_report`;

CREATE TABLE `{prefix}system_report` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `uid` mediumint(8) NOT NULL default '0',
 `username` char(15) NOT NULL default '',
 `ip` char(15) NOT NULL default '',
 `type` tinyint(1) NOT NULL default '0',
 `reason` tinyint(1) NOT NULL default '0',
 `content` text NOT NULL,
 `url` text NOT NULL,
 `dateline` int(10) NOT NULL default '0',
 `process_user` char(15) NOT NULL default '',
 `process_time` int(10) NOT NULL default '0',
 `process_result` tinyint(1) NOT NULL default '0',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_robot`;

CREATE TABLE `{prefix}system_robot` (
 `name` char(50) NOT NULL default '',
 `times` int(10) unsigned NOT NULL default '0',
 `first_visit` int(10) NOT NULL default '0',
 `last_visit` int(10) NOT NULL default '0',
 `agent` char(255) NOT NULL default '',
 `disallow` tinyint(1) NOT NULL default '0',
 PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_robot_ip`;

CREATE TABLE `{prefix}system_robot_ip` (
 `ip` char(15) NOT NULL default '',
 `name` char(50) NOT NULL default '',
 `times` int(10) unsigned NOT NULL default '0',
 `first_visit` int(10) NOT NULL default '0',
 `last_visit` int(10) NOT NULL default '0',
 PRIMARY KEY (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_robot_log`;

CREATE TABLE `{prefix}system_robot_log` (
 `name` char(50) NOT NULL default '',
 `date` date NOT NULL default '0000-00-00',
 `times` int(10) unsigned NOT NULL default '0',
 `first_visit` int(10) unsigned NOT NULL default '0',
 `last_visit` int(10) unsigned NOT NULL default '0',
 UNIQUE KEY `name` (`name`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_role`;

CREATE TABLE `{prefix}system_role` (
 `id` tinyint(1) unsigned NOT NULL auto_increment,
 `name` varchar(50) NOT NULL default '',
 `privs` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}system_sessions`;

CREATE TABLE `{prefix}system_sessions` (
 `sid` char(6) NOT NULL default '',
 `ip1` tinyint(1) unsigned NOT NULL default '0',
 `ip2` tinyint(1) unsigned NOT NULL default '0',
 `ip3` tinyint(1) unsigned NOT NULL default '0',
 `ip4` tinyint(1) unsigned NOT NULL default '0',
 `uid` mediumint(8) unsigned NOT NULL default '0',
 `username` varchar(100) NOT NULL default '',
 `groupid` smallint(4) unsigned NOT NULL default '0',
 `styleid` smallint(4) unsigned NOT NULL default '0',
 `invisible` tinyint(1) NOT NULL default '0',
 `action` tinyint(1) unsigned NOT NULL default '0',
 `lastactivity` int(10) unsigned NOT NULL default '0',
 `lastolupdate` int(10) unsigned NOT NULL default '0',
 `pageviews` smallint(4) unsigned NOT NULL default '0',
 `seccode` mediumint(6) unsigned NOT NULL default '0',
 `fid` smallint(4) unsigned NOT NULL default '0',
 `tid` mediumint(8) unsigned NOT NULL default '0',
 `bloguid` mediumint(8) unsigned NOT NULL default '0',
 UNIQUE KEY `sid` (`sid`),
 KEY `uid` (`uid`),
 KEY `bloguid` (`bloguid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_address`;

CREATE TABLE `{prefix}tttuangou_address` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `owner` int(10) unsigned NOT NULL,
 `name` varchar(18) NOT NULL,
 `region` varchar(18) NOT NULL,
 `address` text NOT NULL,
 `zip` varchar(6) default NULL,
 `phone` varchar(12) NOT NULL,
 `lastuse` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `owner` (`owner`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_api_apps`;

CREATE TABLE `{prefix}tttuangou_api_apps` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `appcode` CHAR(32) NOT NULL,
 `protocol` CHAR(32) NOT NULL,
 `name` CHAR(32) NOT NULL,
 `description` CHAR(128) NOT NULL,
 `seckey` CHAR(255) NOT NULL,
 `enabled` ENUM('true','false') NOT NULL DEFAULT 'false',
 `timestamp_update` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE INDEX `appcode` (`appcode`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_api_protocol`;

CREATE TABLE `{prefix}tttuangou_api_protocol` (
 `channel` ENUM('system','client') NOT NULL,
 `sign` CHAR(32) NOT NULL,
 `appcode` CHAR(32) NOT NULL,
 `uri` CHAR(128) NOT NULL,
 `login` ENUM('yes','no') NOT NULL DEFAULT 'yes',
 `private` ENUM('yes','no') NOT NULL DEFAULT 'yes',
 `fields` TEXT NOT NULL,
 `timestamp_update` INT(10) UNSIGNED NOT NULL,
 UNIQUE INDEX `channel_sign_appcode_uri` (`channel`, `sign`, `appcode`, `uri`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_api_session`;

CREATE TABLE `{prefix}tttuangou_api_session` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `appcode` CHAR(32) NOT NULL,
 `token` CHAR(32) NOT NULL,
 `user_id` INT(10) UNSIGNED NOT NULL,
 `total_request` INT(10) UNSIGNED NOT NULL,
 `timestamp_request` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE INDEX `appcode_token` (`appcode`, `token`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_article`;

CREATE TABLE `{prefix}tttuangou_article` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `author_id` INT(10) UNSIGNED NOT NULL,
 `title` VARCHAR(256) NOT NULL,
 `content` TEXT NOT NULL,
 `writer` VARCHAR(32) NOT NULL,
 `timestamp_create` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_attrs`;

CREATE TABLE `{prefix}tttuangou_attrs` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `cat_id` INT(10) UNSIGNED NOT NULL,
 `name` VARCHAR(128) NOT NULL,
 `price_moves` DECIMAL(10,2) NOT NULL,
 `binding` ENUM('true','false') NOT NULL,
 PRIMARY KEY (`id`),
 INDEX `cat_id` (`cat_id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_attrs_cat`;

CREATE TABLE `{prefix}tttuangou_attrs_cat` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `product_id` INT(10) UNSIGNED NOT NULL,
 `name` VARCHAR(128) NOT NULL,
 `required` ENUM('true','false') NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_attrs_order`;

CREATE TABLE `{prefix}tttuangou_attrs_order` (
 `sign` BIGINT(11) UNSIGNED NOT NULL,
 `price` DECIMAL(10,2) NOT NULL,
 `data` TEXT NOT NULL,
 UNIQUE INDEX `sign` (`sign`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_catalog`;

CREATE TABLE `{prefix}tttuangou_catalog` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `parent` int(10) unsigned NOT NULL DEFAULT '0',
 `name` varchar(32) NOT NULL,
 `flag` varchar(32) NOT NULL,
 `oslcount` int(10) unsigned NOT NULL DEFAULT '0',
 `procount` int(10) unsigned NOT NULL DEFAULT '0',
 `upstime` int(10) unsigned NOT NULL,
 `order` tinyint(2) unsigned NOT NULL DEFAULT '0',
 `hot` tinyint(1) unsigned NOT NULL,
 `fontcolor` varchar(20) NOT NULL,
 `hotorder` tinyint(2) unsigned NOT NULL,
 `index_display_count` int(10) NOT NULL DEFAULT '6',
 PRIMARY KEY (`id`),
 UNIQUE KEY `flag` (`flag`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_city`;

CREATE TABLE `{prefix}tttuangou_city` (
 `cityid` int(10) NOT NULL auto_increment,
 `cityname` varchar(50) NOT NULL default '',
 `shorthand` varchar(20) NOT NULL default '',
 `display` tinyint(1) default '0',
 UNIQUE KEY `cityid` (`cityid`),
 KEY `display` (`display`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_city_place`;

CREATE TABLE `{prefix}tttuangou_city_place` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `type` ENUM('region','street') NOT NULL,
 `parent_type` ENUM('city','region') NOT NULL,
 `parent_id` INT(10) UNSIGNED NOT NULL,
 `name` CHAR(32) NOT NULL,
 `timestamp_update` INT(10) UNSIGNED NOT NULL,
 `hot` tinyint(1) unsigned NOT NULL,
 `fontcolor` char(20) NOT NULL,
 `hotorder` tinyint(2) unsigned NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_comments`;

CREATE TABLE `{prefix}tttuangou_comments` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `product_id` INT(10) UNSIGNED NOT NULL,
 `order_id` BIGINT(10) UNSIGNED NOT NULL,
 `user_id` INT(10) UNSIGNED NOT NULL,
 `user_name` CHAR(32) NOT NULL,
 `score` TINYINT(1) UNSIGNED NOT NULL DEFAULT '5',
 `content` TEXT NOT NULL,
 `img` CHAR(200) NOT NULL DEFAULT '',
 `reply` TEXT NOT NULL,
 `toped` TINYINT(3) UNSIGNED NOT NULL DEFAULT '0',
 `status` ENUM('auditing','approved','denied') NOT NULL DEFAULT 'auditing',
 `timestamp_update` INT(10) UNSIGNED NOT NULL,
 `anonymous` tinyint(1) NOT NULL default '0',
 PRIMARY KEY (`id`),
 INDEX `product_id` (`product_id`),
 KEY `toped` (`toped`),
 KEY `timestamp_update` (`timestamp_update`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_express`;

CREATE TABLE `{prefix}tttuangou_express` (
 `id` int(11) NOT NULL auto_increment,
 `name` varchar(120) NOT NULL,
 `express` smallint(5) unsigned NOT NULL default '0',
 `firstunit` int(10) unsigned NOT NULL default '1000',
 `firstprice` decimal(10,2) unsigned NOT NULL default '10.00',
 `continueunit` int(10) unsigned NOT NULL default '1000',
 `continueprice` decimal(10,2) unsigned NOT NULL default '5.00',
 `count` int(10) unsigned NOT NULL,
 `regiond` tinyint(1) unsigned NOT NULL default '0',
 `dpenable` enum('true','false') NOT NULL default 'false',
 `detail` text NOT NULL,
 `order` smallint(5) unsigned NOT NULL default '1',
 `enabled` enum('true','false') NOT NULL default 'false',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_express_area`;

CREATE TABLE `{prefix}tttuangou_express_area` (
 `id` int(11) unsigned NOT NULL auto_increment,
 `parent` int(10) unsigned NOT NULL,
 `firstprice` decimal(10,2) unsigned NOT NULL default '10.00',
 `continueprice` decimal(10,2) unsigned NOT NULL default '5.00',
 `region` text NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_express_cdp`;

CREATE TABLE `{prefix}tttuangou_express_cdp` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `cid` int(10) unsigned NOT NULL,
 `bgid` int(10) unsigned NOT NULL,
 `config` text NOT NULL,
 `upstime` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `cid` (`cid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_express_corp`;

CREATE TABLE `{prefix}tttuangou_express_corp` (
 `id` int(11) unsigned NOT NULL auto_increment,
 `flag` varchar(10) NOT NULL default 'OTHER',
 `name` varchar(50) NOT NULL,
 `site` varchar(120) NOT NULL,
 `enabled` enum('true','false') NOT NULL default 'false',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_express_printer_log`;

CREATE TABLE `{prefix}tttuangou_express_printer_log` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `sign` bigint(11) unsigned NOT NULL,
 `corp` int(10) unsigned NOT NULL,
 `sender` int(10) unsigned NOT NULL,
 `upstime` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `sign` (`sign`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_finder`;

CREATE TABLE `{prefix}tttuangou_finder` (
 `id` int(10) NOT NULL auto_increment,
 `buyid` int(10) NOT NULL default '0',
 `buytime` int(10) NOT NULL default '0',
 `productid` int(10) NOT NULL default '0',
 `finderid` int(10) NOT NULL default '0',
 `findtime` int(10) NOT NULL default '0',
 `status` smallint(2) NOT NULL default '1',
 UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_metas`;

CREATE TABLE `{prefix}tttuangou_metas` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `uid` int(10) unsigned default NULL,
 `key` char(64) NOT NULL,
 `val` text NOT NULL,
 `life` int(10) unsigned NOT NULL default '3600',
 `uptime` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `uid` (`uid`),
 KEY `key` (`key`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_order`;

CREATE TABLE `{prefix}tttuangou_order` (
 `orderid` bigint(11) NOT NULL default '0',
 `productid` int(10) NOT NULL default '0',
 `productnum` int(10) NOT NULL default '0',
 `productprice` decimal(10,2) NOT NULL default '0.00',
 `totalprice` decimal(10,2) default '0.00',
 `userid` int(10) NOT NULL default '0',
 `addressid` int(10) NOT NULL default '0',
 `buytime` int(10) NOT NULL default '0',
 `is_countdown` TINYINT(1) NOT NULL DEFAULT '0',
 `paytype` int(10) default '0',
 `paymoney` decimal(10,2) NOT NULL default '0.00',
 `pay` tinyint(1) default '0',
 `paytime` int(10) NOT NULL default '0',
 `expresstype` int(10) NOT NULL default '0',
 `expressprice` decimal(10,2) NOT NULL default '0.00',
 `invoice` varchar(32) NOT NULL,
 `expresstime` int(10) NOT NULL default '0',
 `extmsg` text NOT NULL,
 `extmsg_reply` text NOT NULL,
 `process` varchar(24) NOT NULL default '__CREATE__',
 `process_time` int(10) unsigned NOT NULL,
 `status` tinyint(1) unsigned NOT NULL,
 `remark` text,
 `comment` tinyint(1) NOT NULL default '0',
 `share_uid` int(10) NOT NULL default '0' COMMENT '分享产品的用户id',
 `sid` int(10) NOT NULL default '0' COMMENT '商家编号，我要买单的订单需要',
 `last_rebate_day` int(10) NOT NULL DEFAULT '0' COMMENT '剩余返利天数',
 `promo_code` varchar(8) DEFAULT NULL COMMENT '优惠码',
 `promo_cut` decimal(10,2) DEFAULT '0.00' COMMENT '优惠码立减金额',
 UNIQUE KEY `orderid` (`orderid`),
 KEY `productid` (`productid`),
 KEY(`buytime`),
 KEY(`is_countdown`),
 KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_order_clog`;

CREATE TABLE `{prefix}tttuangou_order_clog` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `sign` bigint(11) unsigned NOT NULL,
 `action` varchar(36) NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 `remark` text NOT NULL,
 `time` int(10) unsigned NOT NULL,
 `process` varchar(100) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `sign` (`sign`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_payfrom`;

CREATE TABLE `{prefix}tttuangou_payfrom` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `uid` int(10) unsigned NOT NULL,
 `pid` char(20) NOT NULL,
 `pcode` char(100) NOT NULL,
 `pname` char(200) NOT NULL,
 `payid` char(200) NOT NULL,
 `payfrom` char(200) NOT NULL,
 `paynum` int(10) unsigned NOT NULL,
 `paysum` float unsigned NOT NULL,
 `pay` text NOT NULL,
 `time` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `upid` (`uid`,`pid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_paylog`;

CREATE TABLE `{prefix}tttuangou_paylog` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `uid` int(10) unsigned NOT NULL,
 `type` int(10) unsigned NOT NULL,
 `sign` varchar(32) NOT NULL,
 `money` decimal(10,2) unsigned NOT NULL,
 `time` int(10) unsigned NOT NULL,
 `trade_no` varchar(32) NOT NULL,
 `status` varchar(24) NOT NULL,
 `payfrom` varchar(200) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `sign` (`sign`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_payment`;

CREATE TABLE `{prefix}tttuangou_payment` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `code` varchar(20) NOT NULL,
 `name` varchar(120) NOT NULL,
 `detail` text NOT NULL,
 `order` int(10) unsigned NOT NULL default '0',
 `config` text NOT NULL,
 `enabled` enum('true','false') NOT NULL default 'false',
 `is_cod` tinyint(1) unsigned NOT NULL DEFAULT '0',
 `is_online` tinyint(1) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `pay_code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_phone`;

CREATE TABLE `{prefix}tttuangou_phone` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `uid` int(10) unsigned NOT NULL,
 `phone` char(11) NOT NULL,
 `stime` int(10) unsigned NOT NULL,
 `btime` int(10) unsigned NOT NULL,
 `vfcode` char(6) NOT NULL,
 `vftime` int(10) unsigned NOT NULL DEFAULT '0',
 `vftimes` tinyint(1) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 KEY `phone` (`phone`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_prize_phone`;

CREATE TABLE `{prefix}tttuangou_prize_phone` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `uid` int(10) unsigned NOT NULL,
 `phone` char(11) NOT NULL,
 `vfcode` char(6) NOT NULL,
 `vftime` int(10) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `phone` (`phone`),
 UNIQUE KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_prize_ticket`;

CREATE TABLE `{prefix}tttuangou_prize_ticket` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `pid` int(10) unsigned NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 `number` int(10) unsigned NOT NULL,
 `remark` varchar(128) NOT NULL,
 `upstime` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `pid` (`pid`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_prize_ticket_win`;

CREATE TABLE `{prefix}tttuangou_prize_ticket_win` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `pid` int(10) unsigned NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 `phone` char(11) NOT NULL,
 `number` int(10) unsigned NOT NULL,
 `upstime` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_product`;

CREATE TABLE `{prefix}tttuangou_product` (
 `id` int(10) NOT NULL auto_increment,
 `category` int(10) unsigned NOT NULL DEFAULT '0',
 `sellerid` int(10) NOT NULL default '0',
 `city` int(10) NOT NULL default '0',
 `city_place_region` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `city_place_street` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `name` varchar(200) NOT NULL default '',
 `flag` varchar(80) NOT NULL,
 `price` decimal(10,2) NOT NULL default '0.00',
 `nowprice` decimal(10,2) NOT NULL default '0.00',
 `fundprice` decimal(10,2) NOT NULL default '-1.00',
 `img` text NOT NULL,
 `intro` text NOT NULL,
 `content` text NOT NULL,
 `cue` text NOT NULL,
 `theysay` text NOT NULL,
 `wesay` text NOT NULL,
 `begintime` int(10) NOT NULL default '0',
 `overtime` int(10) NOT NULL default '0',
 `is_countdown` TINYINT(1) NOT NULL DEFAULT '0',
 `type` enum('ticket','stuff','prize') NOT NULL default 'ticket',
 `perioddate` int(10) NOT NULL default '0',
 `weight` int(10) unsigned NOT NULL COMMENT '重量，单位是kg',
 `successnum` smallint(6) NOT NULL default '0',
 `virtualnum` smallint(6) NOT NULL DEFAULT '0' COMMENT '虚拟已购买人数,前台购买人数会显示[ 虚拟购买人数+真实购买人数 ] (注：抽奖项目保存后此项不可再修改)',
 `maxnum` int(10) default '0',
 `oncemax` int(10) default '0',
 `oncemin` int(10) default '1',
 `multibuy` enum('true','false') NOT NULL default 'false',
 `allinone` enum('true','false') NOT NULL default 'false',
 `totalnum` int(10) default '0',
 `display` tinyint(1) NOT NULL default '0',
 `addtime` int(10) NOT NULL default '0',
 `status` smallint(1) NOT NULL default '1',
 `order` smallint(6) default '0',
 `saveHandler` enum('normal','draft') NOT NULL default 'normal',
 `draft` int(10) unsigned NOT NULL default '0',
 `sells_count` INT(10) UNSIGNED NOT NULL DEFAULT '0',
 `longitude` double(15,12) UNSIGNED NOT NULL DEFAULT '0.0000000',
 `latitude` double(15,12) UNSIGNED NOT NULL DEFAULT '0.0000000',
 `score` int(10) NOT NULL DEFAULT '0',
 `linkid` int(10) NOT NULL DEFAULT '0',
 `hotenabled` enum('true','false') NOT NULL DEFAULT 'false',
 `promo_cut` decimal(10,2) NOT NULL COMMENT '优惠码立减',
 `newbie_cut` decimal(10,2) NOT NULL COMMENT '新用户首单立减X元',
 `client_cut` decimal(10,2) NOT NULL COMMENT '在手机客户端购买立减X元',
 `limit_level` tinyint(1) unsigned NOT NULL COMMENT '对购买用户的等级进行限制？',
 `rebate_money` decimal(10,2) NOT NULL COMMENT '购物返现金额，大于0时返现，留空或为0时不返现，小于0时禁止返现',
 `rebate_limit` int(10) NOT NULL COMMENT '购物返现次数限制，留空或为0时不作限制。仅在设置了返现金额时才生效',
 `rebate_day` int(10) NOT NULL DEFAULT '0' COMMENT '设置按天返利天数',
 `last_rebate_day` int(10) NOT NULL DEFAULT '0' COMMENT '剩余返利天数',
 `yungou` tinyint(1) NOT NULL DEFAULT '0' COMMENT '云购模式（0：非云购产品，1：进行中，已成团，2：已结束，等待揭晓，3：已揭晓，云购成功）',
 `sub_sellerid` varchar(200) NOT NULL COMMENT '分店商家ID、即其他可用商家（即该产品团购券可以在哪些商家——分店里使用，存储格式为 3,4,5）',
 `needknow` TEXT COMMENT '购买须知（仅限客户端使用）',
 `tc` TEXT COMMENT '产品套餐（仅限客户端使用）',
 `share_rebate_money` decimal(10,2) COMMENT '分享产品返利金额',
 `role_rebate_user` tinyint(2) NOT NULL DEFAULT '0' COMMENT '0表示全部用户 1仅拥有返利权限的用户',
 UNIQUE KEY `id` (`id`),
 KEY `city` (`city`),
 KEY `is_countdown` (`is_countdown`),
 KEY `begintime` (`begintime`),
 KEY `order` (`order`),
 KEY `sells_count` (`sells_count`),
 KEY `nowprice` (`nowprice`),
 KEY `overtime` (`overtime`),
 KEY `sellerid` (`sellerid`),
 KEY `category` (`category`),
 KEY `yungou` (`yungou`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_push_log`;

CREATE TABLE `{prefix}tttuangou_push_log` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `type` varchar(8) NOT NULL,
 `driver` varchar(8) NOT NULL,
 `target` varchar(128) NOT NULL,
 `data` text NOT NULL,
 `title` TEXT NOT NULL,
 `content` TEXT NOT NULL,
 `result` varchar(256) NOT NULL,
 `result_raw` TEXT NOT NULL,
 `update` int(10) unsigned NOT NULL,
 `logger` ENUM('true','false') NOT NULL DEFAULT 'false',
 `status` ENUM('success','failed','system') NOT NULL DEFAULT 'system',
 `queuemsg` VARCHAR(128) NOT NULL,
 `userip` varchar(32) NOT NULL COMMENT '客户端ip',
 `request_uri` varchar(256) NOT NULL COMMENT '请求来源',
 PRIMARY KEY (`id`),
 KEY `update_userip` (`update`,`userip`),
 KEY `target` (`target`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_push_queue`;

CREATE TABLE `{prefix}tttuangou_push_queue` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `type` VARCHAR(8) NOT NULL,
 `target` MEDIUMTEXT NOT NULL,
 `title` VARCHAR(512) NOT NULL,
 `content` TEXT NOT NULL,
 `guid` CHAR(36) NOT NULL,
 `worked` ENUM('idle','busying','completed','overdue') NOT NULL DEFAULT 'idle',
 `rund` ENUM('true','false') NOT NULL DEFAULT 'false',
 `result` VARCHAR(256) NULL DEFAULT NULL,
 `update` INT(10) UNSIGNED NOT NULL,
 `pr` TINYINT(3) UNSIGNED NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE INDEX `guid_worked` (`guid`, `worked`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_push_template`;

CREATE TABLE `{prefix}tttuangou_push_template` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `type` varchar(8) NOT NULL,
 `name` varchar(128) NOT NULL,
 `intro` varchar(256) NOT NULL,
 `title` varchar(128) NOT NULL,
 `content` text NOT NULL,
 `update` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_question`;

CREATE TABLE `{prefix}tttuangou_question` (
 `id` int(10) NOT NULL auto_increment,
 `userid` int(10) NOT NULL default '0',
 `username` varchar(100) NOT NULL default '',
 `content` text NOT NULL,
 `reply` text NOT NULL,
 `time` int(10) NOT NULL default '0',
 UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_recharge_card`;

CREATE TABLE `{prefix}tttuangou_recharge_card` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `number` char(12) NOT NULL,
 `password` char(6) NOT NULL,
 `price` decimal(10,2) unsigned NOT NULL,
 `usetime` int(10) unsigned NOT NULL DEFAULT '0',
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
 PRIMARY KEY (`id`),
 UNIQUE KEY `number` (`number`),
 INDEX `usetime` (`usetime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_recharge_order`;

CREATE TABLE `{prefix}tttuangou_recharge_order` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `orderid` bigint(11) unsigned NOT NULL,
 `userid` int(10) unsigned NOT NULL,
 `money` decimal(10,2) unsigned NOT NULL,
 `add_money` decimal(10,2) unsigned NOT NULL,
 `createtime` int(10) unsigned NOT NULL,
 `payment` int(10) unsigned NOT NULL,
 `paytime` int(10) unsigned NOT NULL,
 `status` tinyint(1) unsigned NOT NULL DEFAULT '1',
 `ptype` tinyint(1) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 UNIQUE KEY `orderid` (`orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_regions`;

CREATE TABLE `{prefix}tttuangou_regions` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `parent` int(10) unsigned default NULL,
 `path` varchar(20) default NULL,
 `grade` mediumint(8) unsigned default NULL,
 `name` varchar(50) NOT NULL,
 `enabled` enum('true','false') default 'false',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_reports`;

CREATE TABLE `{prefix}tttuangou_reports` (
 `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
 `service` CHAR(32) NOT NULL,
 `channel` CHAR(32) NOT NULL,
 `hoster` INT(10) UNSIGNED NOT NULL,
 `data` DECIMAL(10,2) UNSIGNED NOT NULL,
 `dateline` INT(10) UNSIGNED NOT NULL,
 PRIMARY KEY (`id`),
 INDEX `service` (`service`),
 INDEX `channel` (`channel`),
 INDEX `hoster` (`hoster`),
 INDEX `dateline` (`dateline`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_seller`;

CREATE TABLE `{prefix}tttuangou_seller` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `userid` int(10) unsigned NOT NULL DEFAULT '0',
 `sellername` varchar(100) NOT NULL,
 `sellerphone` varchar(100) NOT NULL,
 `selleraddress` varchar(255) NOT NULL,
 `sellerurl` varchar(100) NOT NULL,
 `sellermap` varchar(100) NOT NULL,
 `area` smallint(6) unsigned NOT NULL,
 `productnum` int(10) NOT NULL DEFAULT '0',
 `successnum` int(10) NOT NULL DEFAULT '0',
 `money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `total_money` decimal(15,2) NOT NULL DEFAULT '0.00',
 `account_money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `forbid_money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `home_uid` int(10) unsigned NOT NULL DEFAULT '0',
 `profit_pre` decimal(6,2) NOT NULL DEFAULT '0.00',
 `sell_pre` decimal(6,2) NOT NULL DEFAULT '0.00',
 `profit_id` int(10) unsigned NOT NULL DEFAULT '10000',
 `time` int(10) NOT NULL DEFAULT '0',
 `enabled` enum('true','false') NOT NULL DEFAULT 'true',
 `id_card` char(255) NOT NULL default '',
 `zhizhao` char(255) NOT NULL default '',
 `city_place_region` int(10) unsigned NOT NULL,
 `city_place_street` int(10) unsigned NOT NULL,
 `imgs` varchar(255) NOT NULL,
 `price_avg` int(10) unsigned NOT NULL,
 `category` int(10) unsigned NOT NULL,
 `trade_time` varchar(255) NOT NULL,
 `content` text NOT NULL,
 `display_order` smallint(4) unsigned NOT NULL DEFAULT '100',
 `longitude` double(15,12) unsigned NOT NULL,
 `latitude` double(15,12) unsigned NOT NULL,
 `maptype` enum('gps','sogou','baidu','google','qq','amap','aliyun','mapabc','mapbar','51ditu') NOT NULL DEFAULT 'sogou' COMMENT '地图类型，默认为sogou；5.0.x版本后的坐标为baidu',
 `up_sellerid` int(10) unsigned NOT NULL COMMENT '总店——上一级商家的ID',
 `sub_sellercount` int(10) unsigned NOT NULL COMMENT '分店——下一级商家数量统计',
 `assure_money` decimal(15,0) DEFAULT '0' COMMENT '商家保证金',
 `counter_name` varchar(20) DEFAULT NULL COMMENT '业务员姓名',
 `counter_tel` char(11) DEFAULT NULL COMMENT '业务员联系电话',
 `bankname` varchar(50) DEFAULT NULL COMMENT '账户名称',
 `banknumber` char(50) DEFAULT NULL COMMENT '银行账号',
 `bankuser` varchar(20) DEFAULT NULL COMMENT '开户人',
 `pay_into` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '设置买单分成，百分比',
 `bsn` char(225) NOT NULL DEFAULT '' COMMENT '打印机的sn1',
 `send_points` decimal(10,2) NOT NULL DEFAULT '0' COMMENT '设置赠送积分，几倍',
 PRIMARY KEY (`id`),
 UNIQUE KEY `userid` (`userid`),
 KEY `display_order` (`display_order`),
 KEY `successnum` (`successnum`),
 KEY `price_avg` (`price_avg`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_seller_ads`;

CREATE TABLE `{prefix}tttuangou_seller_ads` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `sellerid` int(10) NOT NULL DEFAULT '0' COMMENT '商家id',
 `text` varchar(255) NOT NULL COMMENT '文字说明',
 `link` char(255) NOT NULL COMMENT '广告链接',
 `target` char(10) NOT NULL COMMENT '_self 当前页面打开 _blank 新建窗口打开',
 `images` char(255) NOT NULL COMMENT '商家广告图片',
 `sort` int(10) NOT NULL DEFAULT '0' COMMENT '显示排序',
 `aid` char(10) NOT NULL COMMENT '广告id',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_service`;

CREATE TABLE `{prefix}tttuangou_service` (
 `id` int(11) unsigned NOT NULL auto_increment,
 `type` varchar(8) NOT NULL,
 `flag` varchar(18) NOT NULL,
 `name` varchar(32) NOT NULL,
 `weight` int(10) unsigned NOT NULL,
 `count` int(10) unsigned NOT NULL,
 `config` text NOT NULL,
 `enabled` enum('true','false') NOT NULL default 'false',
 `update` int(10) unsigned NOT NULL,
 `surplus` int(10) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_subscribe`;

CREATE TABLE `{prefix}tttuangou_subscribe` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `type` varchar(8) NOT NULL,
 `target` varchar(128) NOT NULL,
 `city` int(10) unsigned NOT NULL default '0',
 `time` int(10) unsigned NOT NULL,
 `validated` enum('true','false') NOT NULL DEFAULT 'false',
 PRIMARY KEY (`id`),
 UNIQUE KEY `target` (`target`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_ticket`;

CREATE TABLE `{prefix}tttuangou_ticket` (
 `ticketid` INT(10) NOT NULL AUTO_INCREMENT,
 `uid` INT(10) NOT NULL DEFAULT '0',
 `productid` INT(10) NOT NULL DEFAULT '0',
 `orderid` BIGINT(13) NOT NULL DEFAULT '0',
 `guid` CHAR(36) NOT NULL,
 `number` VARCHAR(12) NOT NULL,
 `password` VARCHAR(6) NOT NULL DEFAULT '',
 `usetime` DATETIME NOT NULL,
 `status` TINYINT(1) NULL DEFAULT '0',
 `mutis` INT(10) UNSIGNED NOT NULL DEFAULT '1',
 `money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `ops_uid` int(10) unsigned NOT NULL COMMENT '用户UID，该券是哪个用户操作消费的',
 `ops_sid` int(10) unsigned NOT NULL COMMENT '商家SID，该券在哪个商家消费的 ',
 `ops_sname` varchar(100) NOT NULL DEFAULT '' COMMENT '商家名称，该券在哪个商家消费的',
 UNIQUE KEY `ticketid` (`ticketid`),
 UNIQUE KEY `number` (`number`),
 UNIQUE KEY `guid` (`guid`),
 KEY `ops_uid` (`ops_uid`),
 KEY `ops_sid` (`ops_sid`),
 KEY `uid` (`uid`),
 KEY `productid` (`productid`),
 KEY `usetime` (`usetime`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_uploads`;

CREATE TABLE `{prefix}tttuangou_uploads` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `name` varchar(64) NOT NULL,
 `intro` varchar(128) NOT NULL,
 `path` varchar(255) NOT NULL,
 `url` varchar(255) NOT NULL,
 `type` varchar(12) NOT NULL,
 `size` int(10) unsigned NOT NULL,
 `mime` varchar(32) NOT NULL,
 `extra` text NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 `ip` int(10) unsigned NOT NULL,
 `update` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_usermoney`;

CREATE TABLE `{prefix}tttuangou_usermoney` (
 `id` int(10) NOT NULL auto_increment,
 `userid` int(10) NOT NULL default '0',
 `class` enum('sys','usr') NOT NULL default 'sys',
 `type` enum('plus','minus') NOT NULL default 'plus',
 `name` varchar(100) NOT NULL,
 `intro` varchar(200) NOT NULL,
 `money` decimal(10,2) NOT NULL default '0.00',
 `time` int(10) NOT NULL default '0',
 `trade_no` varchar(16) DEFAULT NULL,
 UNIQUE KEY `mid` (`id`),
 KEY `userid` (`userid`),
 KEY `time` (`time`),
 KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_usermsg`;

CREATE TABLE `{prefix}tttuangou_usermsg` (
 `id` int(10) NOT NULL auto_increment,
 `name` varchar(100) NOT NULL default '',
 `phone` varchar(50) NOT NULL default '',
 `elsecontat` varchar(200) NOT NULL default '',
 `content` text NOT NULL,
 `time` int(10) NOT NULL default '0',
 `type` smallint(6) NOT NULL default '0',
 `readed` tinyint(1) NOT NULL default '0',
 UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_zlog`;

CREATE TABLE `{prefix}tttuangou_zlog` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `type` char(12) NOT NULL,
 `uid` int(10) unsigned NOT NULL,
 `uip` int(10) unsigned NOT NULL,
 `index` char(32) NOT NULL,
 `name` varchar(128) NOT NULL,
 `extra` text NOT NULL,
 `time` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `type` (`type`),
 KEY `index` (`index`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_favorite`;

CREATE TABLE `{prefix}tttuangou_favorite` (
 `uid` int(10) unsigned NOT NULL,
 `pid` int(10) unsigned NOT NULL,
 PRIMARY KEY (`uid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_refund`;

CREATE TABLE `{prefix}tttuangou_refund` (
 `orderid` bigint(13) unsigned NOT NULL,
 `uid` int(11) NOT NULL,
 `demand_money` decimal(10,2) unsigned NOT NULL,
 `demand_reason` varchar(255) DEFAULT '',
 `dateline` int(11) NOT NULL,
 `process` tinyint(3) unsigned NOT NULL,
 `op_uid` int(11) DEFAULT NULL,
 `op_money` decimal(10,2) unsigned NOT NULL,
 `op_reason` varchar(255) DEFAULT NULL,
 `op_dateline` int(11) DEFAULT NULL,
 `cash_type` varchar(200) NOT NULL,
 `cash_data` text NOT NULL,
 PRIMARY KEY (`orderid`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_cash_order`;

CREATE TABLE `{prefix}tttuangou_cash_order` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `orderid` bigint(11) unsigned NOT NULL,
 `userid` int(10) unsigned NOT NULL,
 `money` decimal(10,2) unsigned NOT NULL,
 `createtime` int(10) unsigned NOT NULL,
 `paytype` enum('alipay','bank','money') NOT NULL default 'money',
 `alipay` varchar(50) NOT NULL default '',
 `bankname` varchar(50) NOT NULL default '',
 `bankcard` varchar(50) NOT NULL default '',
 `bankusername` varchar(20) NOT NULL default '',
 `paytime` int(10) unsigned NOT NULL,
 `status` enum('no','yes','doing','error') NOT NULL default 'no',
 PRIMARY KEY (`id`),
 UNIQUE KEY `orderid` (`orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_fund_order`;

CREATE TABLE `{prefix}tttuangou_fund_order` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `orderid` bigint(11) unsigned NOT NULL,
 `userid` int(10) unsigned NOT NULL,
 `sellerid` int(10) unsigned NOT NULL,
 `money` decimal(10,2) unsigned NOT NULL,
 `createtime` int(10) unsigned NOT NULL,
 `paytype` enum('alipay','bank','money') NOT NULL default 'money',
 `alipay` varchar(50) NOT NULL default '',
 `bankname` varchar(50) NOT NULL default '',
 `bankcard` varchar(50) NOT NULL default '',
 `bankusername` varchar(20) NOT NULL default '',
 `paytime` int(10) unsigned NOT NULL,
 `status` enum('no','yes','doing','error') NOT NULL default 'no',
 `from` enum('admin','user') NOT NULL default 'user',
 PRIMARY KEY (`id`),
 UNIQUE KEY `orderid` (`orderid`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_cash_order_log`;

CREATE TABLE `{prefix}tttuangou_cash_order_log` (
 `id` int(10) NOT NULL auto_increment,
 `orderid` bigint(11) unsigned NOT NULL,
 `userid` int(10) unsigned NOT NULL,
 `username` varchar(50) NOT NULL,
 `createtime` int(10) unsigned NOT NULL,
 `status` varchar(20) NOT NULL,
 `info` varchar(250) NOT NULL default '',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_fund_order_log`;

CREATE TABLE `{prefix}tttuangou_fund_order_log` (
 `id` int(10) NOT NULL auto_increment,
 `orderid` bigint(11) unsigned NOT NULL,
 `userid` int(10) unsigned NOT NULL,
 `username` varchar(50) NOT NULL,
 `createtime` int(10) unsigned NOT NULL,
 `status` varchar(20) NOT NULL,
 `info` varchar(250) NOT NULL default '',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_rebate_log`;

CREATE TABLE `{prefix}tttuangou_rebate_log` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `uid` int(10) unsigned NOT NULL DEFAULT '0',
 `uname` varchar(45) NOT NULL DEFAULT '',
 `home_uid` int(10) unsigned NOT NULL DEFAULT '0',
 `deal_money` decimal(10,2) unsigned NOT NULL DEFAULT '0.00',
 `fund_money` decimal(10,2) NOT NULL DEFAULT '0.00',
 `salary_pre` decimal(6,2) unsigned NOT NULL DEFAULT '0.00',
 `salary_money` decimal(11,3) unsigned NOT NULL DEFAULT '0.000',
 `orderid` bigint(11) unsigned NOT NULL DEFAULT '0',
 `ticketid` int(10) unsigned NOT NULL DEFAULT '0',
 `type` enum('master','sell','buy','pay_bill','share_product','rebate_day') NOT NULL DEFAULT 'master',
 `addtime` char(10) NOT NULL DEFAULT '0',
 `rec_all` tinyint(1) unsigned NOT NULL DEFAULT '0',
 PRIMARY KEY (`id`),
 KEY `home_uid` (`home_uid`),
 KEY `type` (`type`),
 KEY `addtime` (`addtime`),
 KEY `uid` (`uid`),
 KEY `ticketid` (`ticketid`),
 KEY `orderid` (`orderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_credit`;

CREATE TABLE `{prefix}tttuangou_credit` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `uid` int(10) NOT NULL DEFAULT '0',
 `pid` int(10) NOT NULL DEFAULT '0',
 `info` char(255) NOT NULL DEFAULT '',
 `score` int(10) NOT NULL DEFAULT '0',
 `gettime` int(11) NOT NULL DEFAULT '0',
 `type` enum('buy','reply','forward','signin','creditmall','exchange','mall','pay_bill','credit_buy','give_credit','get_credit','system', 'minus', 'plus') NOT NULL DEFAULT 'buy',
 UNIQUE KEY `id` (`id`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_product_tag`;

CREATE TABLE `{prefix}tttuangou_product_tag` (
 `product_id` int(10) unsigned NOT NULL,
 `tag_id` int(10) unsigned NOT NULL,
 `order` int(10) unsigned NOT NULL,
 `expire` char(10) NOT NULL,
 `expire_time` int(10) unsigned NOT NULL,
 `enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
 `display_enable` tinyint(1) unsigned NOT NULL DEFAULT '0',
 `display_order` int(10) unsigned NOT NULL DEFAULT '100',
 PRIMARY KEY (`product_id`,`tag_id`),
 KEY `order` (`order`),
 KEY `tag_id` (`tag_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_tag`;

CREATE TABLE `{prefix}tttuangou_tag` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `name` char(100) NOT NULL,
 `desc` char(255) NOT NULL,
 `order` int(10) unsigned NOT NULL,
 `expire` char(10) NOT NULL,
 `expire_time` int(10) unsigned NOT NULL,
 `enable` tinyint(1) unsigned NOT NULL DEFAULT '1',
 `display_enable` tinyint(1) unsigned NOT NULL DEFAULT '0',
 `display_order` int(10) unsigned NOT NULL DEFAULT '100',
 PRIMARY KEY (`id`),
 KEY `order` (`order`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_product_link`;

CREATE TABLE `{prefix}tttuangou_product_link` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `sellerid` int(10) unsigned NOT NULL default '0',
 `link_product` text,
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_product_notify`;

CREATE TABLE `{prefix}tttuangou_product_notify` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '自增ID',
 `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
 `product_id` int(10) unsigned NOT NULL COMMENT '产品ID',
 `notify_id` varchar(100) NOT NULL COMMENT '提醒标记',
 `notify_type` enum('sms','mail') NOT NULL DEFAULT 'sms' COMMENT '提醒类型',
 `notify_to` varchar(100) NOT NULL COMMENT '提醒的对象，邮箱或手机号',
 `time` int(10) unsigned NOT NULL COMMENT '提醒的时间戳',
 `status` enum('-1','1') NOT NULL DEFAULT '-1' COMMENT '-1：刚创建未发送的；1：已加入发送队列',
 `status_time` int(10) unsigned NOT NULL COMMENT 'status变更的时间戳',
 PRIMARY KEY (`id`),
 KEY `uid` (`uid`),
 KEY `productid` (`product_id`),
 KEY `time` (`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_cart`;

CREATE TABLE `{prefix}tttuangou_cart` (
 `create_date` int(10) unsigned NOT NULL COMMENT '生成时间',
 `state` tinyint(4) unsigned NOT NULL DEFAULT '1' COMMENT '0-不启用 1-启用',
 `user_id` int(10) unsigned NOT NULL COMMENT '会员id',
 UNIQUE KEY `u_user_id` (`user_id`),
 KEY `i_state` (`state`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_cart_item`;

CREATE TABLE `{prefix}tttuangou_cart_item` (
 `cart_id` int(10) NOT NULL COMMENT '外键，关联cart表的user_id',
 `id` int(10) NOT NULL AUTO_INCREMENT COMMENT '主键',
 `nowprice` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '商品价格',
 `num` int(10) NOT NULL DEFAULT '0' COMMENT '数量',
 `product_id` int(10) NOT NULL COMMENT '商品id',
 `product_name` varchar(200) NOT NULL COMMENT '商品名称',
 `seller_id` int(10) NOT NULL COMMENT '商家id',
 `status` tinyint(4) NOT NULL DEFAULT '1' COMMENT '0-不启用 1-启用',
 PRIMARY KEY (`id`),
 UNIQUE KEY `u_product_id_cart_id` (`cart_id`,`product_id`),
 KEY `i_status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_special`;

CREATE TABLE `{prefix}tttuangou_special` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '专题自增ID',
 `name` varchar(250) NOT NULL COMMENT '专题名称',
 `flag` varchar(250) NOT NULL COMMENT '短标题',
 `intro` varchar(250) NOT NULL COMMENT '专题简介',
 `pic` varchar(250) NOT NULL COMMENT '图片',
 `time` int(10) unsigned NOT NULL COMMENT '创建时间',
 `status` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1：启用 0：未启用',
 `display_order` int(10) unsigned NOT NULL COMMENT '排序',
 `type` varchar(50) NOT NULL COMMENT '类型 link：链接 product：产品',
 `settings` text NOT NULL COMMENT '详情的设置（包含链接地址，产品信息等）',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_signin_log`;

CREATE TABLE `{prefix}tttuangou_signin_log` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '签到LOG自增ID',
 `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
 `day` date NOT NULL COMMENT '签到的日期',
 `credit` int(10) NOT NULL COMMENT '签到的积分',
 `timestamp` int(10) unsigned NOT NULL COMMENT '签到的具体时间戳',
 PRIMARY KEY (`id`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_signin_rule`;

CREATE TABLE `{prefix}tttuangou_signin_rule` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '签到规则自增ID',
 `day` int(10) unsigned NOT NULL COMMENT '连续签到的天数',
 `credit` int(10) NOT NULL COMMENT '相应的积分操作',
 PRIMARY KEY (`id`),
 KEY `day` (`day`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_signin_user`;

CREATE TABLE `{prefix}tttuangou_signin_user` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '签到用户自增ID',
 `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
 `total` int(10) unsigned NOT NULL COMMENT '用户的总签到天数',
 `count` int(10) unsigned NOT NULL COMMENT '用户的连续签到天数',
 `credit` int(10) NOT NULL COMMENT '用户的签到积分总数',
 `day` date NOT NULL COMMENT '最后签到的日期',
 `timestamp` int(10) unsigned NOT NULL COMMENT '最后签到的时间戳',
 PRIMARY KEY (`id`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_creditmall_goods`;

CREATE TABLE `{prefix}tttuangou_creditmall_goods` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '商品ID',
 `uid` int(10) unsigned NOT NULL COMMENT '添加者UID',
 `name` char(200) NOT NULL COMMENT '商品名称',
 `price` decimal(10,2) unsigned NOT NULL COMMENT '商品价格：所需要的积分',
 `desc` text NOT NULL COMMENT '商品详情',
 `image` char(200) NOT NULL COMMENT '商品图片',
 `dateline` int(10) NOT NULL COMMENT '添加时间',
 `total` int(10) unsigned NOT NULL COMMENT '总的商品数（库存数）',
 `count` int(10) unsigned NOT NULL COMMENT '成功卖出的数量',
 `order_count` int(10) unsigned NOT NULL COMMENT '订单数量（包括取消的订单）',
 `order` int(10) unsigned NOT NULL DEFAULT '100' COMMENT '列表时排序（从大到小排序）',
 `expire` int(10) unsigned NOT NULL COMMENT '有效期（过期时间）',
 `category` int(10) NOT NULL DEFAULT '0' COMMENT '商品分类',
 PRIMARY KEY (`id`),
 KEY `order` (`order`),
 KEY `name` (`name`),
 KEY `price` (`price`),
 KEY `expire` (`expire`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_creditmall_order`;

CREATE TABLE `{prefix}tttuangou_creditmall_order` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `sn` varchar(30) NOT NULL COMMENT '订单ID',
 `uid` int(10) unsigned NOT NULL COMMENT '用户UID',
 `username` varchar(50) NOT NULL DEFAULT '',
 `goods_id` int(10) unsigned NOT NULL COMMENT '商品ID',
 `goods_name` varchar(100) NOT NULL DEFAULT '' COMMENT '商品名称',
 `goods_num` int(10) unsigned NOT NULL COMMENT '商品数量',
 `goods_price` decimal(10,2) NOT NULL COMMENT '商品单价（单个商品需要的积分数）',
 `pay_money` decimal(10,2) NOT NULL COMMENT '支付价格（当前商品单价x购买数量所需要支付的总积分数）',
 `address` varchar(250) NOT NULL COMMENT '地址',
 `tel` varchar(100) NOT NULL COMMENT '手机 电话',
 `qq` varchar(100) NOT NULL COMMENT 'QQ',
 `add_time` int(10) unsigned NOT NULL COMMENT '添加时间',
 `pay_time` int(10) unsigned NOT NULL COMMENT '支付时间 大于0时表示该订单已经支付',
 `status_time` int(10) unsigned NOT NULL COMMENT '订单状态变更的时间戳',
 `status` tinyint(1) unsigned NOT NULL COMMENT '订单状态 0 已下单 1 已发货 2 已退单',
 PRIMARY KEY (`id`),
 KEY `status` (`status`),
 KEY `sn` (`sn`),
 KEY `goods_id` (`goods_id`),
 KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_creditmall_order_action`;

CREATE TABLE `{prefix}tttuangou_creditmall_order_action` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
 `order_id` int(10) unsigned NOT NULL COMMENT '订单ID',
 `status` tinyint(1) unsigned NOT NULL COMMENT '状态',
 `msg` char(250) NOT NULL COMMENT '消息',
 `dateline` int(10) unsigned NOT NULL COMMENT '记录时间戳',
 PRIMARY KEY (`id`),
 KEY `status` (`status`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_hongbao`;

CREATE TABLE `{prefix}tttuangou_hongbao` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '红包ID',
 `name` char(200) NOT NULL COMMENT '名称',
 `intro` text NOT NULL COMMENT '说明',
 `time_start` char(100) NOT NULL COMMENT '开始时间，比如2015-03-10 00:00:00',
 `timestamp_start` int(10) unsigned NOT NULL COMMENT '开始的时间戳',
 `time_end` char(100) NOT NULL COMMENT '结束时间，比如2015-05-10 23:59:59',
 `timestamp_end` int(10) unsigned NOT NULL COMMENT '结束的时间戳',
 `enable` tinyint(1) unsigned NOT NULL DEFAULT '1' COMMENT '是否启用',
 `total_num` int(10) unsigned NOT NULL COMMENT '总的红包个数，为0时表示不限制',
 `total_money` float(10,2) unsigned NOT NULL COMMENT '总的红包金额，为0时表示不限制',
 `send_num` int(1) unsigned NOT NULL COMMENT '总的红包发放个数，大于总个数时表示结束',
 `send_money` float(10,2) unsigned NOT NULL COMMENT '总的红包发放金额，大于总金额时表示结束',
 `money_min` float(10,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '每个红包发放金额的最小值',
 `money_max` float(10,2) unsigned NOT NULL DEFAULT '1.00' COMMENT '每个红包发放金额的最大值，两值相等时为固定金额，否则为随机金额',
 `total_pp` int(10) unsigned NOT NULL COMMENT '实际参与的人数',
 `all_pp` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '预计参与的人数：中奖概率=总的红包个数/（预计人数*每人中奖次数）',
 `limit_total_num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '限制个人中奖的总次数',
 `limit_day_num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '限制个人一天之内中奖的次数',
 `limit_total_join_num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '限制个人总共可以参与抽奖的次数',
 `limit_day_join_num` int(10) unsigned NOT NULL DEFAULT '1' COMMENT '限制个人一天之内参与抽奖的次数',
 PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_hongbao_log`;

CREATE TABLE `{prefix}tttuangou_hongbao_log` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
 `hongbao_id` int(10) unsigned NOT NULL COMMENT '红包ID',
 `uid` int(10) unsigned NOT NULL COMMENT '用户ID',
 `day` date NOT NULL COMMENT '参与日期，格式为2015-03-10',
 `money` float(10,2) unsigned NOT NULL DEFAULT '0.00' COMMENT '中奖金额',
 `timestamp` int(10) unsigned NOT NULL COMMENT '参与时间戳',
 PRIMARY KEY (`id`),
 KEY `uid_hongbao_id_day` (`uid`,`hongbao_id`,`day`),
 KEY `hongbao_id_uid_money` (`hongbao_id`,`uid`,`money`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_order_cut`;

CREATE TABLE `{prefix}tttuangou_order_cut` (
 `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '订单立减优惠表ID',
 `sign` char(50) NOT NULL COMMENT '订单号',
 `flag` char(50) NOT NULL COMMENT '优惠立减项目',
 `cut` decimal(10,2) unsigned NOT NULL COMMENT '优惠立减金额',
 `desc` char(255) NOT NULL COMMENT '优惠信息简述',
 `timestamp` int(10) unsigned NOT NULL,
 PRIMARY KEY (`id`),
 KEY `sign` (`sign`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_yungou_canyu`;

CREATE TABLE `{prefix}tttuangou_yungou_canyu` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `pid` int(10) NOT NULL COMMENT '外键，产品表product的ID',
 `orderid` bigint(11) NOT NULL DEFAULT '3' COMMENT '订单号',
 `uid` int(10) NOT NULL COMMENT '外键，用户表members的ID',
 `number` bigint(6) NOT NULL COMMENT '云购号码',
 `remark` varchar(100) NOT NULL DEFAULT '参加云购' COMMENT '现在没什么用，以后可能会在这里添加一些注释。',
 `addtime` int(10) NOT NULL COMMENT '参与此次云购的时间',
 `guid` char(36) NOT NULL COMMENT '唯一标识码，防止重复生成',
 PRIMARY KEY (`id`),
 KEY `pid` (`pid`,`uid`),
 KEY `orderid` (`orderid`),
 KEY `guid` (`guid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_yungou_zhongjiang`;

CREATE TABLE `{prefix}tttuangou_yungou_zhongjiang` (
 `id` int(10) NOT NULL AUTO_INCREMENT,
 `pid` int(10) NOT NULL COMMENT '外键，产品表product的ID',
 `orderid` bigint(11) NOT NULL COMMENT '中奖号码的订单',
 `uid` int(10) NOT NULL COMMENT '外键，用户表members的ID',
 `phone` varchar(11) DEFAULT NULL COMMENT '手机号码',
 `number` bigint(6) NOT NULL COMMENT '中将的号码',
 `addtime` int(10) NOT NULL,
 PRIMARY KEY (`id`),
 KEY `pid` (`pid`,`uid`),
 KEY `number` (`number`),
 KEY `phone` (`phone`),
 KEY `orderid` (`orderid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_seccheck`;

CREATE TABLE `{prefix}tttuangou_seccheck` (
 `ssid` char(32) NOT NULL,
 `dateline` int(10) NOT NULL,
 `code` char(6) NOT NULL,
 `succeed` tinyint(1) unsigned NOT NULL,
 `verified` tinyint(1) unsigned NOT NULL,
 `ip` char(15) NOT NULL,
 PRIMARY KEY (`ssid`),
 KEY `dateline` (`dateline`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_addmoney`;

CREATE TABLE `{prefix}tttuangou_addmoney` (
 `id` bigint(11) NOT NULL DEFAULT 0 COMMENT '用户充值记录自增ID',
 `money` decimal(10,2) NOT NULL COMMENT '金额',
 `paytime` datetime NOT NULL COMMENT '时间',
 `userid` int(11) NOT NULL COMMENT '用户UID',
 UNIQUE KEY `NewIndex1`(`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_credit_order`;

CREATE TABLE `{prefix}tttuangou_credit_order` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `createtime` int(10) unsigned NOT NULL,
 `money` decimal(10,2) unsigned NOT NULL,
 `orderid` bigint(11) unsigned NOT NULL,
 `payment` int(10) unsigned NOT NULL COMMENT '支付方式',
 `paytime` int(10) unsigned NOT NULL,
 `ptype` tinyint(1) unsigned NOT NULL DEFAULT 0,
 `status` tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '大于0并且paytime大于0，表示订单已经支付 1-正常',
 `userid` int(10) unsigned NOT NULL,
 `pay` int(10) unsigned NOT NULL DEFAULT 0,
 `credit` int(10) unsigned NOT NULL DEFAULT 0,
 PRIMARY KEY (`id`),
 UNIQUE KEY `orderid`(`orderid`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_goods_catalog`;

CREATE TABLE `{prefix}tttuangou_goods_catalog` (
 `id` int(10) unsigned NOT NULL auto_increment,
 `flag` varchar(32) NOT NULL,
 `fontcolor` varchar(20) NOT NULL,
 `hot` tinyint(1) unsigned NOT NULL,
 `hotorder` tinyint(2) unsigned NOT NULL,
 `name` varchar(32) NOT NULL,
 `order` tinyint(2) unsigned NOT NULL DEFAULT 0,
 `oslcount` int(10) unsigned NOT NULL DEFAULT 0,
 `parent` int(10) unsigned NOT NULL DEFAULT 0,
 `procount` int(10) unsigned NOT NULL DEFAULT 0,
 `upstime` int(10) unsigned NOT NULL,
 `index_display_count` int(10) NOT NULL DEFAULT 6,
 PRIMARY KEY (`id`),
 UNIQUE KEY `flag`(`flag`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_mail`;

CREATE TABLE `{prefix}tttuangou_mail` (
 `mid` int(11) NOT NULL auto_increment,
 `content` text NOT NULL,
 `intro` varchar(200) NOT NULL,
 `name` varchar(100) NOT NULL,
 `title` varchar(100) NOT NULL,
 PRIMARY KEY (`mid`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `{prefix}tttuangou_sms`;

CREATE TABLE `{prefix}tttuangou_sms` (
 `id` int(10) NOT NULL auto_increment,
 `content` text NOT NULL,
 `mid_resend` varchar(15) NULL,
 `mid` varchar(15) NOT NULL,
 `name` varchar(45) NOT NULL,
 `phone` varchar(11) NOT NULL,
 `state` varchar(26) NOT NULL,
 PRIMARY KEY (`id`) 
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `{prefix}tttuangou_promo_code`;

CREATE TABLE `{prefix}tttuangou_promo_code` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(8) NOT NULL COMMENT '优惠码',
  `name` varchar(16) NOT NULL COMMENT '名称',
  PRIMARY KEY (`id`),
  UNIQUE KEY `idx_code` (`code`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
