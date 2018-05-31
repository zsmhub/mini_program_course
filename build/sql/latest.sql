# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.18)
# Database: mini_program_course
# Generation Time: 2017-10-31 07:20:56 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table mini_course
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mini_course`;

CREATE TABLE `mini_course` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `course_name` char(60) NOT NULL COMMENT '课程名',
  `place` char(100) NOT NULL COMMENT '上课地点',
  `course_time` char(100) NOT NULL DEFAULT '' COMMENT '上课时间',
  `outline` text NOT NULL COMMENT '课程简介',
  `score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '课程学分',
  `teacher_ids` text NOT NULL COMMENT '任课老师id',
  `teacher_names` text NOT NULL COMMENT '任课老师姓名',
  `limit_num` mediumint(9) NOT NULL DEFAULT '0' COMMENT '限制报名人数,默认为0，即无限制',
  `close_time` int(11) NOT NULL COMMENT '报名截止时间',
  `late_time` int(11) NOT NULL COMMENT '扫码迟到时间',
  `activity_num` mediumint(9) NOT NULL DEFAULT '0' COMMENT '已报名人数',
  `operator` char(32) NOT NULL COMMENT '操作者',
  `operate_time` int(11) NOT NULL COMMENT '操作时间',
  `deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '删除标志',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='课程列表';

LOCK TABLES `mini_course` WRITE;
/*!40000 ALTER TABLE `mini_course` DISABLE KEYS */;

INSERT INTO `mini_course` (`id`, `course_name`, `place`, `course_time`, `outline`, `score`, `teacher_ids`, `teacher_names`, `limit_num`, `close_time`, `late_time`, `activity_num`, `operator`, `operate_time`, `deleted`)
VALUES
  (58,'课程一我们的祖国','培训科室','周五下午','课程简单介绍',2.00,'|zhangsan|test|','张三2, 123456',3,1508997660,1508397660,3,'张三',1508397705,0),
  (59,'课程二我们的家','草地','12月1日','课程简单介绍3',2.00,'|test|test2|','123456, afd',1,1508926045,1508397780,0,'张三',1508398735,0),
  (60,'人工智能课程学习','理工楼402','周三晚上7点','学些人工智能等新兴知识。。。',5.00,'|zhangsan|','张三2',2,1509415440,1509501840,2,'张三',1508983504,0),
  (61,'Python基础知识学习','计算机楼502','11月11日11点整','Python基础知识学习：人生苦短，我学Python',1.00,'|zhangsan|','张三2',5,1509156600,1509502200,3,'张三',1509008982,0),
  (62,'PHP学习哟哟2','科学楼','2017-12-12 12:12:12','php是世界上最好的语言，好好学习吧，骚年！本文档将带你一步步创建完成一个微信小程序，并可以在手机上体验该小程序的实际效果。这个小程序的首页将会显示欢迎语以及当前用户的微信头像，点击头像，可以在新开的页面中查看当前小程序的启动日志。',10.00,'|test2|','afd',10,1509070320,1509415920,1,'张三',1509009001,0),
  (63,'音乐课','音乐学院','周二晚8点-10点','音乐课音乐课音乐课音乐课音乐课音乐课音乐课音乐课音乐课音乐课音乐课音乐课',2.00,'|teacher|','teacher',0,1509349620,1509695220,3,'张三',1509004091,0);

/*!40000 ALTER TABLE `mini_course` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mini_student
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mini_student`;

CREATE TABLE `mini_student` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `course_id` int(11) NOT NULL COMMENT '课程id',
  `apply_time` int(11) NOT NULL DEFAULT '0' COMMENT '报名时间',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '状态(0为选修报名, -1为取消报名)',
  `score` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '上课获得学分',
  `is_sign` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否签到，默认0为未签到，1为已签到',
  `sign_time` int(11) NOT NULL DEFAULT '0' COMMENT '签到时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='学生课程报名管理';

LOCK TABLES `mini_student` WRITE;
/*!40000 ALTER TABLE `mini_student` DISABLE KEYS */;

