-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 10, 2023 at 02:17 PM
-- Server version: 10.3.34-MariaDB-0ubuntu0.20.04.1
-- PHP Version: 7.4.3

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `demo_alp_school_management_1`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_log`
--

CREATE TABLE `activity_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `activity_log` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_calculated_difficulty`
--

CREATE TABLE `ai_calculated_difficulty` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `difficulty_level` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ai_calibration_report`
--

CREATE TABLE `ai_calibration_report` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_calibration` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calibration_number` bigint(20) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `school_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `test_type` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `included_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `excluded_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `included_student_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `median_calibration_difficulties` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `median_student_ability` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calibration_constant` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_question_difficulties` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calibrated_question_difficulties` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `current_student_ability` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `calibrated_student_ability` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `median_calibration_ability` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_data` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `median_difficulty_levels` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `standard_deviation_difficulty_levels` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `update_exclude_question_difficulty` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','complete','adjusted') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `answer`
--

CREATE TABLE `answer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) NOT NULL,
  `answer1_en` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer2_en` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer3_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer4_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer1_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer2_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer3_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer4_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer1_ch` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer2_ch` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer3_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer4_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer1_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer2_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer3_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `hint_answer4_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer1_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer2_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer3_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer4_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer1_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer2_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer3_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_hint_answer4_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `answer1_node_relation_id_en` bigint(20) DEFAULT 0,
  `answer2_node_relation_id_en` bigint(20) DEFAULT 0,
  `answer3_node_relation_id_en` bigint(20) DEFAULT 0,
  `answer4_node_relation_id_en` bigint(20) DEFAULT 0,
  `answer1_node_relation_id_ch` bigint(20) DEFAULT 0,
  `answer2_node_relation_id_ch` bigint(20) DEFAULT 0,
  `answer3_node_relation_id_ch` bigint(20) DEFAULT 0,
  `answer4_node_relation_id_ch` bigint(20) DEFAULT 0,
  `correct_answer_en` bigint(20) UNSIGNED DEFAULT NULL,
  `correct_answer_ch` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attempt_exams`
--

CREATE TABLE `attempt_exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `calibration_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `language` enum('en','ch') COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_answers` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempt_first_trial` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempt_second_trial` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attempt_wrong_answer` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `total_correct_answers` bigint(20) UNSIGNED DEFAULT NULL,
  `total_wrong_answers` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_taking_timing` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_ability` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `server_details` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `before_exam_survey` tinyint(4) DEFAULT NULL COMMENT '1-sad 2-Happy',
  `after_exam_survey` tinyint(4) DEFAULT NULL COMMENT '1-sad 2-Happy',
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `attempt_exam_student_mapping`
--

CREATE TABLE `attempt_exam_student_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logged_user_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `log_name` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `log_payload` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `table_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `child_table_name` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `page_name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ip_address` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `calibration_question_log`
--

CREATE TABLE `calibration_question_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `calibration_report_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `seed_question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `previous_ai_difficulty` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calibration_difficulty` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `change_difference` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `median_of_difficulty_level` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question_log_type` enum('include','exclude') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_assignment_students`
--

CREATE TABLE `class_assignment_students` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `student_id` int(11) DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_promotion_history`
--

CREATE TABLE `class_promotion_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promoted_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promoted_class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `promoted_by_userid` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `class_subject_mapping`
--

CREATE TABLE `class_subject_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `class_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_years`
--

CREATE TABLE `curriculum_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `year` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `curriculum_year_student_mapping`
--

CREATE TABLE `curriculum_year_student_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_number_within_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_student_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam`
--

CREATE TABLE `exam` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `calibration_id` bigint(20) UNSIGNED DEFAULT NULL,
  `use_of_mode` tinyint(4) DEFAULT NULL COMMENT '1 = As a Test/Exercise, 2 = As a Collection of Questions',
  `parent_exam_id` int(11) DEFAULT NULL,
  `exam_type` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '1 = Self-Learning, 2 = Excercise, 3 = Test',
  `reference_no` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_id` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `from_date` datetime NOT NULL,
  `to_date` datetime NOT NULL,
  `start_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `end_time` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `report_type` enum('end_date','after_submit','custom_date') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `result_date` date DEFAULT NULL,
  `publish_date` timestamp NULL DEFAULT NULL,
  `time_duration` int(11) DEFAULT NULL COMMENT 'total_no_seconds',
  `description` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `peer_group_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_group_test` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 = yes, 0 = no',
  `template_id` bigint(20) DEFAULT NULL,
  `self_learning_test_type` enum('1','2') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '1-Excercise,2-Test',
  `no_of_trials_per_question` int(11) DEFAULT NULL,
  `difficulty_mode` enum('manual','auto') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_levels` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_hints` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_full_solution` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `display_pr_answer_hints` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `randomize_answer` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `randomize_order` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_objectives_configuration` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `stage_ids` int(11) NOT NULL DEFAULT 4,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by_user` enum('super_admin','school_admin','principal','panel_head','co_ordinator','teacher','student') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `assign_school_status` enum('draft','send_to_school') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `status` enum('draft','pending','publish','active','inactive','complete') COLLATE utf8mb4_unicode_ci DEFAULT 'draft',
  `result_declare` enum('true','false') COLLATE utf8mb4_unicode_ci DEFAULT 'false',
  `is_unlimited` tinyint(1) NOT NULL DEFAULT 0,
  `is_teaching_report_sync` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'true',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_configurations_details`
--

CREATE TABLE `exam_configurations_details` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `strand_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_unit_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_objectives_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_mode` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_levels` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_questions` int(11) DEFAULT 0,
  `time_duration` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_credit_point_rules_mapping`
--

CREATE TABLE `exam_credit_point_rules_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `credit_point_rules` enum('submission_on_time','credit_points_of_accuracy','credit_points_of_normalized_ability') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rules_value` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_school_grade_class_mapping`
--

