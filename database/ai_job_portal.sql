-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2026 at 03:29 PM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.0.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ai_job_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `status` enum('applied','ai_interview_started','ai_interview_completed','ai_evaluated','shortlisted','rejected','interview_slot_booked') DEFAULT NULL,
  `interview_slot` datetime DEFAULT NULL,
  `ai_interview_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `candidate_id`, `job_id`, `status`, `interview_slot`, `ai_interview_id`, `booking_id`, `applied_at`) VALUES
(74, 47, 56, 'applied', NULL, NULL, NULL, '2026-02-21 10:51:48'),
(75, 47, 57, 'shortlisted', NULL, NULL, NULL, '2026-02-23 07:15:19'),
(76, 49, 56, 'shortlisted', NULL, NULL, NULL, '2026-02-23 11:30:51');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_github_stats`
--

CREATE TABLE `candidate_github_stats` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `github_username` varchar(100) DEFAULT NULL,
  `repo_count` int(11) DEFAULT NULL,
  `commit_count` int(11) DEFAULT NULL,
  `languages_used` text DEFAULT NULL,
  `github_score` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_github_stats`
--

INSERT INTO `candidate_github_stats` (`id`, `candidate_id`, `github_username`, `repo_count`, `commit_count`, `languages_used`, `github_score`, `created_at`) VALUES
(1, 47, 'rinudeepak', 3, 54, 'HTML,PHP,CSS,JavaScript,Hack', 3, '2026-02-23 06:53:38');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_interests`
--

CREATE TABLE `candidate_interests` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `interest` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_interests`
--

INSERT INTO `candidate_interests` (`id`, `candidate_id`, `interest`) VALUES
(1, 47, 'UI/UX Design');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_skills`
--

CREATE TABLE `candidate_skills` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `skill_name` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `candidate_skills`
--

INSERT INTO `candidate_skills` (`id`, `candidate_id`, `skill_name`, `created_at`) VALUES
(1, 47, 'PHP, MySQL, JavaScript', '2026-02-21 10:51:33'),
(2, 49, 'PHP, MySQL, JavaScript', '2026-02-23 11:30:35');

-- --------------------------------------------------------

--
-- Table structure for table `career_transitions`
--

CREATE TABLE `career_transitions` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `current_role` varchar(100) NOT NULL,
  `target_role` varchar(100) NOT NULL,
  `skill_gaps` text DEFAULT NULL,
  `learning_roadmap` longtext DEFAULT NULL,
  `status` enum('active','completed','paused','inactive') DEFAULT 'active',
  `progress_percentage` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  `deactivated_at` datetime DEFAULT NULL COMMENT 'When this transition was deactivated',
  `reactivated_at` datetime DEFAULT NULL COMMENT 'Last time this transition was reactivated',
  `reactivation_count` int(11) NOT NULL DEFAULT 0 COMMENT 'Number of times this path has been reactivated',
  `updated_at` datetime DEFAULT NULL,
  `course_status` enum('pending','processing','completed','failed') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certifications`
--

CREATE TABLE `certifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `certification_name` varchar(255) NOT NULL,
  `issuing_organization` varchar(255) NOT NULL,
  `issue_date` date DEFAULT NULL,
  `expiry_date` date DEFAULT NULL,
  `credential_id` varchar(255) DEFAULT NULL,
  `credential_url` varchar(500) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `companies`
--

CREATE TABLE `companies` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `industry` varchar(150) DEFAULT NULL,
  `size` varchar(50) DEFAULT NULL,
  `hq` varchar(200) DEFAULT NULL,
  `branches` text DEFAULT NULL,
  `short_description` varchar(255) DEFAULT NULL,
  `what_we_do` text DEFAULT NULL,
  `mission_values` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contact_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `website`, `industry`, `size`, `hq`, `branches`, `short_description`, `what_we_do`, `mission_values`, `contact_email`, `contact_phone`, `contact_public`, `created_at`, `updated_at`) VALUES