INSERT INTO `mini_student` (`id`, `username`, `course_id`, `apply_time`, `status`, `score`, `is_sign`, `sign_time`)
VALUES
  (1,'1106100116',58,1508397705,-1,0.00,1,1508397705),
  (2,'1106100116',59,1508397705,-1,1.00,0,0),
  (5,'student',59,1508926045,-1,0.00,0,0),
  (7,'student',59,1508926248,-1,0.00,0,0),
  (8,'student',58,1508926697,-1,0.00,0,0),
  (9,'student',58,1508928522,-1,0.00,0,0),
  (10,'student',58,1508928566,0,0.00,0,0),
  (11,'liang',62,1508985758,0,0.00,0,0),
  (12,'student',61,1508988037,0,0.00,0,0),
  (14,'student',63,1509004426,0,0.00,0,0),
  (15,'liang',63,1509005434,0,0.00,0,0),
  (16,'liang',61,1509007474,0,0.00,0,0),
  (17,'yuer',63,1509007563,0,2.00,1,1509067790),
  (18,'yuer',61,1509007583,0,1.00,1,1509070996),
  (19,'liang',60,1509008147,0,0.00,0,0),
  (20,'yuer',60,1509008395,0,0.00,0,0);

/*!40000 ALTER TABLE `mini_student` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mini_teacher
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mini_teacher`;

CREATE TABLE `mini_teacher` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '账号',
  `course_id` int(11) NOT NULL COMMENT '课程id',
  `operate_time` int(11) NOT NULL COMMENT '操作时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师授课管理';

LOCK TABLES `mini_teacher` WRITE;
/*!40000 ALTER TABLE `mini_teacher` DISABLE KEYS */;

INSERT INTO `mini_teacher` (`id`, `username`, `course_id`, `operate_time`)
VALUES
  (1,'zhangsan',58,1508397705),
  (2,'test',58,1508397705),
  (5,'test',59,1508398735),
  (6,'test2',59,1508398735),
  (7,'zhangsan',60,1508983504),
  (14,'teacher',63,1509004091),
  (15,'zhangsan',61,1509008982),
  (16,'test2',62,1509009001);

/*!40000 ALTER TABLE `mini_teacher` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table mini_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `mini_user`;

CREATE TABLE `mini_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(32) NOT NULL DEFAULT '' COMMENT '登陆账号',
  `password` char(32) NOT NULL DEFAULT '' COMMENT '登陆密码',
  `nickname` varchar(20) NOT NULL DEFAULT '' COMMENT '姓名',
  `college` varchar(32) NOT NULL DEFAULT '' COMMENT '所属院系',
  `email` varchar(32) NOT NULL DEFAULT '' COMMENT '邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户状态 0:禁用,1:正常',
  `last_login_time` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近登录时间',
  `last_login_ip` char(15) NOT NULL DEFAULT '' COMMENT '最近登录IP',
  `deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '用户类型：0为学生，1为教师',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='教师学生账户信息表';

LOCK TABLES `mini_user` WRITE;
/*!40000 ALTER TABLE `mini_user` DISABLE KEYS */;