CREATE TABLE `exam_school_grade_class_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `peer_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `status` enum('draft','publish','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_school_mapping`
--

CREATE TABLE `exam_school_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','publish','inactive') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game`
--

CREATE TABLE `game` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `game_planets`
--

CREATE TABLE `game_planets` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `planet_image` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `global_configuration`
--

CREATE TABLE `global_configuration` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades`
--

CREATE TABLE `grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grades_school_mapping`
--

CREATE TABLE `grades_school_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `grade_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `grade_class_mapping`
--

CREATE TABLE `grade_class_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `grade_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_student_exams`
--

CREATE TABLE `history_student_exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `no_of_trial_exam` int(11) DEFAULT NULL,
  `current_question_id` int(11) DEFAULT NULL,
  `first_trial_wrong_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `before_emoji_id` int(11) DEFAULT NULL,
  `after_emoji_id` int(11) DEFAULT NULL,
  `total_seconds` int(11) DEFAULT NULL,
  `first_trial_answered_flag_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_trial_not_attempted_flag_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `second_trial_answered_flag_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `second_trial_not_attempted_flag_question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `history_student_question_answer`
--

CREATE TABLE `history_student_question_answer` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `selected_answer_id` int(11) DEFAULT NULL,
  `answer_ordering` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_second` int(11) DEFAULT NULL,
  `is_trial_no` int(11) DEFAULT NULL,
  `is_answered_flag` enum('true','false') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `intelligent_tutor_videos`
--

CREATE TABLE `intelligent_tutor_videos` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_mapping_id` bigint(20) DEFAULT NULL,
  `document_type` int(11) DEFAULT NULL COMMENT '1-Self-Learning, 2-Execercise, 3-Test',
  `strand_units_mapping_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_name` bigint(20) DEFAULT NULL,
  `file_type` enum('pdf','jpg','png','jpeg','ppt','doc','docx','txt','xls','xlsx','csv','mp4','mp3','3gp','avi','vob','flv','webm','wmv','ogg','mpeg','mov','m4p','wav','aiff','aac','pptx','url') COLLATE utf8mb4_unicode_ci NOT NULL,
  `file_path` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_file_path` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_by` bigint(20) NOT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_objectives`
--

CREATE TABLE `learning_objectives` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `foci_number` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `title_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_available_questions` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT 'yes',
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_objectives_progress_report`
--

CREATE TABLE `learning_objectives_progress_report` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `learning_progress_all` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_progress_test` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_progress_testing_zone` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_objectives_skills`
--

CREATE TABLE `learning_objectives_skills` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `learning_objective_id` bigint(20) UNSIGNED NOT NULL,
  `learning_objectives_skill` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_objective_ordering`
--

CREATE TABLE `learning_objective_ordering` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `learning_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `learning_objective_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `index` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_units`
--

CREATE TABLE `learning_units` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stage_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `strand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_units_progress_report`
--

CREATE TABLE `learning_units_progress_report` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL,
  `learning_progress_all` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_progress_test` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `learning_progress_testing_zone` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `learning_unit_ordering`
--

CREATE TABLE `learning_unit_ordering` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `strand_id` bigint(20) UNSIGNED DEFAULT NULL,
  `learning_unit_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position` int(11) DEFAULT NULL,
  `index` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_activities`
--

CREATE TABLE `login_activities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('login','logout') COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `user_agent` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `main_upload_document`
--

CREATE TABLE `main_upload_document` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `strand_units_mapping_id` bigint(20) UNSIGNED DEFAULT NULL,
  `node_id` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `description_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `upload_by` bigint(20) DEFAULT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive','pending') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `module_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `module_slug` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `nodes`
--

CREATE TABLE `nodes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `node_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_title_en` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_title_ch` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_description_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `node_description_ch` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weakness_name_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `weakness_name_ch` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_main_node` tinyint(4) DEFAULT 0,
  `created_by` bigint(20) DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `node_relation`
--

CREATE TABLE `node_relation` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_node_id` bigint(20) UNSIGNED DEFAULT 0,
  `child_node_id` bigint(20) UNSIGNED DEFAULT 0,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `other_role`
--

CREATE TABLE `other_role` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active_status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parent_child_mapping`
--

CREATE TABLE `parent_child_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `parent_id` bigint(20) UNSIGNED NOT NULL,
  `student_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peer_group`
--

CREATE TABLE `peer_group` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dreamschat_group_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `group_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `group_prefix` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_type` enum('auto','manual') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'manual' COMMENT 'auto',
  `auto_group_by` enum('0','1') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '0- Round Robin  1- Sequence',
  `created_by_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `peer_group_members`
--

CREATE TABLE `peer_group_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `peer_group_id` bigint(20) UNSIGNED NOT NULL,
  `member_id` bigint(20) UNSIGNED NOT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 = Pending, 1 = Active, 2 = InActive, 3 = blocked',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pre_configured_difficulty`
--

CREATE TABLE `pre_configured_difficulty` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `difficulty_level_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_level_name_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_level_name_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_level_color` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `difficulty_level` int(11) NOT NULL,
  `title` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `question`
--

CREATE TABLE `question` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stage_id` int(11) NOT NULL DEFAULT 4,
  `objective_mapping_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `naming_structure_code` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_unique_code` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `marks` bigint(20) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) NOT NULL,
  `school_id` int(11) NOT NULL,
  `question_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '1 = Self-Learning, 2 = Exercise/Assignment, 3 = Testing, 4 = Seed',
  `dificulaty_level` int(11) NOT NULL,
  `pre_configure_difficulty_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ai_difficulty_value` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_hints_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_hints_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `general_hints_video_id_en` bigint(20) UNSIGNED DEFAULT NULL,
  `general_hints_video_id_ch` bigint(20) UNSIGNED DEFAULT NULL,
  `full_solution_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `full_solution_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `e` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `f` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `g` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_approved` enum('yes','no') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'no',
  `status` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `regions`
--

CREATE TABLE `regions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `region_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `remainder_update_school_year_data`
--

CREATE TABLE `remainder_update_school_year_data` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `import_date` date DEFAULT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('pending','complete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(10) UNSIGNED NOT NULL,
  `role_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `role_slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `permission` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `school`
--

CREATE TABLE `school` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `school_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `school_name_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_name_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_address_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_address_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `description_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_ch` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_start_time` date DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `site_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `site_url` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contact_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `fav_icon` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo_image` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_driver` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_host` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_port` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_username` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_passowrd` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `smtp_encryption` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `strands`
--

CREATE TABLE `strands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ch` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `code` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `strand_units_objectives_mappings`
--

CREATE TABLE `strand_units_objectives_mappings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `stage_id` int(11) DEFAULT 4,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `subject_id` bigint(20) UNSIGNED DEFAULT NULL,
  `strand_id` bigint(20) UNSIGNED NOT NULL,
  `learning_unit_id` bigint(20) UNSIGNED NOT NULL,
  `learning_objectives_id` bigint(20) UNSIGNED NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_attempt_exam_history`
--

CREATE TABLE `student_attempt_exam_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `question_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `selected_answer` int(11) DEFAULT NULL,
  `language` enum('en','ch') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_games_mapping`
--

CREATE TABLE `student_games_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `planet_id` bigint(20) UNSIGNED DEFAULT NULL,
  `current_position` int(11) DEFAULT NULL,
  `visited_steps` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `key_step_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `increase_step_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deducted_step_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','inprogress','complete') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_game_credit_point_history`
--

CREATE TABLE `student_game_credit_point_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `game_id` bigint(20) UNSIGNED NOT NULL,
  `planet_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `current_credit_point` int(11) DEFAULT NULL,
  `deduct_current_step` int(11) DEFAULT NULL,
  `deducted_steps` int(11) DEFAULT NULL,
  `increased_steps` int(11) DEFAULT NULL,
  `remaining_credit_point` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_group`
--

CREATE TABLE `student_group` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `exam_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `school_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `study_report`
--

CREATE TABLE `study_report` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `report_type` enum('assignment_test','self_learning') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Assignment/Test,Self Learning',
  `study_type` tinyint(4) NOT NULL COMMENT '1: Exercise; 2: Test;',
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `student_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `average_accuracy` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_status` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `questions_difficulties` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '1 = Active, 0 = InActive',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `subjects_school_mapping`
--

CREATE TABLE `subjects_school_mapping` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL,
  `subject_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers_class_subject_assign`
--

CREATE TABLE `teachers_class_subject_assign` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `teacher_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `class_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `subject_id` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_name_id` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teaching_report`
--

CREATE TABLE `teaching_report` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `report_type` enum('assignment_test','self_learning') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Assignment/Test,Self Learning',
  `study_type` tinyint(4) NOT NULL COMMENT '1: Exercise; 2: Test;',
  `school_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `peer_group_id` bigint(20) UNSIGNED DEFAULT NULL,
  `grade_with_class` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_students` bigint(20) DEFAULT NULL,
  `student_progress` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `average_accuracy` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `study_status` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `questions_difficulties` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_time` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `test_templates`
--

CREATE TABLE `test_templates` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template_type` enum('1','2','3') COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT '1 = Self-Learning, 2 = Excercise, 3 = Test',
  `difficulty_level` enum('1','2','3','4') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_ids` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `upload_documents`
--

CREATE TABLE `upload_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_mapping_id` bigint(20) UNSIGNED DEFAULT NULL,
  `document_type` int(11) DEFAULT NULL COMMENT '1-Self-Learning, 2-Execercise, 3-Test',
  `strand_units_mapping_id` bigint(20) UNSIGNED DEFAULT NULL,
  `file_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_type` enum('pdf','jpg','png','jpeg','ppt','doc','docx','txt','xls','xlsx','csv','mp4','mp3','3gp','avi','vob','flv','webm','wmv','ogg','mpeg','mov','m4p','wav','aiff','aac','pptx','url') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `file_path` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail_file_path` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `language_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `alp_chat_user_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name_en` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name_ch` longtext CHARACTER SET utf8mb4 DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mobile_no` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `city` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `region_id` bigint(20) UNSIGNED DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `profile_photo` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_school_admin_privilege_access` enum('yes','no') COLLATE utf8mb4_unicode_ci DEFAULT 'no',
  `role_id` tinyint(1) DEFAULT NULL COMMENT '1 = admin, 2=teacher, 3 = student, 4 = parent, 5 = school',
  `grade_id` bigint(20) DEFAULT NULL,
  `class_id` bigint(20) UNSIGNED DEFAULT NULL,
  `school_id` bigint(20) DEFAULT NULL,
  `permanent_reference_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_number_within_class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_student_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `student_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_name` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `class_class_student_number` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'class + class student number',
  `other_roles_id` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `overall_ability` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('pending','active','inactive') COLLATE utf8mb4_unicode_ci DEFAULT 'pending',
  `import_date` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_credit_points`
--

CREATE TABLE `user_credit_points` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `no_of_credit_points` bigint(20) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_credit_point_history`
--

CREATE TABLE `user_credit_point_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `curriculum_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `exam_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `test_type` enum('test','exercise','self_learning','assessment') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `self_learning_type` enum('test','exercise') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `credit_point_type` enum('submission','accuracy','ability','manual_credit_point') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `no_of_credit_point` bigint(20) DEFAULT NULL,
  `credit_point_history` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `weather_detail`
--

CREATE TABLE `weather_detail` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `weather_info` longtext COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_log_curriculum_year_id_foreign` (`curriculum_year_id`),
  ADD KEY `activity_log_school_id_foreign` (`school_id`),
  ADD KEY `activity_log_user_id_foreign` (`user_id`);

--
-- Indexes for table `ai_calculated_difficulty`
--
ALTER TABLE `ai_calculated_difficulty`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ai_calibration_report`
--
ALTER TABLE `ai_calibration_report`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `attempt_exams`
--
ALTER TABLE `attempt_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_exams_exam_id_foreign` (`exam_id`),
  ADD KEY `attempt_exams_student_id_foreign` (`student_id`),
  ADD KEY `attempt_exams_grade_id_foreign` (`grade_id`),
  ADD KEY `attempt_exams_class_id_foreign` (`class_id`),
  ADD KEY `attempt_exams_curriculum_year_id_foreign` (`curriculum_year_id`),
  ADD KEY `attempt_exams_calibration_id_foreign` (`calibration_id`);

--
-- Indexes for table `attempt_exam_student_mapping`
--
ALTER TABLE `attempt_exam_student_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attempt_exam_student_mapping_exam_id_foreign` (`exam_id`),
  ADD KEY `attempt_exam_student_mapping_student_id_foreign` (`student_id`),
  ADD KEY `attempt_exam_student_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_logged_user_id_foreign` (`logged_user_id`),
  ADD KEY `audit_logs_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `calibration_question_log`
--
ALTER TABLE `calibration_question_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `calibration_question_log_calibration_report_id_foreign` (`calibration_report_id`),
  ADD KEY `calibration_question_log_question_id_foreign` (`question_id`),
  ADD KEY `calibration_question_log_seed_question_id_foreign` (`seed_question_id`);

--
-- Indexes for table `class_assignment_students`
--
ALTER TABLE `class_assignment_students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `class_promotion_history`
--
ALTER TABLE `class_promotion_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_promotion_history_student_id_foreign` (`student_id`),
  ADD KEY `class_promotion_history_current_grade_id_foreign` (`current_grade_id`),
  ADD KEY `class_promotion_history_current_class_id_foreign` (`current_class_id`),
  ADD KEY `class_promotion_history_promoted_grade_id_foreign` (`promoted_grade_id`),
  ADD KEY `class_promotion_history_promoted_class_id_foreign` (`promoted_class_id`),
  ADD KEY `class_promotion_history_promoted_by_userid_foreign` (`promoted_by_userid`),
  ADD KEY `class_promotion_history_school_id_foreign` (`school_id`),
  ADD KEY `class_promotion_history_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `class_subject_mapping`
--
ALTER TABLE `class_subject_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `class_subject_mapping_subject_id_foreign` (`subject_id`),
  ADD KEY `class_subject_mapping_class_id_foreign` (`class_id`),
  ADD KEY `class_subject_mapping_school_id_foreign` (`school_id`),
  ADD KEY `class_subject_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `curriculum_years`
--
ALTER TABLE `curriculum_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `curriculum_year_student_mapping`
--
ALTER TABLE `curriculum_year_student_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `curriculum_year_student_mapping_user_id_foreign` (`user_id`),
  ADD KEY `curriculum_year_student_mapping_school_id_foreign` (`school_id`),
  ADD KEY `curriculum_year_student_mapping_grade_id_foreign` (`grade_id`),
  ADD KEY `curriculum_year_student_mapping_class_id_foreign` (`class_id`),
  ADD KEY `curriculum_year_student_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `exam`
--
ALTER TABLE `exam`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_created_by_foreign` (`created_by`),
  ADD KEY `exam_curriculum_year_id_foreign` (`curriculum_year_id`),
  ADD KEY `exam_calibration_id_foreign` (`calibration_id`);

--
-- Indexes for table `exam_configurations_details`
--
ALTER TABLE `exam_configurations_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_configurations_details_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_configurations_details_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `exam_configurations_details_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `exam_credit_point_rules_mapping`
--
ALTER TABLE `exam_credit_point_rules_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_credit_point_rules_mapping_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_credit_point_rules_mapping_school_id_foreign` (`school_id`),
  ADD KEY `exam_credit_point_rules_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `exam_school_grade_class_mapping`
--
ALTER TABLE `exam_school_grade_class_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_school_grade_class_mapping_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_school_grade_class_mapping_school_id_foreign` (`school_id`),
  ADD KEY `exam_school_grade_class_mapping_grade_id_foreign` (`grade_id`),
  ADD KEY `exam_school_grade_class_mapping_class_id_foreign` (`class_id`),
  ADD KEY `exam_school_grade_class_mapping_peer_group_id_foreign` (`peer_group_id`),
  ADD KEY `exam_school_grade_class_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `exam_school_mapping`
--
ALTER TABLE `exam_school_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_school_mapping_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_school_mapping_school_id_foreign` (`school_id`),
  ADD KEY `exam_school_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `game`
--
ALTER TABLE `game`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `game_planets`
--
ALTER TABLE `game_planets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `game_planets_grade_id_foreign` (`grade_id`);

--
-- Indexes for table `global_configuration`
--
ALTER TABLE `global_configuration`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades`
--
ALTER TABLE `grades`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `grades_school_mapping`
--
ALTER TABLE `grades_school_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grades_school_mapping_school_id_foreign` (`school_id`),
  ADD KEY `grades_school_mapping_grade_id_foreign` (`grade_id`),
  ADD KEY `grades_school_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `grade_class_mapping`
--
ALTER TABLE `grade_class_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `grade_class_mapping_school_id_foreign` (`school_id`),
  ADD KEY `grade_class_mapping_grade_id_foreign` (`grade_id`),
  ADD KEY `grade_class_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `history_student_exams`
--
ALTER TABLE `history_student_exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_student_exams_student_id_foreign` (`student_id`),
  ADD KEY `history_student_exams_exam_id_foreign` (`exam_id`);

--
-- Indexes for table `history_student_question_answer`
--
ALTER TABLE `history_student_question_answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `history_student_question_answer_student_id_foreign` (`student_id`),
  ADD KEY `history_student_question_answer_exam_id_foreign` (`exam_id`),
  ADD KEY `history_student_question_answer_question_id_foreign` (`question_id`);

--
-- Indexes for table `intelligent_tutor_videos`
--
ALTER TABLE `intelligent_tutor_videos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `intelligent_tutor_videos_strand_units_mapping_id_foreign` (`strand_units_mapping_id`),
  ADD KEY `intelligent_tutor_videos_language_id_foreign` (`language_id`),
  ADD KEY `intelligent_tutor_videos_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `learning_objectives`
--
ALTER TABLE `learning_objectives`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_objectives_learning_unit_id_foreign` (`learning_unit_id`);

--
-- Indexes for table `learning_objectives_progress_report`
--
ALTER TABLE `learning_objectives_progress_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_objectives_progress_report_student_id_foreign` (`student_id`);

--
-- Indexes for table `learning_objectives_skills`
--
ALTER TABLE `learning_objectives_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_objectives_skills_learning_objective_id_foreign` (`learning_objective_id`);

--
-- Indexes for table `learning_objective_ordering`
--
ALTER TABLE `learning_objective_ordering`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_objective_ordering_school_id_foreign` (`school_id`),
  ADD KEY `learning_objective_ordering_learning_objective_id_foreign` (`learning_objective_id`),
  ADD KEY `learning_objective_ordering_learning_unit_id_foreign` (`learning_unit_id`);

--
-- Indexes for table `learning_units`
--
ALTER TABLE `learning_units`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_units_strand_id_foreign` (`strand_id`);

--
-- Indexes for table `learning_units_progress_report`
--
ALTER TABLE `learning_units_progress_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_units_progress_report_student_id_foreign` (`student_id`);

--
-- Indexes for table `learning_unit_ordering`
--
ALTER TABLE `learning_unit_ordering`
  ADD PRIMARY KEY (`id`),
  ADD KEY `learning_unit_ordering_school_id_foreign` (`school_id`),
  ADD KEY `learning_unit_ordering_learning_unit_id_foreign` (`learning_unit_id`),
  ADD KEY `learning_unit_ordering_strand_id_foreign` (`strand_id`);

--
-- Indexes for table `login_activities`
--
ALTER TABLE `login_activities`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `main_upload_document`
--
ALTER TABLE `main_upload_document`
  ADD PRIMARY KEY (`id`),
  ADD KEY `main_upload_document_language_id_foreign` (`language_id`),
  ADD KEY `main_upload_document_strand_units_mapping_id_foreign` (`strand_units_mapping_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `nodes`
--
ALTER TABLE `nodes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `node_relation`
--
ALTER TABLE `node_relation`
  ADD PRIMARY KEY (`id`),
  ADD KEY `node_relation_parent_node_id_foreign` (`parent_node_id`),
  ADD KEY `node_relation_child_node_id_foreign` (`child_node_id`);

--
-- Indexes for table `other_role`
--
ALTER TABLE `other_role`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `parent_child_mapping`
--
ALTER TABLE `parent_child_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `parent_child_mapping_student_id_foreign` (`student_id`),
  ADD KEY `parent_child_mapping_parent_id_foreign` (`parent_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `peer_group`
--
ALTER TABLE `peer_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peer_group_school_id_foreign` (`school_id`),
  ADD KEY `peer_group_subject_id_foreign` (`subject_id`),
  ADD KEY `peer_group_created_by_user_id_foreign` (`created_by_user_id`),
  ADD KEY `peer_group_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `peer_group_members`
--
ALTER TABLE `peer_group_members`
  ADD PRIMARY KEY (`id`),
  ADD KEY `peer_group_members_peer_group_id_foreign` (`peer_group_id`),
  ADD KEY `peer_group_members_member_id_foreign` (`member_id`),
  ADD KEY `peer_group_members_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `pre_configured_difficulty`
--
ALTER TABLE `pre_configured_difficulty`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pre_configured_difficulty_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `question_objective_mapping_id_foreign` (`objective_mapping_id`),
  ADD KEY `question_general_hints_video_id_foreign` (`general_hints_video_id_en`),
  ADD KEY `question_general_hints_video_id_ch_foreign` (`general_hints_video_id_ch`);

--
-- Indexes for table `regions`
--
ALTER TABLE `regions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `remainder_update_school_year_data`
--
ALTER TABLE `remainder_update_school_year_data`
  ADD PRIMARY KEY (`id`),
  ADD KEY `remainder_update_school_year_data_curriculum_year_id_foreign` (`curriculum_year_id`),
  ADD KEY `remainder_update_school_year_data_school_id_foreign` (`school_id`),
  ADD KEY `remainder_update_school_year_data_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `school`
--
ALTER TABLE `school`
  ADD PRIMARY KEY (`id`),
  ADD KEY `school_region_id_foreign` (`region_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `strands`
--
ALTER TABLE `strands`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `strand_units_objectives_mappings`
--
ALTER TABLE `strand_units_objectives_mappings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `grade_id` (`grade_id`,`subject_id`,`strand_id`,`learning_unit_id`,`learning_objectives_id`),
  ADD KEY `strand_units_objectives_mappings_grade_id_foreign` (`grade_id`),
  ADD KEY `strand_units_objectives_mappings_subject_id_foreign` (`subject_id`),
  ADD KEY `strand_units_objectives_mappings_strand_id_foreign` (`strand_id`),
  ADD KEY `strand_units_objectives_mappings_learning_unit_id_foreign` (`learning_unit_id`),
  ADD KEY `strand_units_objectives_mappings_learning_objectives_id_foreign` (`learning_objectives_id`);

--
-- Indexes for table `student_attempt_exam_history`
--
ALTER TABLE `student_attempt_exam_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_attempt_exam_history_exam_id_foreign` (`exam_id`),
  ADD KEY `student_attempt_exam_history_question_id_foreign` (`question_id`),
  ADD KEY `student_attempt_exam_history_student_id_foreign` (`student_id`);

--
-- Indexes for table `student_games_mapping`
--
ALTER TABLE `student_games_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_games_mapping_game_id_foreign` (`game_id`),
  ADD KEY `student_games_mapping_student_id_foreign` (`student_id`),
  ADD KEY `student_games_mapping_planet_id_foreign` (`planet_id`);

--
-- Indexes for table `student_game_credit_point_history`
--
ALTER TABLE `student_game_credit_point_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_game_credit_point_history_game_id_foreign` (`game_id`),
  ADD KEY `student_game_credit_point_history_planet_id_foreign` (`planet_id`),
  ADD KEY `student_game_credit_point_history_user_id_foreign` (`user_id`);

--
-- Indexes for table `student_group`
--
ALTER TABLE `student_group`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_group_grade_id_foreign` (`grade_id`);

--
-- Indexes for table `study_report`
--
ALTER TABLE `study_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study_report_school_id_foreign` (`school_id`),
  ADD KEY `study_report_exam_id_foreign` (`exam_id`),
  ADD KEY `study_report_grade_id_foreign` (`grade_id`),
  ADD KEY `study_report_class_id_foreign` (`class_id`),
  ADD KEY `study_report_student_id_foreign` (`student_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `subjects_school_mapping`
--
ALTER TABLE `subjects_school_mapping`
  ADD PRIMARY KEY (`id`),
  ADD KEY `subjects_school_mapping_school_id_foreign` (`school_id`),
  ADD KEY `subjects_school_mapping_subject_id_foreign` (`subject_id`),
  ADD KEY `subjects_school_mapping_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `teachers_class_subject_assign`
--
ALTER TABLE `teachers_class_subject_assign`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teachers_class_subject_assign_school_id_foreign` (`school_id`),
  ADD KEY `teachers_class_subject_assign_teacher_id_foreign` (`teacher_id`),
  ADD KEY `teachers_class_subject_assign_class_id_foreign` (`class_id`),
  ADD KEY `teachers_class_subject_assign_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `teaching_report`
--
ALTER TABLE `teaching_report`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teaching_report_school_id_foreign` (`school_id`),
  ADD KEY `teaching_report_exam_id_foreign` (`exam_id`),
  ADD KEY `teaching_report_grade_id_foreign` (`grade_id`),
  ADD KEY `teaching_report_class_id_foreign` (`class_id`),
  ADD KEY `teaching_report_peer_group_id_foreign` (`peer_group_id`),
  ADD KEY `teaching_report_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `test_templates`
--
ALTER TABLE `test_templates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `test_templates_created_by_foreign` (`created_by`);

--
-- Indexes for table `upload_documents`
--
ALTER TABLE `upload_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `upload_documents_strand_units_mapping_id_foreign` (`strand_units_mapping_id`),
  ADD KEY `upload_documents_document_mapping_id_foreign` (`document_mapping_id`),
  ADD KEY `upload_documents_language_id_foreign` (`language_id`),
  ADD KEY `upload_documents_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_class_id_foreign` (`class_id`),
  ADD KEY `users_curriculum_year_id_foreign` (`curriculum_year_id`),
  ADD KEY `users_region_id_foreign` (`region_id`);

--
-- Indexes for table `user_credit_points`
--
ALTER TABLE `user_credit_points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_credit_points_user_id_foreign` (`user_id`);

--
-- Indexes for table `user_credit_point_history`
--
ALTER TABLE `user_credit_point_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_credit_point_history_user_id_foreign` (`user_id`),
  ADD KEY `user_credit_point_history_exam_id_foreign` (`exam_id`),
  ADD KEY `user_credit_point_history_curriculum_year_id_foreign` (`curriculum_year_id`);

--
-- Indexes for table `weather_detail`
--
ALTER TABLE `weather_detail`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_log`
--
ALTER TABLE `activity_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_calculated_difficulty`
--
ALTER TABLE `ai_calculated_difficulty`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ai_calibration_report`
--
ALTER TABLE `ai_calibration_report`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `answer`
--
ALTER TABLE `answer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attempt_exams`
--
ALTER TABLE `attempt_exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `attempt_exam_student_mapping`
--
ALTER TABLE `attempt_exam_student_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `calibration_question_log`
--
ALTER TABLE `calibration_question_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_assignment_students`
--
ALTER TABLE `class_assignment_students`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_promotion_history`
--
ALTER TABLE `class_promotion_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `class_subject_mapping`
--
ALTER TABLE `class_subject_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `curriculum_years`
--
ALTER TABLE `curriculum_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `curriculum_year_student_mapping`
--
ALTER TABLE `curriculum_year_student_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam`
--
ALTER TABLE `exam`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_configurations_details`
--
ALTER TABLE `exam_configurations_details`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_credit_point_rules_mapping`
--
ALTER TABLE `exam_credit_point_rules_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_school_grade_class_mapping`
--
ALTER TABLE `exam_school_grade_class_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_school_mapping`
--
ALTER TABLE `exam_school_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game`
--
ALTER TABLE `game`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `game_planets`
--
ALTER TABLE `game_planets`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `global_configuration`
--
ALTER TABLE `global_configuration`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades`
--
ALTER TABLE `grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grades_school_mapping`
--
ALTER TABLE `grades_school_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `grade_class_mapping`
--
ALTER TABLE `grade_class_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_student_exams`
--
ALTER TABLE `history_student_exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `history_student_question_answer`
--
ALTER TABLE `history_student_question_answer`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `intelligent_tutor_videos`
--
ALTER TABLE `intelligent_tutor_videos`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `languages`
--
ALTER TABLE `languages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_objectives`
--
ALTER TABLE `learning_objectives`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_objectives_progress_report`
--
ALTER TABLE `learning_objectives_progress_report`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_objectives_skills`
--
ALTER TABLE `learning_objectives_skills`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_objective_ordering`
--
ALTER TABLE `learning_objective_ordering`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_units`
--
ALTER TABLE `learning_units`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_units_progress_report`
--
ALTER TABLE `learning_units_progress_report`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `learning_unit_ordering`
--
ALTER TABLE `learning_unit_ordering`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_activities`
--
ALTER TABLE `login_activities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `main_upload_document`
--
ALTER TABLE `main_upload_document`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `nodes`
--
ALTER TABLE `nodes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `node_relation`
--
ALTER TABLE `node_relation`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `other_role`
--
ALTER TABLE `other_role`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `parent_child_mapping`
--
ALTER TABLE `parent_child_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_group`
--
ALTER TABLE `peer_group`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `peer_group_members`
--
ALTER TABLE `peer_group_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `pre_configured_difficulty`
--
ALTER TABLE `pre_configured_difficulty`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `question`
--
ALTER TABLE `question`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `regions`
--
ALTER TABLE `regions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `remainder_update_school_year_data`
--
ALTER TABLE `remainder_update_school_year_data`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `school`
--
ALTER TABLE `school`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `strands`
--
ALTER TABLE `strands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `strand_units_objectives_mappings`
--
ALTER TABLE `strand_units_objectives_mappings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_attempt_exam_history`
--
ALTER TABLE `student_attempt_exam_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_games_mapping`
--
ALTER TABLE `student_games_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_game_credit_point_history`
--
ALTER TABLE `student_game_credit_point_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_group`
--
ALTER TABLE `student_group`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `study_report`
--
ALTER TABLE `study_report`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `subjects_school_mapping`
--
ALTER TABLE `subjects_school_mapping`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teachers_class_subject_assign`
--
ALTER TABLE `teachers_class_subject_assign`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `teaching_report`
--
ALTER TABLE `teaching_report`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `test_templates`
--
ALTER TABLE `test_templates`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `upload_documents`
--
ALTER TABLE `upload_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_credit_points`
--
ALTER TABLE `user_credit_points`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_credit_point_history`
--
ALTER TABLE `user_credit_point_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `weather_detail`
--
ALTER TABLE `weather_detail`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_log`
--
ALTER TABLE `activity_log`
  ADD CONSTRAINT `activity_log_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_log_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `activity_log_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attempt_exams`
--
ALTER TABLE `attempt_exams`
  ADD CONSTRAINT `attempt_exams_calibration_id_foreign` FOREIGN KEY (`calibration_id`) REFERENCES `ai_calibration_report` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exams_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exams_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exams_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exams_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exams_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `attempt_exam_student_mapping`
--
ALTER TABLE `attempt_exam_student_mapping`
  ADD CONSTRAINT `attempt_exam_student_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exam_student_mapping_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attempt_exam_student_mapping_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `audit_logs_logged_user_id_foreign` FOREIGN KEY (`logged_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `calibration_question_log`
--
ALTER TABLE `calibration_question_log`
  ADD CONSTRAINT `calibration_question_log_calibration_report_id_foreign` FOREIGN KEY (`calibration_report_id`) REFERENCES `ai_calibration_report` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calibration_question_log_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `calibration_question_log_seed_question_id_foreign` FOREIGN KEY (`seed_question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_promotion_history`
--
ALTER TABLE `class_promotion_history`
  ADD CONSTRAINT `class_promotion_history_current_class_id_foreign` FOREIGN KEY (`current_class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_current_grade_id_foreign` FOREIGN KEY (`current_grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_promoted_by_userid_foreign` FOREIGN KEY (`promoted_by_userid`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_promoted_class_id_foreign` FOREIGN KEY (`promoted_class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_promoted_grade_id_foreign` FOREIGN KEY (`promoted_grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_promotion_history_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `class_subject_mapping`
--
ALTER TABLE `class_subject_mapping`
  ADD CONSTRAINT `class_subject_mapping_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subject_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subject_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `class_subject_mapping_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `curriculum_year_student_mapping`
--
ALTER TABLE `curriculum_year_student_mapping`
  ADD CONSTRAINT `curriculum_year_student_mapping_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_year_student_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_year_student_mapping_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_year_student_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `curriculum_year_student_mapping_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam`
--
ALTER TABLE `exam`
  ADD CONSTRAINT `exam_calibration_id_foreign` FOREIGN KEY (`calibration_id`) REFERENCES `ai_calibration_report` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_configurations_details`
--
ALTER TABLE `exam_configurations_details`
  ADD CONSTRAINT `exam_configurations_details_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_configurations_details_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_configurations_details_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_credit_point_rules_mapping`
--
ALTER TABLE `exam_credit_point_rules_mapping`
  ADD CONSTRAINT `exam_credit_point_rules_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_credit_point_rules_mapping_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_credit_point_rules_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_school_grade_class_mapping`
--
ALTER TABLE `exam_school_grade_class_mapping`
  ADD CONSTRAINT `exam_school_grade_class_mapping_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_grade_class_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_grade_class_mapping_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_grade_class_mapping_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_grade_class_mapping_peer_group_id_foreign` FOREIGN KEY (`peer_group_id`) REFERENCES `peer_group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_grade_class_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_school_mapping`
--
ALTER TABLE `exam_school_mapping`
  ADD CONSTRAINT `exam_school_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_mapping_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_school_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `game_planets`
--
ALTER TABLE `game_planets`
  ADD CONSTRAINT `game_planets_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grades_school_mapping`
--
ALTER TABLE `grades_school_mapping`
  ADD CONSTRAINT `grades_school_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_school_mapping_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grades_school_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `grade_class_mapping`
--
ALTER TABLE `grade_class_mapping`
  ADD CONSTRAINT `grade_class_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grade_class_mapping_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `grade_class_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `history_student_exams`
--
ALTER TABLE `history_student_exams`
  ADD CONSTRAINT `history_student_exams_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_student_exams_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `history_student_question_answer`
--
ALTER TABLE `history_student_question_answer`
  ADD CONSTRAINT `history_student_question_answer_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_student_question_answer_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `history_student_question_answer_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `intelligent_tutor_videos`
--
ALTER TABLE `intelligent_tutor_videos`
  ADD CONSTRAINT `intelligent_tutor_videos_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intelligent_tutor_videos_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `intelligent_tutor_videos_strand_units_mapping_id_foreign` FOREIGN KEY (`strand_units_mapping_id`) REFERENCES `strand_units_objectives_mappings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_objectives`
--
ALTER TABLE `learning_objectives`
  ADD CONSTRAINT `learning_objectives_learning_unit_id_foreign` FOREIGN KEY (`learning_unit_id`) REFERENCES `learning_units` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_objectives_progress_report`
--
ALTER TABLE `learning_objectives_progress_report`
  ADD CONSTRAINT `learning_objectives_progress_report_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_objectives_skills`
--
ALTER TABLE `learning_objectives_skills`
  ADD CONSTRAINT `learning_objectives_skills_learning_objective_id_foreign` FOREIGN KEY (`learning_objective_id`) REFERENCES `learning_objectives` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_objective_ordering`
--
ALTER TABLE `learning_objective_ordering`
  ADD CONSTRAINT `learning_objective_ordering_learning_objective_id_foreign` FOREIGN KEY (`learning_objective_id`) REFERENCES `learning_objectives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_objective_ordering_learning_unit_id_foreign` FOREIGN KEY (`learning_unit_id`) REFERENCES `learning_units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_objective_ordering_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_units`
--
ALTER TABLE `learning_units`
  ADD CONSTRAINT `learning_units_strand_id_foreign` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_units_progress_report`
--
ALTER TABLE `learning_units_progress_report`
  ADD CONSTRAINT `learning_units_progress_report_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `learning_unit_ordering`
--
ALTER TABLE `learning_unit_ordering`
  ADD CONSTRAINT `learning_unit_ordering_learning_unit_id_foreign` FOREIGN KEY (`learning_unit_id`) REFERENCES `learning_units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_unit_ordering_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `learning_unit_ordering_strand_id_foreign` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `main_upload_document`
--
ALTER TABLE `main_upload_document`
  ADD CONSTRAINT `main_upload_document_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `main_upload_document_strand_units_mapping_id_foreign` FOREIGN KEY (`strand_units_mapping_id`) REFERENCES `strand_units_objectives_mappings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `node_relation`
--
ALTER TABLE `node_relation`
  ADD CONSTRAINT `node_relation_child_node_id_foreign` FOREIGN KEY (`child_node_id`) REFERENCES `nodes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `node_relation_parent_node_id_foreign` FOREIGN KEY (`parent_node_id`) REFERENCES `nodes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `parent_child_mapping`
--
ALTER TABLE `parent_child_mapping`
  ADD CONSTRAINT `parent_child_mapping_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `parent_child_mapping_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peer_group`
--
ALTER TABLE `peer_group`
  ADD CONSTRAINT `peer_group_created_by_user_id_foreign` FOREIGN KEY (`created_by_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peer_group_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peer_group_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peer_group_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `peer_group_members`
--
ALTER TABLE `peer_group_members`
  ADD CONSTRAINT `peer_group_members_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peer_group_members_member_id_foreign` FOREIGN KEY (`member_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `peer_group_members_peer_group_id_foreign` FOREIGN KEY (`peer_group_id`) REFERENCES `peer_group` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `pre_configured_difficulty`
--
ALTER TABLE `pre_configured_difficulty`
  ADD CONSTRAINT `pre_configured_difficulty_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `question_general_hints_video_id_ch_foreign` FOREIGN KEY (`general_hints_video_id_ch`) REFERENCES `upload_documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_general_hints_video_id_foreign` FOREIGN KEY (`general_hints_video_id_en`) REFERENCES `upload_documents` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `question_objective_mapping_id_foreign` FOREIGN KEY (`objective_mapping_id`) REFERENCES `strand_units_objectives_mappings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `remainder_update_school_year_data`
--
ALTER TABLE `remainder_update_school_year_data`
  ADD CONSTRAINT `remainder_update_school_year_data_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `remainder_update_school_year_data_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `remainder_update_school_year_data_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `school`
--
ALTER TABLE `school`
  ADD CONSTRAINT `school_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `strand_units_objectives_mappings`
--
ALTER TABLE `strand_units_objectives_mappings`
  ADD CONSTRAINT `strand_units_objectives_mappings_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `strand_units_objectives_mappings_learning_objectives_id_foreign` FOREIGN KEY (`learning_objectives_id`) REFERENCES `learning_objectives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `strand_units_objectives_mappings_learning_unit_id_foreign` FOREIGN KEY (`learning_unit_id`) REFERENCES `learning_units` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `strand_units_objectives_mappings_strand_id_foreign` FOREIGN KEY (`strand_id`) REFERENCES `strands` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `strand_units_objectives_mappings_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_attempt_exam_history`
--
ALTER TABLE `student_attempt_exam_history`
  ADD CONSTRAINT `student_attempt_exam_history_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attempt_exam_history_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_attempt_exam_history_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_games_mapping`
--
ALTER TABLE `student_games_mapping`
  ADD CONSTRAINT `student_games_mapping_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_games_mapping_planet_id_foreign` FOREIGN KEY (`planet_id`) REFERENCES `game_planets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_games_mapping_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_game_credit_point_history`
--
ALTER TABLE `student_game_credit_point_history`
  ADD CONSTRAINT `student_game_credit_point_history_game_id_foreign` FOREIGN KEY (`game_id`) REFERENCES `game` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_game_credit_point_history_planet_id_foreign` FOREIGN KEY (`planet_id`) REFERENCES `game_planets` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `student_game_credit_point_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_group`
--
ALTER TABLE `student_group`
  ADD CONSTRAINT `student_group_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `study_report`
--
ALTER TABLE `study_report`
  ADD CONSTRAINT `study_report_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_report_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_report_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_report_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `study_report_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `subjects_school_mapping`
--
ALTER TABLE `subjects_school_mapping`
  ADD CONSTRAINT `subjects_school_mapping_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_school_mapping_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `subjects_school_mapping_subject_id_foreign` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers_class_subject_assign`
--
ALTER TABLE `teachers_class_subject_assign`
  ADD CONSTRAINT `teachers_class_subject_assign_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grades` (`id`),
  ADD CONSTRAINT `teachers_class_subject_assign_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teachers_class_subject_assign_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`),
  ADD CONSTRAINT `teachers_class_subject_assign_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `teaching_report`
--
ALTER TABLE `teaching_report`
  ADD CONSTRAINT `teaching_report_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_report_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_report_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_report_grade_id_foreign` FOREIGN KEY (`grade_id`) REFERENCES `grades` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_report_peer_group_id_foreign` FOREIGN KEY (`peer_group_id`) REFERENCES `peer_group` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `teaching_report_school_id_foreign` FOREIGN KEY (`school_id`) REFERENCES `school` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `test_templates`
--
ALTER TABLE `test_templates`
  ADD CONSTRAINT `test_templates_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `upload_documents`
--
ALTER TABLE `upload_documents`
  ADD CONSTRAINT `upload_documents_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upload_documents_document_mapping_id_foreign` FOREIGN KEY (`document_mapping_id`) REFERENCES `main_upload_document` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upload_documents_language_id_foreign` FOREIGN KEY (`language_id`) REFERENCES `languages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `upload_documents_strand_units_mapping_id_foreign` FOREIGN KEY (`strand_units_mapping_id`) REFERENCES `strand_units_objectives_mappings` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_class_id_foreign` FOREIGN KEY (`class_id`) REFERENCES `grade_class_mapping` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `users_region_id_foreign` FOREIGN KEY (`region_id`) REFERENCES `regions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_credit_points`
--
ALTER TABLE `user_credit_points`
  ADD CONSTRAINT `user_credit_points_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_credit_point_history`
--
ALTER TABLE `user_credit_point_history`
  ADD CONSTRAINT `user_credit_point_history_curriculum_year_id_foreign` FOREIGN KEY (`curriculum_year_id`) REFERENCES `curriculum_years` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_credit_point_history_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exam` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `user_credit_point_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
