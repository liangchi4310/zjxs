/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : zjxs

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-01-21 13:48:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for fa_admin
-- ----------------------------
DROP TABLE IF EXISTS `fa_admin`;
CREATE TABLE `fa_admin` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(32) NOT NULL DEFAULT '' COMMENT '密码',
  `nickname` varchar(45) DEFAULT '' COMMENT '昵称',
  `salt` varchar(32) DEFAULT '' COMMENT '密码盐',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `email` varchar(60) DEFAULT '' COMMENT '电子邮箱',
  `loginfailure` tinyint(1) DEFAULT '0' COMMENT '失败次数',
  `logintime` int(11) DEFAULT NULL COMMENT '登录时间',
  `loginip` char(15) DEFAULT '' COMMENT '登录IP',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `token` varchar(60) DEFAULT '' COMMENT 'Session标识',
  `status` char(10) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `logincount` int(11) DEFAULT '0' COMMENT '登录次数',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- ----------------------------
-- Records of fa_admin
-- ----------------------------
INSERT INTO `fa_admin` VALUES ('1', 'admin', '5e27dfff315e5c83008b1e7c790f55a3', 'admin', 'sDpKOQ', '/assets/img/avatar.png', 'admin@fastadmin.net', '0', '1611206382', '127.0.0.1', null, '1611207591', 'f70fc793-a7e1-4fa3-a4df-015915427b21', 'normal', '5');