INSERT INTO `mini_user` (`id`, `username`, `password`, `nickname`, `college`, `email`, `status`, `last_login_time`, `last_login_ip`, `deleted`, `type`)
VALUES
  (1,'zhangsan','25f9e794323b453885f5181f1b624d0b','张三2','计算机2','zhangsan@qq.com',1,0,'',0,1),
  (2,'1106100118','25f9e794323b453885f5181f1b624d0b','李四','文科','a@qq.com',1,0,'',1,1),
  (3,'test','83422503bcfc01d303030e8a7cc80efc','123456','1','1@qq.com',1,0,'',0,1),
  (4,'test2','f340faa42120e3825353e3064e4872cd','afd','df','dfd@qq.com',1,0,'',0,1),
  (5,'1106100116','25f9e794323b453885f5181f1b624d0b','a','技术','a@qq.com',1,0,'',0,0),
  (6,'wangwu','df10ef8509dc176d733d59549e7dbfaf','12','yishu','a@qq.com',1,0,'',0,0),
  (7,'student','25f9e794323b453885f5181f1b624d0b','student','计算机','a@qq.com',1,0,'',0,0),
  (8,'liang','25f9e794323b453885f5181f1b624d0b','鬼2','软件工程','2@qq.com',1,0,'',0,0),
  (9,'yuer','25f9e794323b453885f5181f1b624d0b','鱼儿','文化','a@qq.com',1,0,'',0,0),
  (10,'teacher','25f9e794323b453885f5181f1b624d0b','teacher','音乐系','a@qq.com',1,0,'',0,1),
  (12,'user1','25f9e794323b453885f5181f1b624d0b','user1','English','user1@qq.com',1,0,'',0,0),
  (13,'linlin','25f9e794323b453885f5181f1b624d0b','中淋','Chinese','user2@qq.com',1,0,'',0,0),
  (14,'user1','25f9e794323b453885f5181f1b624d0b','user1','English','user1@qq.com',1,0,'',0,1),
  (15,'user2','25f9e794323b453885f5181f1b624d0b','user2','Chinese','user2@qq.com',1,0,'',0,1);

/*!40000 ALTER TABLE `mini_user` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_menu
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_menu`;

CREATE TABLE `sys_menu` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ParentId` mediumint(9) NOT NULL DEFAULT '0',
  `Title` varchar(155) NOT NULL DEFAULT '',
  `Sort` smallint(6) NOT NULL DEFAULT '0',
  `LinkInfo` varchar(255) NOT NULL DEFAULT '',
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `icon` varchar(55) NOT NULL DEFAULT '' COMMENT '分类小图标',
  `Deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`Id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='管理后台菜单';

LOCK TABLES `sys_menu` WRITE;
/*!40000 ALTER TABLE `sys_menu` DISABLE KEYS */;

