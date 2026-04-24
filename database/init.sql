-- 数据标注任务发布系统数据库初始化脚本
-- 数据库: biaozhu
-- 字符集: utf8mb4

CREATE DATABASE IF NOT EXISTS `biaozhu` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `biaozhu`;

-- 1. 用户表
CREATE TABLE IF NOT EXISTS `bz_users` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL COMMENT '用户名',
    `password` VARCHAR(255) NOT NULL COMMENT '密码',
    `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
    `email` VARCHAR(100) DEFAULT NULL COMMENT '邮箱',
    `phone` VARCHAR(20) DEFAULT NULL COMMENT '手机号',
    `avatar` VARCHAR(255) DEFAULT NULL COMMENT '头像',
    `balance` DECIMAL(10,2) DEFAULT 0.00 COMMENT '累计收益',
    `pending_balance` DECIMAL(10,2) DEFAULT 0.00 COMMENT '待入账收益',
    `education_status` TINYINT DEFAULT 0 COMMENT '教育经历状态: 0未完善,1已完善',
    `work_status` TINYINT DEFAULT 0 COMMENT '工作经历状态: 0未完善,1已完善',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1正常,0禁用',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    UNIQUE KEY `uk_username` (`username`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户表';

-- 2. 用户教育经历表
CREATE TABLE IF NOT EXISTS `bz_user_education` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `school` VARCHAR(100) NOT NULL COMMENT '学校名称',
    `major` VARCHAR(100) NOT NULL COMMENT '专业',
    `degree` VARCHAR(20) NOT NULL COMMENT '学历: 大专,本科,硕士,博士',
    `start_year` INT COMMENT '入学年份',
    `end_year` INT COMMENT '毕业年份',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户教育经历表';

-- 3. 用户工作经历表
CREATE TABLE IF NOT EXISTS `bz_user_work` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `company` VARCHAR(100) NOT NULL COMMENT '公司名称',
    `position` VARCHAR(100) NOT NULL COMMENT '职位',
    `description` TEXT COMMENT '工作描述',
    `start_date` DATE COMMENT '开始日期',
    `end_date` DATE COMMENT '结束日期',
    `is_current` TINYINT DEFAULT 0 COMMENT '是否当前工作: 1是,0否',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户工作经历表';

-- 4. 任务分类表
CREATE TABLE IF NOT EXISTS `bz_task_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL COMMENT '分类名称',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1显示,0隐藏',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务分类表';

-- 5. 任务表
CREATE TABLE IF NOT EXISTS `bz_tasks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(200) NOT NULL COMMENT '任务名称',
    `category_id` INT UNSIGNED DEFAULT 0 COMMENT '分类ID',
    `price` DECIMAL(10,2) NOT NULL COMMENT '任务价格',
    `description` TEXT COMMENT '任务描述',
    `requirements` TEXT COMMENT '招募要求',
    `process` TEXT COMMENT '申请流程说明',
    `dingtalk_qrcode` VARCHAR(255) DEFAULT NULL COMMENT '钉钉群二维码',
    `tags` VARCHAR(500) DEFAULT NULL COMMENT '标签,逗号分隔',
    `total_count` INT UNSIGNED DEFAULT 0 COMMENT '总名额',
    `applied_count` INT UNSIGNED DEFAULT 0 COMMENT '已申请人数',
    `view_count` INT UNSIGNED DEFAULT 0 COMMENT '浏览量(热度)',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1招募中,2进行中,3已结束',
    `start_time` INT UNSIGNED DEFAULT 0 COMMENT '开始时间',
    `end_time` INT UNSIGNED DEFAULT 0 COMMENT '结束时间',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    KEY `idx_category_id` (`category_id`),
    KEY `idx_status` (`status`),
    KEY `idx_create_time` (`create_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务表';