-- ----------------------------
-- Table structure for fa_admin_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_admin_log`;
CREATE TABLE `fa_admin_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(11) NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `username` varchar(45) NOT NULL DEFAULT '' COMMENT '管理员名字',
  `url` varchar(1500) NOT NULL DEFAULT '' COMMENT '操作页面',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '日志标题',
  `content` text NOT NULL COMMENT '内容',
  `ip` varchar(255) NOT NULL DEFAULT '' COMMENT 'IP',
  `useragent` varchar(255) NOT NULL DEFAULT '' COMMENT 'User-Agent',
  `createtime` int(11) DEFAULT '0' COMMENT '操作时间',
  PRIMARY KEY (`id`),
  KEY `username` (`username`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员日志表';

-- ----------------------------
-- Records of fa_admin_log
-- ----------------------------

-- ----------------------------
-- Table structure for fa_adv
-- ----------------------------
DROP TABLE IF EXISTS `fa_adv`;
CREATE TABLE `fa_adv` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '广告位id',
  `pid` int(11) NOT NULL,
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '广告标题',
  `advurl` varchar(255) NOT NULL DEFAULT '' COMMENT '路径',
  `status` char(10) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '10',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告表';

-- ----------------------------
-- Records of fa_adv
-- ----------------------------

-- ----------------------------
-- Table structure for fa_advposition
-- ----------------------------
DROP TABLE IF EXISTS `fa_advposition`;
CREATE TABLE `fa_advposition` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '广告位名称',
  `desc` varchar(255) NOT NULL DEFAULT '' COMMENT '广告位描述',
  `status` char(10) NOT NULL DEFAULT 'normal' COMMENT '状态',
  `create_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='广告位表';

-- ----------------------------
-- Records of fa_advposition
-- ----------------------------

-- ----------------------------
-- Table structure for fa_attachment
-- ----------------------------
DROP TABLE IF EXISTS `fa_attachment`;
CREATE TABLE `fa_attachment` (
  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
  `admin_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '管理员ID',
  `user_id` int(11) NOT NULL DEFAULT '0' COMMENT '会员ID',
  `url` varchar(255) NOT NULL DEFAULT '' COMMENT '物理路径',
  `imagewidth` varchar(30) NOT NULL DEFAULT '' COMMENT '宽度',
  `imageheight` varchar(30) NOT NULL DEFAULT '' COMMENT '高度',
  `imagetype` varchar(30) NOT NULL DEFAULT '' COMMENT '图片类型',
  `imageframes` int(255) NOT NULL DEFAULT '0' COMMENT '图片帧数',
  `filesize` int(11) NOT NULL DEFAULT '0' COMMENT '文件大小',
  `mimetype` varchar(100) NOT NULL DEFAULT '' COMMENT 'mime类型',
  `extparam` varchar(255) NOT NULL DEFAULT '' COMMENT '透传数据',
  `createtime` int(11) DEFAULT NULL COMMENT '创建日期',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  `uploadtime` int(11) DEFAULT NULL COMMENT '上传时间',
  `storage` varchar(100) NOT NULL DEFAULT 'local' COMMENT '存储位置',
  `sha1` varchar(40) NOT NULL DEFAULT '' COMMENT '文件 sha1编码',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='附件表';

-- ----------------------------
-- Records of fa_attachment
-- ----------------------------

-- ----------------------------
-- Table structure for fa_auth_group
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_group`;
CREATE TABLE `fa_auth_group` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(10) unsigned NOT NULL DEFAULT '0',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '组名',
  `rules` text NOT NULL COMMENT '规则ID',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `status` char(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COMMENT='角色表';

-- ----------------------------
-- Records of fa_auth_group
-- ----------------------------
INSERT INTO `fa_auth_group` VALUES ('1', '0', '超级管理员', '*', '1596786007', '1596786007', 'normal');
INSERT INTO `fa_auth_group` VALUES ('2', '1', '网站管理员', '1,12,13,14,15,16,2,9,17,18,19,20,21,10,11,4,3,5,22,23,24,25,26,6,27,28,29,7,31,32,33,30,8,34,35,36,37', '1596865550', '1596973162', 'normal');
INSERT INTO `fa_auth_group` VALUES ('3', '1', '软件管理员', '', '1596972036', '1596972036', 'normal');

-- ----------------------------
-- Table structure for fa_auth_group_access
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_group_access`;
CREATE TABLE `fa_auth_group_access` (
  `uid` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  UNIQUE KEY `uid_group_id` (`uid`,`group_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='权限分组表';

-- ----------------------------
-- Records of fa_auth_group_access
-- ----------------------------
INSERT INTO `fa_auth_group_access` VALUES ('1', '1');
INSERT INTO `fa_auth_group_access` VALUES ('2', '2');

-- ----------------------------
-- Table structure for fa_auth_rule
-- ----------------------------
DROP TABLE IF EXISTS `fa_auth_rule`;
CREATE TABLE `fa_auth_rule` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` enum('menu','file') NOT NULL DEFAULT 'file' COMMENT 'menu为菜单,file为权限节点',
  `pid` int(11) NOT NULL DEFAULT '0' COMMENT '父ID',
  `name` varchar(100) NOT NULL DEFAULT '' COMMENT '规则名称',
  `title` varchar(60) NOT NULL DEFAULT '' COMMENT '规则名称',
  `icon` varchar(50) NOT NULL DEFAULT '' COMMENT '图标',
  `condition` varchar(255) NOT NULL DEFAULT '' COMMENT '条件',
  `remark` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `ismenu` tinyint(1) NOT NULL COMMENT '是否为菜单',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  `weigh` int(11) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` char(15) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of fa_auth_rule
-- ----------------------------
INSERT INTO `fa_auth_rule` VALUES ('1', 'file', '0', 'dashboard', '控制台', 'fa fa-dashboard', '', '', '1', '1596786800', '1596786800', '146', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('2', 'file', '0', 'general', '常规管理', 'fa fa-cogs', '', '', '1', '1596786876', '1596786876', '137', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('3', 'file', '0', 'auth', '权限管理', 'fa fa-group', '', '', '1', '1596786925', '1596899902', '99', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('4', 'file', '0', 'article', '文章管理', 'fa fa-paste', '', '', '1', '1596798467', '1596899777', '123', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('5', 'file', '3', 'auth/admin', '管理员管理', 'fa fa-users', '', '', '1', '1596899890', '1596899890', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('6', 'file', '3', 'auth/adminlog', '管理员日志', 'fa fa-list-alt', '', 'Admin log tips', '1', '1596899977', '1596899977', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('7', 'file', '3', 'auth/group', '角色组', 'fa fa-group', '', 'Group tips', '1', '1596900026', '1596900026', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('8', 'file', '3', 'auth/rule', '规则管理', 'fa fa-bars', '', 'Rule tips', '1', '1596900068', '1596900068', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('9', 'file', '2', 'general/config', '系统配置', 'fa fa-cog', '', 'Config tips', '1', '1596900139', '1596900139', '60', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('10', 'file', '2', 'general/attachment', '附件管理', 'fa fa-file-image-o', '', 'Attachment tips', '1', '1596900184', '1596900184', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('11', 'file', '2', 'general/profile', '个人配置', 'fa fa-users', '', '', '1', '1596900229', '1596900229', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('12', 'file', '1', 'dashboard/index', 'View', 'fa fa-circle-o', '', '', '0', '1596900341', '1596900341', '136', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('13', 'file', '1', 'dashboard/add', 'Add', 'fa fa-circle-o', '', '', '0', '1596900426', '1596900426', '135', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('14', 'file', '1', 'dashboard/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1596900485', '1596900485', '133', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('15', 'file', '1', 'dashboard/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1596900519', '1596900536', '132', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('16', 'file', '1', 'dashboard/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1596900613', '1596900613', '132', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('17', 'file', '9', 'general/config/index', 'View', 'fa fa-circle-o', '', '', '0', '1596900657', '1596900657', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('18', 'file', '5', 'auth/admin/index', 'View', 'fa fa-circle-o', '', '', '0', '1598079285', '1598079285', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('19', 'file', '5', 'auth/admin/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598079308', '1598079308', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('20', 'file', '5', 'auth/admin/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598079366', '1598079366', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('21', 'file', '5', 'auth/admin/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598079366', '1598079366', '118', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('22', 'file', '6', 'auth/adminlog/index', 'View', 'fa fa-circle-o', '', '', '0', '1598079437', '1598079437', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('23', 'file', '6', 'auth/adminlog/detail', 'Detail', 'fa fa-circle-o', '', '', '0', '1598079437', '1598079437', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('24', 'file', '6', 'auth/adminlog/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598079437', '1598079437', '113', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('25', 'file', '7', 'auth/group/index', 'View', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('26', 'file', '7', 'auth/group/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('27', 'file', '7', 'auth/group/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('28', 'file', '7', 'auth/group/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598079598', '1598079598', '109', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('29', 'file', '8', 'auth/rule/index', 'View', 'fa fa-circle-o', '', '', '0', '1598080094', '1598080094', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('30', 'file', '8', 'auth/rule/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('31', 'file', '8', 'auth/rule/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('32', 'file', '8', 'auth/rule/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '104', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('33', 'file', '9', 'general/config/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('34', 'file', '9', 'general/config/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('35', 'file', '9', 'general/config/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080118', '1598080118', '52', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('36', 'file', '10', 'general/attachment/index', 'View', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('37', 'file', '10', 'general/attachment/select', 'Select attachment', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('38', 'file', '10', 'general/attachment/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('39', 'file', '10', 'general/attachment/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('40', 'file', '10', 'general/attachment/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('41', 'file', '10', 'general/attachment/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1598080598', '1598080598', '53', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('42', 'file', '11', 'general/profile/index', 'View', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('43', 'file', '11', 'general/profile/add', 'Add', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('44', 'file', '11', 'general/profile/edit', 'Edit', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('45', 'file', '11', 'general/profile/del', 'Delete', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('46', 'file', '11', 'general/profile/multi', 'Multi', 'fa fa-circle-o', '', '', '0', '1598080797', '1598080797', '34', 'normal');
INSERT INTO `fa_auth_rule` VALUES ('47', 'file', '11', 'general/profile/update', 'Update profile', 'fa fa-circle-o', '', '', '0', '1497429920', '1497429920', '34', 'normal');

-- ----------------------------
-- Table structure for fa_config
-- ----------------------------
DROP TABLE IF EXISTS `fa_config`;
CREATE TABLE `fa_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL DEFAULT '' COMMENT '变量名',
  `group` varchar(30) NOT NULL DEFAULT '' COMMENT '分组',
  `title` varchar(100) NOT NULL DEFAULT '' COMMENT '变量标题',
  `tip` varchar(255) NOT NULL DEFAULT '' COMMENT '变量描述',
  `type` varchar(30) NOT NULL DEFAULT '' COMMENT '类型:string,text,int,bool,array,datetime,date,file',
  `value` text NOT NULL COMMENT '变量值',
  `content` text NOT NULL COMMENT '变量字典数据',
  `rule` varchar(100) NOT NULL DEFAULT '' COMMENT '验证规则',
  `extend` varchar(255) NOT NULL DEFAULT '' COMMENT '扩展属性',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COMMENT='系统配置';

-- ----------------------------
-- Records of fa_config
-- ----------------------------
INSERT INTO `fa_config` VALUES ('1', 'web_name', 'basic', '网站标题', '网站标题前台显示标题', 'string', '正脊先生', '', 'required', '');
INSERT INTO `fa_config` VALUES ('2', 'web_desc', 'basic', '网站描述', '网站搜索引擎描述', 'string', '', '', '', '');
INSERT INTO `fa_config` VALUES ('3', 'configgroup', 'dictionary', '配置分组', '', 'array', '{\"basic\":\"Basic\",\"email\":\"Email\",\"dictionary\":\"Dictionary\",\"user\":\"User\",\"example\":\"Example\"}', '', '', '');
INSERT INTO `fa_config` VALUES ('4', 'web_key', 'basic', '网站关键字', '网站搜索引擎关键字', 'string', '', '', '', '');
INSERT INTO `fa_config` VALUES ('5', 'categorytype', 'dictionary', '分类类型', '', 'array', '{\"default\":\"Default\",\"page\":\"Page\",\"article\":\"Article\",\"test\":\"Test\"}', '', '', '');
INSERT INTO `fa_config` VALUES ('6', 'cdnurl', 'basic', 'Cdn url', '如果静态资源使用第三方云储存请配置该值', 'string', '', '', '', '');
INSERT INTO `fa_config` VALUES ('7', 'version', 'basic', '版本号', '如果静态资源有变动请重新配置该值', 'string', '1.0.1', '', 'required', '');
INSERT INTO `fa_config` VALUES ('8', 'timezone', 'basic', '时区', '', 'string', 'Asia/Shanghai', '', 'required', '');
INSERT INTO `fa_config` VALUES ('9', 'forbiddenip', 'basic', '禁止IP', '一行一条记录', 'text', '', '', '', '');
INSERT INTO `fa_config` VALUES ('10', 'languages', 'basic', '模块语言', '', 'array', '{\"backend\":\"zh-cn\",\"frontend\":\"zh-cn\"}', '', '', '');
INSERT INTO `fa_config` VALUES ('11', 'web_close', 'basic', '关闭站点', '关闭站点', 'switch', '1', '', '', '');
INSERT INTO `fa_config` VALUES ('12', 'web_logo', 'basic', '网站logo', '', 'image', '', '', '', '');

-- ----------------------------
-- Table structure for fa_cart
-- ----------------------------
DROP TABLE IF EXISTS `fa_cart`;
CREATE TABLE `fa_cart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned DEFAULT '0' COMMENT '用户表id',
  `proid` int(11) DEFAULT '0' COMMENT '商品id',
  `quantity` int(11) DEFAULT '0' COMMENT '数量',
  `checked` tinyint(1) DEFAULT NULL COMMENT '是否选择,1=已勾选,0=未勾选',
  `createtime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '创建时间',
  `updatetime` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='购物车表';


-- ----------------------------
-- Table structure for fa_category
-- ----------------------------
DROP TABLE IF EXISTS `fa_category`;
CREATE TABLE `fa_category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `pid` int(11) NOT NULL COMMENT '父类别id当id=0时说明是根节点,一级类别',
  `name` varchar(30) NOT NULL COMMENT '类别名称',
  `nickname` varchar(100) DEFAULT '',
  `image` varchar(100) NOT NULL DEFAULT '' COMMENT '图片',
  `type` tinyint(1) NOT NULL COMMENT '栏目类型 1 文章 2 商城',
  `keywords` varchar(60) DEFAULT '' COMMENT '关键字',
  `description` varchar(120) DEFAULT '' COMMENT '描述',
  `status` tinyint(1) NOT NULL DEFAULT '1' COMMENT '类别状态1-正常,2-已废弃',
  `sort` int(11) NOT NULL DEFAULT '0' COMMENT '排序编号',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='分类表';

-- ----------------------------
-- Table structure for fa_doctor_users
-- ----------------------------
DROP TABLE IF EXISTS `fa_doctor_users`;
CREATE TABLE `fa_doctor_users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(30) NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(45) NOT NULL DEFAULT '' COMMENT '密码',
  `openid` varchar(30) DEFAULT '' COMMENT '第三方登录id',
  `opentype` tinyint(4) DEFAULT NULL COMMENT '1 qq 2微信 3支付宝',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='医生用户表';

-- ----------------------------
-- Table structure for fa_ems
-- ----------------------------
DROP TABLE IF EXISTS `fa_ems`;
CREATE TABLE `fa_ems` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `event` varchar(30) NOT NULL DEFAULT '' COMMENT '事件',
  `email` varchar(60) NOT NULL DEFAULT '' COMMENT '邮箱',
  `code` varchar(10) NOT NULL DEFAULT '' COMMENT '验证码',
  `times` int(11) unsigned DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(15) DEFAULT '' COMMENT 'IP',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮箱验证码表';


-- ----------------------------
-- Table structure for fa_orderitem
-- ----------------------------
DROP TABLE IF EXISTS `fa_orderitem`;
CREATE TABLE `fa_orderitem` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `orderid` int(11) DEFAULT NULL COMMENT '订单ID',
  `uid` int(11) DEFAULT NULL COMMENT '用户id',
  `proid` int(11) DEFAULT NULL COMMENT '商品id',
  `gprice` decimal(10,2) DEFAULT '0.00' COMMENT '生成订单时的商品单价',
  `quantity` int(255) DEFAULT '1' COMMENT '商品数量',
  `totalprice` decimal(10,2) DEFAULT '0.00' COMMENT '商品总价',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单明细表';

-- ----------------------------
-- Table structure for fa_orders
-- ----------------------------
DROP TABLE IF EXISTS `fa_orders`;
CREATE TABLE `fa_orders` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='订单表';

-- ----------------------------
-- Table structure for fa_product
-- ----------------------------
DROP TABLE IF EXISTS `fa_product`;
CREATE TABLE `fa_product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cid` int(11) DEFAULT NULL COMMENT '类别Id',
  `name` varchar(255) NOT NULL COMMENT '商品名称',
  `subtitle` varchar(255) DEFAULT '' COMMENT '商品副标题',
  `detail` varchar(255) DEFAULT NULL COMMENT '商品详情',
  `price` decimal(10,2) DEFAULT '0.00' COMMENT '价格,单位-元保留两位小数',
  `stock` int(11) DEFAULT '0' COMMENT '库存数量',
  `status` tinyint(1) DEFAULT '1' COMMENT '商品状态.1-在售 2-下架 3-删除',
  `createtime` int(11) DEFAULT NULL,
  `updatetime` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='商品表';

-- ----------------------------
-- Table structure for fa_sms
-- ----------------------------
DROP TABLE IF EXISTS `fa_sms`;
CREATE TABLE `fa_sms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `event` varchar(30) NOT NULL COMMENT '事件',
  `mobile` varchar(15) NOT NULL COMMENT '手机号',
  `code` varchar(10) NOT NULL COMMENT '验证码',
  `times` int(10) unsigned DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(15) DEFAULT NULL COMMENT 'ip',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='短信验证码表';

-- ----------------------------
-- Table structure for fa_user
-- ----------------------------
DROP TABLE IF EXISTS `fa_user`;
CREATE TABLE `fa_user` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `
username` varchar(45) DEFAULT '' COMMENT '用户名',
  `password` varchar(60) DEFAULT '' COMMENT '密码',
  `nickname` varchar(60) DEFAULT NULL COMMENT '昵称',
  `
mobile` varchar(11) DEFAULT '' COMMENT '手机号',
  `
email` varchar(32) DEFAULT '' COMMENT '邮箱',
  `avatar` varchar(255) DEFAULT '' COMMENT '头像',
  `level` tinyint(1) unsigned DEFAULT '0' COMMENT '等级',
  `gender` tinyint(1) unsigned DEFAULT '0' COMMENT '性别',
  `birthday` date DEFAULT NULL COMMENT '生日',
  `bio` varchar(150) DEFAULT '' COMMENT '格言',
  `money` decimal(10,2) unsigned DEFAULT '0.00' COMMENT '余额',
  `score` int(11) DEFAULT '0' COMMENT '积分',
  `successions` int(11) unsigned DEFAULT '1' COMMENT '连续登录天数',
  `maxsuccessions` int(10) unsigned DEFAULT '1' COMMENT '最大连续登录天数',
  `prevtime` int(11) DEFAULT NULL COMMENT '上次登录时间',
  `logintime` int(11) DEFAULT NULL COMMENT '登录时间',
  `loginip` varchar(15) DEFAULT '' COMMENT '登录IP',
  `status` tinyint(1) DEFAULT NULL COMMENT '状态',
  `openid` varchar(60) DEFAULT '' COMMENT '用户openid',
  `last_sign_time` int(11) DEFAULT NULL COMMENT '上次签到时间',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  `updatetime` int(11) DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- ----------------------------
-- Table structure for fa_user_money_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_money_log`;
CREATE TABLE `fa_user_money_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL COMMENT '会员ID',
  `money` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更余额',
  `before` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更前余额',
  `after` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT '变更后余额',
  `memo` varchar(255) NOT NULL DEFAULT '' COMMENT '备注',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员余额变动表';

-- ----------------------------
-- Table structure for fa_user_score_log
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_score_log`;
CREATE TABLE `fa_user_score_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `uid` int(10) unsigned NOT NULL,
  `score` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '变更积分',
  `before` int(11) NOT NULL DEFAULT '0' COMMENT '变更前积分',
  `after` int(11) NOT NULL DEFAULT '0' COMMENT '变更后积分',
  `memo` varchar(255) DEFAULT '' COMMENT '备注',
  `createtime` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='会员积分变动表';

-- ----------------------------
-- Table structure for fa_user_token
-- ----------------------------
DROP TABLE IF EXISTS `fa_user_token`;
CREATE TABLE `fa_user_token` (
  `token` varchar(50) NOT NULL COMMENT 'Token',
  `uid` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '会员ID',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  `expiretime` int(10) DEFAULT NULL COMMENT '过期时间',
  PRIMARY KEY (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='会员Token表';

-- ----------------------------
-- Table structure for fa_version
-- ----------------------------
DROP TABLE IF EXISTS `fa_version`;
CREATE TABLE `fa_version` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `oldversion` varchar(30) NOT NULL DEFAULT '' COMMENT '旧版本号',
  `newversion` varchar(30) NOT NULL DEFAULT '' COMMENT '新版本号',
  `packagesize` varchar(30) NOT NULL DEFAULT '' COMMENT '包大小',
  `content` varchar(500) NOT NULL DEFAULT '' COMMENT '升级内容',
  `downloadurl` varchar(255) NOT NULL DEFAULT '' COMMENT '下载地址',
  `enforce` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '强制更新',
  `createtime` int(10) NOT NULL DEFAULT '0' COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  `weigh` int(10) NOT NULL DEFAULT '0' COMMENT '权重',
  `status` varchar(30) NOT NULL DEFAULT '' COMMENT '状态',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT COMMENT='版本表';