INSERT INTO `sys_menu` (`Id`, `ParentId`, `Title`, `Sort`, `LinkInfo`, `Status`, `icon`, `Deleted`)
VALUES
  (1,0,'系统管理',-1,'',1,'',0),
  (2,0,'开发模块',-2,'',1,'',1),
  (5,2,'控制器管理',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:8:\"ctrllist\";}',1,'',1),
  (6,1,'菜单管理',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:8:\"menulist\";}',1,'',0),
  (7,1,'角色管理',2,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:4:\"Role\";s:1:\"a\";s:8:\"rolelist\";}',1,'',0),
  (9,2,'模型管理',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:3:\"Mod\";s:1:\"a\";s:7:\"modlist\";}',1,'',1),
  (10,1,'管理员管理',4,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:4:\"User\";s:1:\"a\";s:8:\"userlist\";}',1,'',0),
  (11,0,'课程管理',0,'',1,'',0),
  (12,11,'课程列表',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_list\";}',1,'',0),
  (13,11,'教师参与课程列表',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:12:\"teacher_list\";}',1,'',0),
  (14,11,'学生参与课程列表',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:12:\"student_list\";}',1,'',0),
  (15,1,'教师学生账号管理',3,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:9:\"user_list\";}',1,'',1),
  (16,0,'师生账号管理',0,'',1,'',0),
  (17,16,'教师账号列表',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:12:\"teacher_list\";}',1,'',0),
  (18,16,'学生账号列表',0,'a:3:{s:1:\"d\";s:5:\"admin\";s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:12:\"student_list\";}',1,'',0);

/*!40000 ALTER TABLE `sys_menu` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_role`;

CREATE TABLE `sys_role` (
  `Id` smallint(6) NOT NULL AUTO_INCREMENT,
  `Name` char(50) NOT NULL DEFAULT '' COMMENT '角色名',
  `Intro` varchar(255) NOT NULL DEFAULT '' COMMENT '描述',
  `Status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '角色状态 0:禁用,1:正常',
  `Permissions` text NOT NULL COMMENT '权限',
  `Deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`Id`),
  KEY `Name` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户角色';

LOCK TABLES `sys_role` WRITE;
/*!40000 ALTER TABLE `sys_role` DISABLE KEYS */;

INSERT INTO `sys_role` (`Id`, `Name`, `Intro`, `Status`, `Permissions`, `Deleted`)
VALUES
  (1,'系统管理员','系统管理员用户组',1,'a:9:{s:4:\"Ctrl\";a:7:{s:13:\"addcontroller\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:13:\"addcontroller\";s:1:\"d\";s:5:\"admin\";}s:9:\"addaction\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:9:\"addaction\";s:1:\"d\";s:5:\"admin\";}s:8:\"ctrllist\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:8:\"ctrllist\";s:1:\"d\";s:5:\"admin\";}s:8:\"funclist\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:8:\"funclist\";s:1:\"d\";s:5:\"admin\";}s:7:\"add_dir\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:7:\"add_dir\";s:1:\"d\";s:5:\"admin\";}s:8:\"editctrl\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:8:\"editctrl\";s:1:\"d\";s:5:\"admin\";}s:8:\"editfunc\";a:3:{s:1:\"c\";s:4:\"Ctrl\";s:1:\"a\";s:8:\"editfunc\";s:1:\"d\";s:5:\"admin\";}}s:3:\"Mod\";a:5:{s:7:\"modlist\";a:3:{s:1:\"c\";s:3:\"Mod\";s:1:\"a\";s:7:\"modlist\";s:1:\"d\";s:5:\"admin\";}s:8:\"addmodel\";a:3:{s:1:\"c\";s:3:\"Mod\";s:1:\"a\";s:8:\"addmodel\";s:1:\"d\";s:5:\"admin\";}s:7:\"addfunc\";a:3:{s:1:\"c\";s:3:\"Mod\";s:1:\"a\";s:7:\"addfunc\";s:1:\"d\";s:5:\"admin\";}s:7:\"methods\";a:3:{s:1:\"c\";s:3:\"Mod\";s:1:\"a\";s:7:\"methods\";s:1:\"d\";s:5:\"admin\";}s:7:\"add_dir\";a:3:{s:1:\"c\";s:3:\"Mod\";s:1:\"a\";s:7:\"add_dir\";s:1:\"d\";s:5:\"admin\";}}s:4:\"Menu\";a:6:{s:8:\"add_menu\";a:3:{s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:8:\"add_menu\";s:1:\"d\";s:5:\"admin\";}s:9:\"edit_menu\";a:3:{s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:9:\"edit_menu\";s:1:\"d\";s:5:\"admin\";}s:7:\"add_cat\";a:3:{s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:7:\"add_cat\";s:1:\"d\";s:5:\"admin\";}s:8:\"edit_cat\";a:3:{s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:8:\"edit_cat\";s:1:\"d\";s:5:\"admin\";}s:8:\"menulist\";a:3:{s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:8:\"menulist\";s:1:\"d\";s:5:\"admin\";}s:6:\"delete\";a:3:{s:1:\"c\";s:4:\"Menu\";s:1:\"a\";s:6:\"delete\";s:1:\"d\";s:5:\"admin\";}}s:4:\"User\";a:4:{s:8:\"userlist\";a:3:{s:1:\"c\";s:4:\"User\";s:1:\"a\";s:8:\"userlist\";s:1:\"d\";s:5:\"admin\";}s:7:\"adduser\";a:3:{s:1:\"c\";s:4:\"User\";s:1:\"a\";s:7:\"adduser\";s:1:\"d\";s:5:\"admin\";}s:9:\"edit_user\";a:3:{s:1:\"c\";s:4:\"User\";s:1:\"a\";s:9:\"edit_user\";s:1:\"d\";s:5:\"admin\";}s:6:\"delete\";a:3:{s:1:\"c\";s:4:\"User\";s:1:\"a\";s:6:\"delete\";s:1:\"d\";s:5:\"admin\";}}s:4:\"Role\";a:4:{s:8:\"rolelist\";a:3:{s:1:\"c\";s:4:\"Role\";s:1:\"a\";s:8:\"rolelist\";s:1:\"d\";s:5:\"admin\";}s:7:\"addrole\";a:3:{s:1:\"c\";s:4:\"Role\";s:1:\"a\";s:7:\"addrole\";s:1:\"d\";s:5:\"admin\";}s:8:\"editrole\";a:3:{s:1:\"c\";s:4:\"Role\";s:1:\"a\";s:8:\"editrole\";s:1:\"d\";s:5:\"admin\";}s:6:\"delete\";a:3:{s:1:\"c\";s:4:\"Role\";s:1:\"a\";s:6:\"delete\";s:1:\"d\";s:5:\"admin\";}}s:4:\"Home\";a:1:{s:5:\"index\";a:3:{s:1:\"c\";s:4:\"Home\";s:1:\"a\";s:5:\"index\";s:1:\"d\";s:5:\"admin\";}}s:3:\"Api\";a:1:{s:7:\"ueditor\";a:3:{s:1:\"c\";s:3:\"Api\";s:1:\"a\";s:7:\"ueditor\";s:1:\"d\";s:5:\"admin\";}}s:8:\"Contacts\";a:7:{s:12:\"teacher_list\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:12:\"teacher_list\";s:1:\"d\";s:5:\"admin\";}s:12:\"student_list\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:12:\"student_list\";s:1:\"d\";s:5:\"admin\";}s:8:\"add_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:8:\"add_user\";s:1:\"d\";s:5:\"admin\";}s:9:\"edit_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:9:\"edit_user\";s:1:\"d\";s:5:\"admin\";}s:6:\"delete\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:6:\"delete\";s:1:\"d\";s:5:\"admin\";}s:11:\"import_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:11:\"import_user\";s:1:\"d\";s:5:\"admin\";}s:11:\"export_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:11:\"export_user\";s:1:\"d\";s:5:\"admin\";}}s:6:\"Course\";a:12:{s:11:\"course_list\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_list\";s:1:\"d\";s:5:\"admin\";}s:10:\"course_add\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:10:\"course_add\";s:1:\"d\";s:5:\"admin\";}s:11:\"course_edit\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_edit\";s:1:\"d\";s:5:\"admin\";}s:10:\"course_del\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:10:\"course_del\";s:1:\"d\";s:5:\"admin\";}s:13:\"export_course\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:13:\"export_course\";s:1:\"d\";s:5:\"admin\";}s:19:\"course_apply_result\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:19:\"course_apply_result\";s:1:\"d\";s:5:\"admin\";}s:20:\"export_student_apply\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:20:\"export_student_apply\";s:1:\"d\";s:5:\"admin\";}s:12:\"teacher_list\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:12:\"teacher_list\";s:1:\"d\";s:5:\"admin\";}s:14:\"export_teacher\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:14:\"export_teacher\";s:1:\"d\";s:5:\"admin\";}s:12:\"student_list\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:12:\"student_list\";s:1:\"d\";s:5:\"admin\";}s:14:\"export_student\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:14:\"export_student\";s:1:\"d\";s:5:\"admin\";}s:11:\"course_sign\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_sign\";s:1:\"d\";s:5:\"admin\";}}}',0),
  (2,'hr','hr',1,'a:4:{s:4:\"Home\";a:1:{s:5:\"index\";a:3:{s:1:\"c\";s:4:\"Home\";s:1:\"a\";s:5:\"index\";s:1:\"d\";s:5:\"admin\";}}s:3:\"Api\";a:1:{s:7:\"ueditor\";a:3:{s:1:\"c\";s:3:\"Api\";s:1:\"a\";s:7:\"ueditor\";s:1:\"d\";s:5:\"admin\";}}s:8:\"Contacts\";a:7:{s:12:\"teacher_list\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:12:\"teacher_list\";s:1:\"d\";s:5:\"admin\";}s:12:\"student_list\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:12:\"student_list\";s:1:\"d\";s:5:\"admin\";}s:8:\"add_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:8:\"add_user\";s:1:\"d\";s:5:\"admin\";}s:9:\"edit_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:9:\"edit_user\";s:1:\"d\";s:5:\"admin\";}s:6:\"delete\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:6:\"delete\";s:1:\"d\";s:5:\"admin\";}s:11:\"import_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:11:\"import_user\";s:1:\"d\";s:5:\"admin\";}s:11:\"export_user\";a:3:{s:1:\"c\";s:8:\"Contacts\";s:1:\"a\";s:11:\"export_user\";s:1:\"d\";s:5:\"admin\";}}s:6:\"Course\";a:12:{s:11:\"course_list\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_list\";s:1:\"d\";s:5:\"admin\";}s:10:\"course_add\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:10:\"course_add\";s:1:\"d\";s:5:\"admin\";}s:11:\"course_edit\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_edit\";s:1:\"d\";s:5:\"admin\";}s:10:\"course_del\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:10:\"course_del\";s:1:\"d\";s:5:\"admin\";}s:13:\"export_course\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:13:\"export_course\";s:1:\"d\";s:5:\"admin\";}s:19:\"course_apply_result\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:19:\"course_apply_result\";s:1:\"d\";s:5:\"admin\";}s:20:\"export_student_apply\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:20:\"export_student_apply\";s:1:\"d\";s:5:\"admin\";}s:12:\"teacher_list\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:12:\"teacher_list\";s:1:\"d\";s:5:\"admin\";}s:14:\"export_teacher\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:14:\"export_teacher\";s:1:\"d\";s:5:\"admin\";}s:12:\"student_list\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:12:\"student_list\";s:1:\"d\";s:5:\"admin\";}s:14:\"export_student\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:14:\"export_student\";s:1:\"d\";s:5:\"admin\";}s:11:\"course_sign\";a:3:{s:1:\"c\";s:6:\"Course\";s:1:\"a\";s:11:\"course_sign\";s:1:\"d\";s:5:\"admin\";}}}',0);

/*!40000 ALTER TABLE `sys_role` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table sys_user
# ------------------------------------------------------------

DROP TABLE IF EXISTS `sys_user`;

CREATE TABLE `sys_user` (
  `Id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `UserName` char(32) NOT NULL DEFAULT '' COMMENT '用户登录id',
  `Password` char(32) NOT NULL DEFAULT '' COMMENT '用户登录密码',
  `NickName` char(20) NOT NULL DEFAULT '' COMMENT '昵称',
  `RoleId` smallint(6) NOT NULL DEFAULT '0' COMMENT '角色组ID',
  `Email` char(50) NOT NULL COMMENT '邮箱',
  `Status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '用户状态 0:禁用,1:正常',
  `LastLogTime` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最近登录时间',
  `LastLogIP` char(15) NOT NULL DEFAULT '' COMMENT '最近登录IP',
  `LogFaild` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '登陆失败次数',
  `Deleted` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  PRIMARY KEY (`Id`),
  KEY `sys_user_ibfk_2` (`RoleId`),
  CONSTRAINT `sys_user_ibfk_2` FOREIGN KEY (`RoleId`) REFERENCES `sys_role` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='用户表';

LOCK TABLES `sys_user` WRITE;
/*!40000 ALTER TABLE `sys_user` DISABLE KEYS */;

INSERT INTO `sys_user` (`Id`, `UserName`, `Password`, `NickName`, `RoleId`, `Email`, `Status`, `LastLogTime`, `LastLogIP`, `LogFaild`, `Deleted`)
VALUES
  (2,'zhangsan2','25f9e794323b453885f5181f1b624d0b','张三',1,'zhangsan2@qq.com',1,1509431305,'172.17.0.1',0,0),
  (3,'linlin','25f9e794323b453885f5181f1b624d0b','中淋',1,'linlin@qq.com',1,1509421129,'172.17.0.1',0,0),
  (4,'admin','25f9e794323b453885f5181f1b624d0b','admin',1,'test@qq.com',1,1509434395,'172.17.0.1',0,0);

/*!40000 ALTER TABLE `sys_user` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