-- 6. 用户任务申请表
CREATE TABLE IF NOT EXISTS `bz_user_tasks` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `task_id` INT UNSIGNED NOT NULL COMMENT '任务ID',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1待提交,2待审核,3通过,4拒绝',
    `submit_time` INT UNSIGNED DEFAULT 0 COMMENT '提交时间',
    `audit_time` INT UNSIGNED DEFAULT 0 COMMENT '审核时间',
    `audit_remark` VARCHAR(500) DEFAULT NULL COMMENT '审核备注',
    `earnings` DECIMAL(10,2) DEFAULT 0.00 COMMENT '获得收益',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    UNIQUE KEY `uk_user_task` (`user_id`, `task_id`),
    KEY `idx_user_id` (`user_id`),
    KEY `idx_task_id` (`task_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户任务申请表';

-- 7. 收益明细表
CREATE TABLE IF NOT EXISTS `bz_earnings` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `user_task_id` INT UNSIGNED DEFAULT 0 COMMENT '用户任务ID',
    `type` TINYINT DEFAULT 1 COMMENT '类型: 1任务收益,2推荐奖励,3其他',
    `amount` DECIMAL(10,2) NOT NULL COMMENT '金额',
    `description` VARCHAR(200) DEFAULT NULL COMMENT '描述',
    `status` TINYINT DEFAULT 0 COMMENT '状态: 0待入账,1已入账',
    `settle_time` INT UNSIGNED DEFAULT 0 COMMENT '结算时间',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    KEY `idx_user_id` (`user_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='收益明细表';

-- 8. FAQ分类表
CREATE TABLE IF NOT EXISTS `bz_faq_categories` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(50) NOT NULL COMMENT '分类名称',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1显示,0隐藏',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='FAQ分类表';

-- 9. FAQ表
CREATE TABLE IF NOT EXISTS `bz_faqs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT UNSIGNED DEFAULT 0 COMMENT '分类ID',
    `question` VARCHAR(500) NOT NULL COMMENT '问题',
    `answer` TEXT NOT NULL COMMENT '答案',
    `view_count` INT UNSIGNED DEFAULT 0 COMMENT '浏览量',
    `sort` INT DEFAULT 0 COMMENT '排序',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1显示,0隐藏',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    KEY `idx_category_id` (`category_id`),
    KEY `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='FAQ表';

-- 10. 任务评论表
CREATE TABLE IF NOT EXISTS `bz_task_comments` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `task_id` INT UNSIGNED NOT NULL COMMENT '任务ID',
    `user_id` INT UNSIGNED NOT NULL COMMENT '用户ID',
    `content` TEXT NOT NULL COMMENT '评论内容',
    `parent_id` INT UNSIGNED DEFAULT 0 COMMENT '父评论ID',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1显示,0隐藏',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    KEY `idx_task_id` (`task_id`),
    KEY `idx_user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='任务评论表';

-- 11. 管理员表
CREATE TABLE IF NOT EXISTS `bz_admins` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `username` VARCHAR(50) NOT NULL COMMENT '用户名',
    `password` VARCHAR(255) NOT NULL COMMENT '密码',
    `nickname` VARCHAR(50) DEFAULT NULL COMMENT '昵称',
    `status` TINYINT DEFAULT 1 COMMENT '状态: 1正常,0禁用',
    `last_login_time` INT UNSIGNED DEFAULT 0 COMMENT '最后登录时间',
    `last_login_ip` VARCHAR(50) DEFAULT NULL COMMENT '最后登录IP',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    UNIQUE KEY `uk_username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- 12. 系统配置表
CREATE TABLE IF NOT EXISTS `bz_configs` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `key` VARCHAR(100) NOT NULL COMMENT '配置键名',
    `value` TEXT COMMENT '配置值',
    `description` VARCHAR(200) DEFAULT NULL COMMENT '配置描述',
    `create_time` INT UNSIGNED DEFAULT 0,
    `update_time` INT UNSIGNED DEFAULT 0,
    UNIQUE KEY `uk_key` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='系统配置表';

-- 插入初始数据
-- 管理员账号: admin / admin123
INSERT INTO `bz_admins` (`username`, `password`, `nickname`, `status`, `create_time`) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '超级管理员', 1, UNIX_TIMESTAMP());

-- 任务分类
INSERT INTO `bz_task_categories` (`name`, `sort`, `status`, `create_time`) VALUES 
('图像标注', 1, 1, UNIX_TIMESTAMP()),
('语音标注', 2, 1, UNIX_TIMESTAMP()),
('文本标注', 3, 1, UNIX_TIMESTAMP()),
('数据采集', 4, 1, UNIX_TIMESTAMP()),
('其他任务', 5, 1, UNIX_TIMESTAMP());

-- FAQ分类
INSERT INTO `bz_faq_categories` (`name`, `sort`, `status`, `create_time`) VALUES 
('新手入门', 1, 1, UNIX_TIMESTAMP()),
('任务相关', 2, 1, UNIX_TIMESTAMP()),
('收益结算', 3, 1, UNIX_TIMESTAMP()),
('账号问题', 4, 1, UNIX_TIMESTAMP());

-- FAQ示例
INSERT INTO `bz_faqs` (`category_id`, `question`, `answer`, `sort`, `status`, `create_time`) VALUES 
(1, '如何开始做任务？', '1. 注册并完善个人信息；2. 在任务广场浏览任务；3. 点击任务详情，查看招募要求；4. 点击申请按钮，加入钉钉群获取任务详情。', 1, 1, UNIX_TIMESTAMP()),
(1, '完善个人信息有什么用？', '完善教育经历和工作经历可以让我们更好地为您推荐合适的任务，提高您的申请通过率。', 2, 1, UNIX_TIMESTAMP()),
(2, '任务状态有哪些？', '任务状态包括：待提交（已申请但未提交作品）、待审核（已提交等待审核）、通过审核（审核通过）、已拒绝（审核未通过）。', 1, 1, UNIX_TIMESTAMP()),
(3, '收益如何结算？', '任务审核通过后，收益会先进入待入账状态，每月15日统一结算上月通过审核的任务收益，结算后可提现。', 1, 1, UNIX_TIMESTAMP()),
(4, '忘记密码怎么办？', '请联系管理员或通过注册邮箱/手机号找回密码。', 1, 1, UNIX_TIMESTAMP());

-- 系统配置
INSERT INTO `bz_configs` (`key`, `value`, `description`, `create_time`) VALUES 
('site_name', '数据标注平台', '网站名称', UNIX_TIMESTAMP()),
('site_description', '专业的数据标注任务发布平台', '网站描述', UNIX_TIMESTAMP()),
('dingtalk_default_qrcode', '', '默认钉钉群二维码', UNIX_TIMESTAMP()),
('settle_day', '15', '每月结算日', UNIX_TIMESTAMP());