(1, 'TechNova Solutions', 'uploads/company_logos/1771669764_20d47a35f4f9596afa95.jpg', 'https://www.technovasolutions.com', 'IT Services', '50-200', 'Bangalore, India', 'Kochi, Chennai', 'We build scalable digital solutions for modern businesses.', 'We develop web applications, mobile apps, and cloud solutions for startups and enterprises.', 'Innovation, Transparency, Customer Success', 'hr@technovasolutions.com', '+91-9876543210', 1, '2026-02-21 09:27:53', '2026-02-21 10:29:24'),
(2, 'PHP Developer', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(3, 'CodeCraft Technologies', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(4, 'PixelSoft Pvt Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(5, 'Innova Systems', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(6, 'NextGen IT Services', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(7, 'BlueSky Technologies', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(8, 'CloudNova Pvt Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(9, 'DesignHub Studio', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(10, 'InfraTech Solutions', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(11, 'Insight Analytics', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(12, 'AlgoSoft Systems', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(13, 'WebSpark IT Services', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(14, 'TalentBridge HR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(15, 'TestPro Labs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(16, 'MarketScope Consulting', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(17, 'Infotech Systems', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(18, 'company1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(19, 'AppWave Solutions', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(20, 'SecureNet Pvt Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:53', '2026-02-21 09:27:53'),
(21, 'WriteSmart Media', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:54', '2026-02-21 09:27:54'),
(22, 'Creative Pixel Studio', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:54', '2026-02-21 09:27:54'),
(23, 'SkyHost Technologies', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:54', '2026-02-21 09:27:54'),
(24, 'InnovateHub', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:54', '2026-02-21 09:27:54'),
(25, 'GreenLeaf Finance', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-21 09:27:54', '2026-02-21 09:27:54'),
(26, 'GreenLeaf Industries', NULL, 'https://www.greenleafindustries.com', 'Manufacturing', '200-500', 'Coimbatore, India', 'Hyderabad, Pune', 'Sustainable manufacturing for a better tomorrow.', 'We manufacture eco-friendly packaging products and export globally.', 'Sustainability, Integrity, Quality', 'careers@greenleafindustries.com', '', 0, '2026-02-21 10:40:17', '2026-02-21 10:43:18');

-- --------------------------------------------------------

--
-- Table structure for table `course_lessons`
--

CREATE TABLE `course_lessons` (
  `id` int(11) NOT NULL,
  `module_id` int(11) NOT NULL,
  `lesson_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext DEFAULT NULL,
  `resources` text DEFAULT NULL,
  `exercises` text DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course_modules`
--

CREATE TABLE `course_modules` (
  `id` int(11) NOT NULL,
  `transition_id` int(11) NOT NULL,
  `module_number` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_weeks` int(11) DEFAULT 1,
  `content` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `daily_tasks`
--

CREATE TABLE `daily_tasks` (
  `id` int(11) NOT NULL,
  `transition_id` int(11) NOT NULL,
  `task_title` varchar(255) NOT NULL,
  `task_description` text DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT 10,
  `day_number` int(11) NOT NULL,
  `module_number` int(11) DEFAULT NULL,
  `lesson_number` int(11) DEFAULT NULL,
  `is_completed` tinyint(1) DEFAULT 0,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `education`
--

CREATE TABLE `education` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `degree` varchar(255) NOT NULL,
  `field_of_study` varchar(255) DEFAULT NULL,
  `institution` varchar(255) NOT NULL,
  `start_year` year(4) NOT NULL,
  `end_year` year(4) DEFAULT NULL,
  `grade` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `education`
--

INSERT INTO `education` (`id`, `user_id`, `degree`, `field_of_study`, `institution`, `start_year`, `end_year`, `grade`, `created_at`, `updated_at`) VALUES
(1, 47, 'B.Sc.', 'Computer Science', 'Prajyoti Niketan College, Pudukad, Thrissur', '2012', '2015', '', '2026-02-23 01:24:40', '2026-02-23 01:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `interview_bookings`
--

CREATE TABLE `interview_bookings` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `slot_id` int(11) NOT NULL,
  `slot_datetime` datetime NOT NULL,
  `booking_status` enum('booked','rescheduled','cancelled','completed') DEFAULT 'booked',
  `reschedule_count` int(11) DEFAULT 0,
  `max_reschedules` int(11) DEFAULT 2,
  `can_reschedule` tinyint(1) DEFAULT 1,
  `booked_at` datetime NOT NULL,
  `last_rescheduled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_sessions`
--

CREATE TABLE `interview_sessions` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `application_id` int(11) NOT NULL,
  `session_id` varchar(100) NOT NULL,
  `position` varchar(255) NOT NULL,
  `conversation_history` longtext NOT NULL,
  `turn` int(11) NOT NULL DEFAULT 1,
  `max_turns` int(11) NOT NULL DEFAULT 10,
  `status` enum('active','completed','evaluated') NOT NULL DEFAULT 'active',
  `evaluation_data` longtext DEFAULT NULL,
  `technical_score` decimal(5,2) DEFAULT NULL,
  `communication_score` decimal(5,2) DEFAULT NULL,
  `problem_solving_score` decimal(5,2) DEFAULT NULL,
  `adaptability_score` decimal(5,2) DEFAULT NULL,
  `enthusiasm_score` decimal(5,2) DEFAULT NULL,
  `overall_rating` decimal(5,2) DEFAULT NULL,
  `ai_decision` enum('shortlisted','rejected') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interview_slots`
--

CREATE TABLE `interview_slots` (
  `id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `slot_date` date NOT NULL,
  `slot_time` time NOT NULL,
  `slot_datetime` datetime NOT NULL,
  `capacity` int(11) DEFAULT 1,
  `booked_count` int(11) DEFAULT 0,
  `is_available` tinyint(1) DEFAULT 1,
  `created_by` int(11) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `title` varchar(200) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `company` varchar(250) NOT NULL,
  `location` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `experience_level` varchar(100) DEFAULT NULL,
  `openings` int(11) DEFAULT 1,
  `min_ai_cutoff_score` int(11) DEFAULT 0,
  `ai_interview_policy` enum('OFF','OPTIONAL','REQUIRED_SOFT','REQUIRED_HARD') NOT NULL DEFAULT 'REQUIRED_HARD',
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `employment_type` varchar(50) DEFAULT 'Full-time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_id`, `company_id`, `title`, `category`, `company`, `location`, `description`, `required_skills`, `experience_level`, `openings`, `min_ai_cutoff_score`, `ai_interview_policy`, `status`, `created_at`, `employment_type`) VALUES
(56, 46, 1, 'PHP Developer', NULL, 'TechNova Solutions', 'Bangalore', 'We are looking for a PHP Developer to build and maintain web applications.', 'PHP, MySQL, HTML, CSS, JavaScript', '2-3 years', 3, 85, 'OPTIONAL', 'open', '2026-02-21 15:35:28', 'Full-time'),
(57, 46, 1, 'Mechanical Engineer', 'Engineering', 'TechNova Solutions', 'Coimbatore, India', 'Responsible for maintaining and improving manufacturing machinery', 'AutoCAD, SolidWorks, Machine Maintenance', '1-3 Years', 2, 80, 'REQUIRED_HARD', 'open', '2026-02-21 16:07:59', 'Full-time'),
(58, 48, 26, 'Quality Inspector', 'Manufacturing', 'GreenLeaf Industries', 'Pune, India', 'Ensure product quality and compliance with standards.', 'Quality Control, Inspection, ISO Standards', '2-5 Years', 1, 70, 'REQUIRED_HARD', 'open', '2026-02-21 16:15:54', 'Full-time'),
(59, 46, 1, 'job2', 'Manufacturing', 'TechNova Solutions', 'Thrissur, Kerala', 'aaaaa', 'tool n die', '2-3 years', 3, 45, 'REQUIRED_HARD', 'open', '2026-02-24 14:07:09', 'Full-time'),
(60, 46, 1, 'job3', 'information tachnology', 'TechNova Solutions', 'Bangalore, Karnataka', 'aaaaa', '', '', 3, 80, 'REQUIRED_HARD', 'open', '2026-02-24 14:14:47', 'Full-time');

-- --------------------------------------------------------

--
-- Table structure for table `job_suggestions`
--

CREATE TABLE `job_suggestions` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `score` decimal(5,2) DEFAULT 0.00,
  `reason` text DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `version` varchar(255) NOT NULL,
  `class` varchar(255) NOT NULL,
  `group` varchar(255) NOT NULL,
  `namespace` varchar(255) NOT NULL,
  `time` int(11) NOT NULL,
  `batch` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-01-20-082658', 'App\\Database\\Migrations\\CreateAiInterviewsTable', 'default', 'App', 1768897866, 1),
(2, '2026-01-21-124705', 'App\\Database\\Migrations\\CreateinterviewSessionsTable', 'default', 'App', 1768999649, 2),
(3, '2026-01-21-125510', 'App\\Database\\Migrations\\CreateInterviewSessionsTable', 'default', 'App', 1769000373, 3),
(4, '2026-02-20-090000', 'App\\Database\\Migrations\\AddAiInterviewPolicyToJobs', 'default', 'App', 1771578796, 4),
(5, '2026-02-20-100000', 'App\\Database\\Migrations\\AddRecruiterVerificationFieldsToUsers', 'default', 'App', 1771578796, 4),
(6, '2026-02-21-120000', 'App\\Database\\Migrations\\AddRecruiterCompanyProfileFieldsToUsers', 'default', 'App', 1771661764, 5),
(7, '2026-02-21-123000', 'App\\Database\\Migrations\\EnsureCompanyProfilesTable', 'default', 'App', 1771661764, 5),
(8, '2026-02-21-130000', 'App\\Database\\Migrations\\CreateCompaniesTable', 'default', 'App', 1771666073, 6),
(9, '2026-02-21-131000', 'App\\Database\\Migrations\\AddCompanyIdToUsersAndJobs', 'default', 'App', 1771666074, 6),
(10, '2026-02-21-132000', 'App\\Database\\Migrations\\ChangeJobsExperienceLevelToVarchar', 'default', 'App', 1771668102, 7),
(11, '2026-02-23-100000', 'App\\Database\\Migrations\\CreateSavedJobsTable', 'default', 'App', 1771826565, 8),
(12, '2026-02-23-120000', 'App\\Database\\Migrations\\CreateRecruiterCandidateActionsTable', 'default', 'App', 1771829444, 9),
(13, '2026-02-23-130000', 'App\\Database\\Migrations\\CreateRecruiterCandidateMessagesTable', 'default', 'App', 1771843012, 10),
(14, '2026-02-23-150000', 'App\\Database\\Migrations\\AddGoogleIdToUsers', 'default', 'App', 1771844544, 11);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `type` enum('resume_not_uploaded','ai_not_started','ai_incomplete','slot_not_booked','reschedule_required','interview_scheduled','result_published') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` datetime NOT NULL,
  `read_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `application_id`, `type`, `title`, `message`, `action_link`, `is_read`, `created_at`, `read_at`) VALUES
(1, 47, 75, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-02-23 07:24:59', '2026-02-23 07:30:26'),
(2, 47, 75, '', 'Contact Viewed', 'Rohith Kumar viewed your contact details.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-02-23 07:31:07', '2026-02-23 07:33:03'),
(3, 47, 75, '', 'Message from Recruiter', 'Rohith Kumar: Hi,\r\n\r\nThanks for applying. To proceed, please complete this short screening form:\r\n\r\n???? https://forms.gle/J38gwfPmk2gSqsEN7\r\n\r\nWe only review applicants who submit this form.\r\n\r\nThanks', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-02-23 10:29:58', '2026-02-23 10:30:31'),
(4, 47, 75, '', 'Message from Recruiter', 'Rohith Kumar: Hello,\r\n\r\nThank you for your interest in our Full Stack Engineer / Intern - C# opportunity, please fill the below form.\r\n\r\nhttps://forms.gle/xYjoWAxPBhjcXS5U7\r\n\r\nThanks and Regards\r\nHarshitha J', 'http://localhost/ai-job-portal/public/candidate/messages/46?application_id=75', 1, '2026-02-23 10:37:59', '2026-02-23 10:38:37'),
(5, 47, 75, '', 'Message from Recruiter', 'Rohith Kumar sent you a message. Open conversation to read it.', 'http://localhost/ai-job-portal/public/candidate/messages/46?application_id=75', 1, '2026-02-23 10:43:19', '2026-02-23 10:43:37'),
(6, 46, 75, '', 'Candidate Replied', 'Manju Aravind replied to your message.', 'http://localhost/ai-job-portal/public/recruiter/candidate/47?application_id=75&show_contact=1', 1, '2026-02-23 10:47:02', '2026-02-23 10:47:28'),
(7, 47, 75, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-02-23 10:47:29', '2026-02-24 06:38:44'),
(12, 47, 75, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-24 08:28:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recruiter_candidate_actions`
--

CREATE TABLE `recruiter_candidate_actions` (
  `id` int(11) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `action_type` varchar(50) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `recruiter_candidate_actions`
--

INSERT INTO `recruiter_candidate_actions` (`id`, `candidate_id`, `recruiter_id`, `application_id`, `job_id`, `action_type`, `created_at`) VALUES
(11, 47, 46, 75, 57, 'profile_viewed', '2026-02-23 07:24:59'),
(12, 47, 46, 75, 57, 'contact_viewed', '2026-02-23 07:31:07'),
(13, 47, 46, 75, NULL, 'profile_viewed', '2026-02-23 10:47:29'),
(14, 49, 46, 76, 56, 'profile_viewed', '2026-02-23 11:31:09'),
(15, 49, 46, 76, 56, 'contact_viewed', '2026-02-23 11:31:13'),
(16, 49, 46, 76, 56, 'resume_downloaded', '2026-02-24 06:09:41'),
(17, 47, 46, 75, 57, 'resume_downloaded', '2026-02-24 06:17:03'),
(18, 47, 46, 75, 57, 'profile_viewed', '2026-02-24 08:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `recruiter_candidate_messages`
--

CREATE TABLE `recruiter_candidate_messages` (
  `id` int(11) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `application_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `sender_id` int(11) NOT NULL,
  `sender_role` varchar(20) NOT NULL,
  `message` text NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `recruiter_candidate_messages`
--

INSERT INTO `recruiter_candidate_messages` (`id`, `candidate_id`, `recruiter_id`, `application_id`, `job_id`, `sender_id`, `sender_role`, `message`, `created_at`) VALUES
(1, 47, 46, 75, 57, 46, 'recruiter', 'Hello,\r\n\r\nThank you for your interest in our Full Stack Engineer / Intern - C# opportunity, please fill the below form.\r\n\r\nhttps://forms.gle/xYjoWAxPBhjcXS5U7\r\n\r\nThanks and Regards\r\nHarshitha J', '2026-02-23 10:37:59'),
(2, 47, 46, 75, NULL, 47, 'candidate', 'Okey mam. i will fill and update you soon', '2026-02-23 10:39:17'),
(3, 47, 46, 75, NULL, 47, 'candidate', 'Okey mam. i will fill and update you soon', '2026-02-23 10:39:18'),
(4, 47, 46, 75, NULL, 47, 'candidate', 'yes i updated', '2026-02-23 10:39:54'),
(5, 47, 46, 75, 57, 46, 'recruiter', 'ok iwill check and update', '2026-02-23 10:43:19'),
(6, 47, 46, 75, NULL, 47, 'candidate', 'thankyou', '2026-02-23 10:43:49'),
(7, 47, 46, 75, NULL, 47, 'candidate', 'mam, have any update?', '2026-02-23 10:47:02');

-- --------------------------------------------------------

--
-- Table structure for table `reschedule_history`
--

CREATE TABLE `reschedule_history` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `old_slot_id` int(11) NOT NULL,
  `new_slot_id` int(11) NOT NULL,
  `old_slot_datetime` datetime NOT NULL,
  `new_slot_datetime` datetime NOT NULL,
  `reason` text DEFAULT NULL,
  `rescheduled_by` enum('candidate','admin') DEFAULT 'candidate',
  `rescheduled_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `saved_jobs`
--

CREATE TABLE `saved_jobs` (
  `id` int(11) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `saved_jobs`
--

INSERT INTO `saved_jobs` (`id`, `candidate_id`, `job_id`, `created_at`, `updated_at`) VALUES
(2, 47, 56, '2026-02-23 06:25:45', '2026-02-23 06:25:45'),
(3, 49, 58, '2026-02-23 12:40:01', '2026-02-23 12:40:01');

-- --------------------------------------------------------

--
-- Table structure for table `skills`
--

CREATE TABLE `skills` (
  `id` int(11) NOT NULL,
  `skill_name` varchar(100) DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `aliases` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `skills`
--

INSERT INTO `skills` (`id`, `skill_name`, `category`, `aliases`) VALUES
(1, 'PHP', 'Backend', 'PHP8,Core PHP,PHP Programming'),
(2, 'Laravel', 'Backend Framework', 'Laravel Framework,Laravel MVC'),
(3, 'CodeIgniter', 'Backend Framework', 'CI,CodeIgniter 3,CodeIgniter 4'),
(4, 'MySQL', 'Database', 'MySQL DB,SQL Database'),
(5, 'PostgreSQL', 'Database', 'Postgres,PostgreSQL DB'),
(6, 'JavaScript', 'Frontend', 'JS,ECMAScript'),
(7, 'React', 'Frontend Framework', 'ReactJS,React Library'),
(8, 'HTML', 'Frontend', 'HTML5,Web Markup'),
(9, 'CSS', 'Frontend', 'CSS3,Stylesheet'),
(10, 'Bootstrap', 'Frontend Framework', 'Bootstrap 4,Bootstrap 5'),
(11, 'Node.js', 'Backend', 'Node,NodeJS'),
(12, 'Express.js', 'Backend Framework', 'Express,ExpressJS'),
(13, 'MongoDB', 'Database', 'Mongo DB,NoSQL DB'),
(14, 'REST API', 'Backend', 'RESTful API,Web API'),
(15, 'Git', 'Version Control', 'GitHub,GitLab,Bitbucket'),
(16, 'Docker', 'DevOps', 'Containerization,Docker Engine'),
(17, 'AWS', 'Cloud', 'Amazon Web Services,AWS Cloud'),
(18, 'Python', 'Backend', 'Python3,Python Programming'),
(19, 'Django', 'Backend Framework', 'Django Framework'),
(20, 'Selenium', 'Testing', 'Selenium WebDriver,Automation Testing');

-- --------------------------------------------------------

--
-- Table structure for table `stage_history`
--

CREATE TABLE `stage_history` (
  `id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `stage_name` varchar(50) NOT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `stage_history`
--

INSERT INTO `stage_history` (`id`, `application_id`, `stage_name`, `start_time`, `end_time`) VALUES
(1, 74, 'Applied', '2026-02-21 10:51:48', NULL),
(2, 75, 'Applied', '2026-02-23 07:15:19', '2026-02-23 12:21:42'),
(3, 76, 'Applied', '2026-02-23 11:30:52', '2026-02-23 12:21:32'),
(4, 76, 'Shortlisted (Recruiter Override)', '2026-02-23 12:21:32', NULL),
(5, 75, 'Shortlisted (Recruiter Override)', '2026-02-23 12:21:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `google_id` varchar(191) DEFAULT NULL,
  `preferred_language` varchar(5) DEFAULT 'en',
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('candidate','recruiter','','') NOT NULL,
  `company_name` varchar(255) DEFAULT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_verification_token` varchar(128) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `phone_otp` varchar(10) DEFAULT NULL,
  `phone_otp_expires_at` datetime DEFAULT NULL,
  `phone_verified_at` datetime DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `resume_path` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `bio` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `google_id`, `preferred_language`, `phone`, `role`, `company_name`, `company_id`, `email_verification_token`, `email_verified_at`, `phone_otp`, `phone_otp_expires_at`, `phone_verified_at`, `password`, `created_at`, `resume_path`, `profile_photo`, `location`, `bio`) VALUES
(46, 'Rohith Kumar', 'rohith@technova.com', NULL, 'en', '+919544104305', 'recruiter', 'TechNova Solutions', 1, NULL, NULL, NULL, NULL, '2026-02-21 09:55:45', '$2y$10$tE9gbIZsmrhYj3JT0GUUr.Ra9rusYsib3.sglY1aTUA/80Qe4ENwi', '2026-02-21 15:25:05', NULL, NULL, NULL, NULL),
(47, 'Manju Aravind', 'manju@gmail.com', NULL, 'en', '1234567890', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$NyfTmre9jA2XQ7YQyaA5dOFUht/7z2AYgaVvOQn0O/JnFz.oULaTe', '2026-02-21 15:39:46', 'uploads/resumes/Rinu_George_Resume_14.pdf', 'uploads/profiles/47_1771671062.jpg', 'BANGALORE', ''),
(48, 'Asha Govind', 'asha@greenleaf.com', NULL, 'en', '+919544104305', 'recruiter', 'GreenLeaf Industries', 26, NULL, NULL, NULL, NULL, '2026-02-21 10:40:38', '$2y$10$vnhHNHOsFGO3BoRU8ORdkO7scVwwPBJZWKAXD7907xXQsYdujrAfe', '2026-02-21 16:10:17', NULL, NULL, NULL, NULL),
(49, 'rinu george', 'rinugeorgep@gmail.com', '110489513847967949727', 'en', '09747751235', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$aX/2kGvTsL77jRiGRl9c9uF3csUzIulWshhaTrNuGDzASo4EJtY1q', '2026-02-23 16:58:13', 'uploads/resumes/Rinu_George_Resume_15.pdf', '', 'BANGALORE', '');

-- --------------------------------------------------------

--
-- Table structure for table `work_experiences`
--

CREATE TABLE `work_experiences` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `job_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `employment_type` enum('Full-time','Part-time','Contract','Internship','Freelance') DEFAULT 'Full-time',
  `location` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `is_current` tinyint(1) DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `work_experiences`
--

INSERT INTO `work_experiences` (`id`, `user_id`, `job_title`, `company_name`, `employment_type`, `location`, `start_date`, `end_date`, `is_current`, `description`, `created_at`, `updated_at`) VALUES
(1, 49, 'Software Developer', 'SANDS Lab', 'Full-time', 'Thrissur, Kerala', '2020-02-03', '2022-06-24', 0, '', '2026-02-24 05:08:33', '2026-02-24 05:08:33'),
(2, 49, 'Web Developer', 'KJP Digital Solutions Pvt Ltd', 'Full-time', 'Thrissur, Kerala', '2023-06-26', '2024-06-28', 0, '', '2026-02-24 05:09:45', '2026-02-24 05:09:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `candidate_interests`
--
ALTER TABLE `candidate_interests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `career_transitions`
--
ALTER TABLE `career_transitions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `idx_career_path_lookup` (`candidate_id`,`current_role`,`target_role`);

--
-- Indexes for table `certifications`
--
ALTER TABLE `certifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `companies`
--
ALTER TABLE `companies`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `companies_name_unique` (`name`);

--
-- Indexes for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD PRIMARY KEY (`id`),
  ADD KEY `module_id` (`module_id`);

--
-- Indexes for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transition_id` (`transition_id`);

--
-- Indexes for table `daily_tasks`
--
ALTER TABLE `daily_tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `transition_id` (`transition_id`);

--
-- Indexes for table `education`
--
ALTER TABLE `education`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `interview_bookings`
--
ALTER TABLE `interview_bookings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_application` (`application_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `slot_id` (`slot_id`),
  ADD KEY `booking_status` (`booking_status`);

--
-- Indexes for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_id` (`session_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `user_id_status` (`user_id`,`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `interview_slots`
--
ALTER TABLE `interview_slots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `slot_datetime` (`slot_datetime`),
  ADD KEY `is_available` (`is_available`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `recruiter_id` (`recruiter_id`),
  ADD KEY `jobs_company_id_idx` (`company_id`);

--
-- Indexes for table `job_suggestions`
--
ALTER TABLE `job_suggestions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_suggestion` (`candidate_id`,`job_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `is_read` (`is_read`),
  ADD KEY `type` (`type`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `idx_user_unread` (`user_id`,`is_read`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Indexes for table `recruiter_candidate_actions`
--
ALTER TABLE `recruiter_candidate_actions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `recruiter_id` (`recruiter_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `action_type` (`action_type`);

--
-- Indexes for table `recruiter_candidate_messages`
--
ALTER TABLE `recruiter_candidate_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `recruiter_id` (`recruiter_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `reschedule_history`
--
ALTER TABLE `reschedule_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `saved_jobs_candidate_job_unique` (`candidate_id`,`job_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `stage_history`
--
ALTER TABLE `stage_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `application_id` (`application_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_google_id_unique` (`google_id`),
  ADD KEY `users_company_id_idx` (`company_id`);

--
-- Indexes for table `work_experiences`
--
ALTER TABLE `work_experiences`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidate_interests`
--
ALTER TABLE `candidate_interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `career_transitions`
--
ALTER TABLE `career_transitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=283;

--
-- AUTO_INCREMENT for table `daily_tasks`
--
ALTER TABLE `daily_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interview_bookings`
--
ALTER TABLE `interview_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interview_slots`
--
ALTER TABLE `interview_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT for table `job_suggestions`
--
ALTER TABLE `job_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recruiter_candidate_actions`
--
ALTER TABLE `recruiter_candidate_actions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `recruiter_candidate_messages`
--
ALTER TABLE `recruiter_candidate_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `reschedule_history`
--
ALTER TABLE `reschedule_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stage_history`
--
ALTER TABLE `stage_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `work_experiences`
--
ALTER TABLE `work_experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applications`
--
ALTER TABLE `applications`
  ADD CONSTRAINT `applications_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `applications_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`);

--
-- Constraints for table `candidate_interests`
--
ALTER TABLE `candidate_interests`
  ADD CONSTRAINT `candidate_interests_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  ADD CONSTRAINT `candidate_skills_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `career_transitions`
--
ALTER TABLE `career_transitions`
  ADD CONSTRAINT `career_transitions_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `certifications`
--
ALTER TABLE `certifications`
  ADD CONSTRAINT `certifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_lessons`
--
ALTER TABLE `course_lessons`
  ADD CONSTRAINT `course_lessons_ibfk_1` FOREIGN KEY (`module_id`) REFERENCES `course_modules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `course_modules`
--
ALTER TABLE `course_modules`
  ADD CONSTRAINT `course_modules_ibfk_1` FOREIGN KEY (`transition_id`) REFERENCES `career_transitions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `daily_tasks`
--
ALTER TABLE `daily_tasks`
  ADD CONSTRAINT `daily_tasks_ibfk_1` FOREIGN KEY (`transition_id`) REFERENCES `career_transitions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `education`
--
ALTER TABLE `education`
  ADD CONSTRAINT `education_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_company_id_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `job_suggestions`
--
ALTER TABLE `job_suggestions`
  ADD CONSTRAINT `job_suggestions_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `job_suggestions_ibfk_2` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `recruiter_candidate_actions`
--
ALTER TABLE `recruiter_candidate_actions`
  ADD CONSTRAINT `recruiter_candidate_actions_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `recruiter_candidate_actions_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recruiter_candidate_actions_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `recruiter_candidate_actions_recruiter_id_foreign` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recruiter_candidate_messages`
--
ALTER TABLE `recruiter_candidate_messages`
  ADD CONSTRAINT `recruiter_candidate_messages_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `recruiter_candidate_messages_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recruiter_candidate_messages_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `recruiter_candidate_messages_recruiter_id_foreign` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  ADD CONSTRAINT `saved_jobs_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `saved_jobs_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `stage_history`
--
ALTER TABLE `stage_history`
  ADD CONSTRAINT `stage_history_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`);

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_company_id_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `work_experiences`
--
ALTER TABLE `work_experiences`
  ADD CONSTRAINT `work_experiences_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
