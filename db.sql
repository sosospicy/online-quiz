-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- 主机： localhost
-- 生成日期： 2022-11-10 13:59:30
-- 服务器版本： 8.0.30
-- PHP 版本： 8.1.9

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `quiz`
--

-- --------------------------------------------------------

--
-- 表的结构 `exams`
--

CREATE TABLE `exams` (
  `id` int UNSIGNED NOT NULL COMMENT '主键 自增',
  `code` varchar(256) COLLATE utf8mb4_general_ci NOT NULL COMMENT '测验编号',
  `title` varchar(256) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '考卷标题',
  `question_list` varchar(1024) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '题目列表 json',
  `created_by` int UNSIGNED NOT NULL COMMENT '创建者',
  `is_active` tinyint NOT NULL DEFAULT '1' COMMENT '是否有效',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='考卷表';

-- --------------------------------------------------------

--
-- 表的结构 `options`
--

CREATE TABLE `options` (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键 自增',
  `question_id` bigint UNSIGNED NOT NULL COMMENT '所属题目ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '选项内容',
  `is_right` tinyint NOT NULL DEFAULT '0' COMMENT '是否为正确答案，0 否  1 是',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='选项表';

--
-- 转存表中的数据 `options`
--

INSERT INTO `options` (`id`, `question_id`, `content`, `is_right`, `updated_at`) VALUES
(5, 1, 'Good luck', 1, '2022-11-04 06:16:08'),
(6, 1, 'Never mind', 0, '2022-11-04 06:16:08'),
(7, 1, 'Yes, please', 0, '2022-11-04 06:16:08'),
(8, 1, 'It\'s nothing', 0, '2022-11-04 06:16:08'),
(9, 2, 'I\'m not Mary.', 0, '2022-11-04 06:18:13'),
(10, 2, 'Who are you?', 0, '2022-11-04 06:18:13'),
(11, 2, 'Speaking.', 1, '2022-11-04 06:18:13'),
(12, 2, 'Mary is well today.', 0, '2022-11-04 06:18:13'),
(13, 3, 'It was a long time.', 0, '2022-11-04 06:32:08'),
(14, 3, 'Two weeks ago.', 0, '2022-11-04 06:32:08'),
(15, 3, 'No. Only a couple of days.', 1, '2022-11-04 06:32:08'),
(16, 3, 'Not long time ago.', 0, '2022-11-04 06:32:08'),
(17, 4, 'harm', 0, '2022-11-04 06:34:18'),
(18, 4, 'angry', 0, '2022-11-04 06:34:18'),
(19, 4, 'until', 0, '2022-11-04 06:34:18'),
(20, 4, 'under', 0, '2022-11-04 06:34:18'),
(21, 4, 'paid', 1, '2022-11-04 06:34:18'),
(27, 5, 'Capital adequacy', 1, '2022-11-04 06:41:31'),
(28, 5, 'Asset quality', 1, '2022-11-04 06:41:31'),
(29, 5, 'Management ability', 1, '2022-11-04 06:41:31'),
(30, 5, 'Earning performance', 1, '2022-11-04 06:41:31'),
(31, 5, 'Liquidity', 1, '2022-11-04 06:41:31'),
(32, 6, 'open an account', 1, '2022-11-04 06:43:58'),
(33, 6, 'deposit money', 1, '2022-11-04 06:43:58'),
(34, 6, 'transfer money', 0, '2022-11-04 06:43:58'),
(35, 6, 'settlement', 0, '2022-11-04 06:43:58'),
(36, 6, 'withdraw money', 1, '2022-11-04 06:43:58'),
(37, 6, 'Account Closing', 1, '2022-11-04 06:43:58'),
(38, 7, 'risk identified', 0, '2022-11-04 06:46:01'),
(39, 7, 'behavior control', 1, '2022-11-04 06:46:01'),
(40, 7, 'assessment and authorization', 1, '2022-11-04 06:46:01'),
(41, 7, 'Information Exchange', 0, '2022-11-04 06:46:01'),
(42, 8, 'Deposits', 0, '2022-11-04 06:51:15'),
(43, 8, 'Trust Consulting', 1, '2022-11-04 06:51:15'),
(44, 8, 'L/C', 1, '2022-11-04 06:51:15'),
(45, 8, 'Agency', 1, '2022-11-04 06:51:15'),
(46, 9, 'The relative noun serves as a \"link\" between the relative clause and its antecedent. It performs two functions: showing concord with its antecedent and indicating its function within the relative clause.', 1, '2022-11-04 06:59:30'),
(47, 10, 'When tense points to the temporal location of an event or a state of affairs, aspect \"reflects the way in which the verb action is regarded or experienced with respect to time\".', 1, '2022-11-04 06:59:30');

-- --------------------------------------------------------

--
-- 表的结构 `questions`
--

CREATE TABLE `questions` (
  `id` bigint UNSIGNED NOT NULL COMMENT '主键,自增',
  `type` enum('radio','checkbox','textarea') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'radio' COMMENT '题目类型：单选radio；多选checkbox；简答textarea',
  `category_id` int NOT NULL DEFAULT '0' COMMENT '类别，预留字段',
  `title` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '题目',
  `created_by` int NOT NULL DEFAULT '0' COMMENT '创建者ID',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `is_active` tinyint NOT NULL DEFAULT '1' COMMENT '是否有效',
  `score` int UNSIGNED NOT NULL COMMENT '题目分值'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='题目表';

--
-- 转存表中的数据 `questions`
--

INSERT INTO `questions` (`id`, `type`, `category_id`, `title`, `created_by`, `created_at`, `updated_at`, `is_active`, `score`) VALUES
(1, 'radio', 0, '- We\'ll have a basketball match this afternoon. \r\n- ________.', 0, '2022-11-04 06:14:40', '2022-11-04 06:14:40', 1, 5),
(2, 'radio', 0, '- Is John there?\r\n- _________', 0, '2022-11-04 06:17:04', '2022-11-04 06:17:04', 1, 10),
(3, 'radio', 0, '- Are you going on holiday for a long time?\r\n- _________', 0, '2022-11-04 06:31:11', '2022-11-04 06:31:11', 1, 5),
(4, 'radio', 0, 'One night there was a heavy snow, and in the morning Mr Smith\'s garden was full of snow. Mr Smith wanted to take his car out, so he ______ a man to clean the road from his garage to his gate. ', 0, '2022-11-04 06:33:19', '2022-11-04 06:33:19', 1, 8),
(5, 'checkbox', 0, 'CAMEL Rating System includes：', 0, '2022-11-04 06:39:46', '2022-11-04 06:39:46', 1, 10),
(6, 'checkbox', 0, 'What are the several steps of Savings deposits?', 0, '2022-11-04 06:42:04', '2022-11-04 06:42:04', 1, 12),
(7, 'checkbox', 0, 'The typical measures of internal control included:', 0, '2022-11-04 06:44:38', '2022-11-04 06:44:38', 1, 10),
(8, 'checkbox', 0, 'The followings can be classified as off-balance sheet business are:', 0, '2022-11-04 06:50:25', '2022-11-04 06:50:25', 1, 8),
(9, 'textarea', 0, 'What\'s the function of relative pronoun?', 0, '2022-11-04 06:55:29', '2022-11-04 06:55:29', 1, 15),
(10, 'textarea', 0, 'If tense is related to time, what is aspect related to?', 0, '2022-11-04 06:55:29', '2022-11-04 06:55:29', 1, 20);

-- --------------------------------------------------------

--
-- 表的结构 `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int UNSIGNED NOT NULL COMMENT '主键 自增',
  `exam_code` varchar(256) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '试卷code',
  `score` int UNSIGNED NOT NULL COMMENT '分数',
  `failed_question_list` varchar(2048) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '错题ID json',
  `submitted_by` int UNSIGNED NOT NULL COMMENT '提交者',
  `submitted_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '提交时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='考试记录'; 


-- --------------------------------------------------------

--
-- 表的结构 `users`
--

CREATE TABLE `users` (
  `id` int NOT NULL,
  `reference_id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL COMMENT '第三方账户ID',
  `source` enum('google','facebook','local') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'local' COMMENT '登录来源，google|facebook|local',
  `name` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `password` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `customer_id` varchar(256) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'stripe中顾客标识id',
  `subscription_id` varchar(256) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订阅ID',
  `subscription_status` varchar(128) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '订阅状态 active trialing incomplete等'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;



--
-- 转储表的索引
--

--
-- 表的索引 `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_code` (`code`);

--
-- 表的索引 `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_question` (`question_id`);

--
-- 表的索引 `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- 表的索引 `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_exam_user` (`exam_code`,`submitted_by`);

--
-- 表的索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_subscription_id` (`subscription_id`),
  ADD UNIQUE KEY `uniq_customer_id` (`customer_id`),
  ADD UNIQUE KEY `uniq_email` (`email`) USING BTREE;

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `exams`
--
ALTER TABLE `exams`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键 自增', AUTO_INCREMENT=9;

--
-- 使用表AUTO_INCREMENT `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键 自增', AUTO_INCREMENT=48;

--
-- 使用表AUTO_INCREMENT `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键,自增', AUTO_INCREMENT=11;

--
-- 使用表AUTO_INCREMENT `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键 自增', AUTO_INCREMENT=5;

--
-- 使用表AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

  --
-- 表的结构 `email_verification`
--

CREATE TABLE `email_verification` (
  `id` int UNSIGNED NOT NULL COMMENT '主键',
  `code` varchar(128) COLLATE utf8mb4_general_ci NOT NULL COMMENT '验证码',
  `data` varchar(1024) COLLATE utf8mb4_general_ci NOT NULL COMMENT '用户注册数据json',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `is_verified` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否已验证'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='注册邮件验证';

--
-- 转储表的索引
--

--
-- 表的索引 `email_verification`
--
ALTER TABLE `email_verification`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_code` (`code`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `email_verification`
--
ALTER TABLE `email_verification`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键';
  
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
