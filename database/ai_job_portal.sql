-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2026 at 10:03 AM
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
  `resume_version_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `status` enum('applied','ai_interview_started','ai_interview_completed','ai_evaluated','shortlisted','rejected','interview_slot_booked','selected','hired','withdrawn') DEFAULT NULL,
  `interview_slot` datetime DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `candidate_id`, `resume_version_id`, `job_id`, `status`, `interview_slot`, `booking_id`, `applied_at`) VALUES
(1, 2, NULL, 6, 'applied', NULL, NULL, '2026-03-07 06:29:46');

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
(1, 2, 'rinudeepak', 3, 62, 'HTML,PHP,CSS,JavaScript,Hack', 3, '2026-03-06 12:02:32');

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
(1, 2, 'Backend Development');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_profiles`
--

CREATE TABLE `candidate_profiles` (
  `user_id` int(11) NOT NULL,
  `headline` varchar(255) DEFAULT NULL,
  `location` varchar(150) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `key_skills` text DEFAULT NULL,
  `preferred_locations` varchar(500) DEFAULT NULL,
  `current_salary` decimal(10,2) DEFAULT NULL,
  `expected_salary` decimal(10,2) DEFAULT NULL,
  `notice_period` varchar(50) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `candidate_profiles`
--

INSERT INTO `candidate_profiles` (`user_id`, `headline`, `location`, `bio`, `resume_path`, `profile_photo`, `key_skills`, `preferred_locations`, `current_salary`, `expected_salary`, `notice_period`, `created_at`, `updated_at`) VALUES
(2, '', NULL, NULL, 'uploads/resumes/Rinu_George_Resume_16.pdf', NULL, NULL, 'Bangalore', 3.30, 4.00, 'Immediate', '2026-03-06 12:01:26', '2026-03-07 04:46:01');

-- --------------------------------------------------------

--
-- Table structure for table `candidate_projects`
--

CREATE TABLE `candidate_projects` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `project_name` varchar(180) NOT NULL,
  `role_name` varchar(180) DEFAULT NULL,
  `tech_stack` varchar(255) DEFAULT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `project_summary` text DEFAULT NULL,
  `impact_metrics` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `candidate_resume_versions`
--

CREATE TABLE `candidate_resume_versions` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) DEFAULT NULL,
  `application_id` int(11) DEFAULT NULL,
  `career_transition_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `target_role` varchar(180) DEFAULT NULL,
  `source_role` varchar(180) DEFAULT NULL,
  `generation_source` varchar(50) NOT NULL DEFAULT 'role_based',
  `base_resume_path` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `highlight_skills` text DEFAULT NULL,
  `content` longtext NOT NULL,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `last_synced_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
(2, 2, 'PHP, MySQL, JavaScript', '2026-03-07 04:46:03');

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
  `culture_summary` text DEFAULT NULL,
  `employee_benefits` text DEFAULT NULL,
  `workplace_photos` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contact_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `website`, `industry`, `size`, `hq`, `branches`, `short_description`, `what_we_do`, `mission_values`, `culture_summary`, `employee_benefits`, `workplace_photos`, `contact_email`, `contact_phone`, `contact_public`, `created_at`, `updated_at`) VALUES
(1, 'TechNova Solutions', 'uploads/company_logos/1772797666_1de3e808ef5b10255dc2.jpg', '', '', '', 'Bangalore', 'Bangalore, Kochi, Pune, Hyderabad, Chennai', '', '', '', '', '', '[\"uploads\\/company_branding\\/1772797666_bbda61ca6960368af974.jpg\",\"uploads\\/company_branding\\/1772797666_69ee990c60d9e2e4a4d0.jpg\",\"uploads\\/company_branding\\/1772797666_9589276903dce23ee7f1.jpg\"]', '', '', 0, '2026-03-06 11:41:11', '2026-03-06 11:47:46'),
(2, 'PrecisionTech Industries', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 11:52:06', '2026-03-06 11:52:06');

-- --------------------------------------------------------

--
-- Table structure for table `company_reviews`
--

CREATE TABLE `company_reviews` (
  `id` int(11) UNSIGNED NOT NULL,
  `company_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `review_type` varchar(20) DEFAULT 'interview',
  `rating` tinyint(1) NOT NULL,
  `headline` varchar(180) DEFAULT NULL,
  `review_text` text DEFAULT NULL,
  `pros` text DEFAULT NULL,
  `cons` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'published',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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
(1, 2, 'B.Sc.', 'Computer Science', 'Prajyoti Niketan College, Pudukad, Thrissur', '2012', '2015', '', '2026-03-06 06:34:14', '2026-03-06 06:34:14');

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
  `employment_type` varchar(50) DEFAULT 'Full-time',
  `salary_range` varchar(100) DEFAULT NULL,
  `application_deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_id`, `company_id`, `title`, `category`, `company`, `location`, `description`, `required_skills`, `experience_level`, `openings`, `min_ai_cutoff_score`, `ai_interview_policy`, `status`, `created_at`, `employment_type`, `salary_range`, `application_deadline`) VALUES
(1, 1, 1, 'Quality Inspector', 'Manufacturing', 'TechNova Solutions', 'Pune, India', 'Inspect manufactured products and ensure they meet quality standards. Identify defects and improve production quality.', 'Quality Control, Inspection Tools, Documentation, ISO Standards', '1-2 Years', 4, 75, 'OPTIONAL', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(2, 1, 1, 'Frontend Developer', 'IT / Software', 'TechNova Solutions', 'Hyderabad, India', 'Develop responsive web interfaces and collaborate with backend developers to build modern web applications.', 'HTML, CSS, JavaScript, Bootstrap, React', '2-4 Years', 2, 82, 'OPTIONAL', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(3, 1, 1, 'Data Analyst', 'Analytics', 'TechNova Solutions', 'Chennai, India', 'Analyze business data, identify trends, and create reports for decision making.', 'Python, SQL, Excel, Power BI', '1-3 Years', 3, 80, 'REQUIRED_SOFT', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(4, 1, 1, 'HR Executive', 'Human Resources', 'TechNova Solutions', 'Kochi, India', 'Handle recruitment processes, employee engagement, and HR documentation.', 'Recruitment, Communication, HRMS, Interview Coordination', '1-2 Years', 1, 70, 'OPTIONAL', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(5, 1, 1, 'DevOps Engineer', 'IT / Cloud', 'TechNova Solutions', 'Bangalore, India', 'Manage CI/CD pipelines, cloud infrastructure, and deployment automation.', 'Docker, Kubernetes, AWS, CI/CD, Linux', '3-5 Years', 2, 85, 'REQUIRED_HARD', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(6, 3, 2, 'Backend Developer', 'IT / Software', 'PrecisionTech Industries', 'Bangalore, India', 'Develop and maintain server-side logic and APIs for scalable web applications.', 'PHP, Laravel, MySQL, JavaScript', '2-3 years', 1, 80, 'OPTIONAL', 'open', '2026-03-06 17:25:19', 'Full-time', NULL, '2026-03-31'),
(7, 3, 2, 'Electrical Engineer', 'Engineering', 'PrecisionTech Industries', 'Chennai, India', 'Design, develop, and maintain electrical systems for industrial applications.', 'Circuit Design, PLC, Electrical Testing, AutoCAD', '1-3 Years', 2, 78, 'REQUIRED_HARD', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(8, 3, 2, 'Digital Marketing Executive', 'Marketing', 'PrecisionTech Industries', 'Chennai, India', 'Plan and execute digital marketing campaigns including SEO, SEM, and social media.', 'SEO, Google Ads, Social Media Marketing, Content Marketing', '1-2 Years', 2, 70, 'OPTIONAL', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(9, 3, 2, 'UI/UX Designer', 'Design', 'PrecisionTech Industries', 'Chennai, India', 'Design intuitive user interfaces and improve user experience for web and mobile apps.', 'Figma, Adobe XD, Wireframing, Prototyping', '2-3 Years', 1, 75, 'OPTIONAL', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(10, 3, 2, 'Mechanical Design Engineer', 'Manufacturing', 'PrecisionTech Industries', 'Chennai, India', 'Design mechanical components and develop product prototypes.', 'SolidWorks, AutoCAD, Product Design, Mechanical Analysis', '2-5 Years', 2, 82, 'REQUIRED_HARD', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(11, 3, 2, 'Python Developer', 'IT / Software', 'PrecisionTech Industries', 'Chennai, India', 'Build backend services and data processing pipelines using Python.', 'Python, Django, Flask, PostgreSQL, REST API', '2-4 Years', 3, 85, 'OPTIONAL', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_alerts`
--

CREATE TABLE `job_alerts` (
  `id` int(11) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `role_keywords` varchar(255) DEFAULT NULL,
  `location_keywords` varchar(255) DEFAULT NULL,
  `skills_keywords` varchar(255) DEFAULT NULL,
  `salary_min` int(11) DEFAULT NULL,
  `salary_max` int(11) DEFAULT NULL,
  `notify_email` tinyint(1) NOT NULL DEFAULT 1,
  `notify_in_app` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_alert_deliveries`
--

CREATE TABLE `job_alert_deliveries` (
  `id` int(11) UNSIGNED NOT NULL,
  `job_alert_id` int(11) UNSIGNED NOT NULL,
  `job_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `email_sent_at` datetime DEFAULT NULL,
  `in_app_sent_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

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

--
-- Dumping data for table `job_suggestions`
--

INSERT INTO `job_suggestions` (`id`, `candidate_id`, `job_id`, `score`, `reason`, `created_at`) VALUES
(1, 2, 6, 95.00, 'Strong skill match in PHP, MySQL, and JavaScript for Backend Development.', '2026-03-07 06:05:41'),
(2, 2, 2, 70.00, 'Related skills in JavaScript and interest in IT.', '2026-03-07 06:05:41'),
(3, 2, 11, 60.00, 'Related skills in Python and interest in software development.', '2026-03-07 06:05:41'),
(4, 2, 3, 50.00, 'Some related skills in SQL and interest in analytics.', '2026-03-07 06:05:41'),
(5, 2, 1, 20.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41'),
(6, 2, 4, 15.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41'),
(7, 2, 5, 10.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41'),
(8, 2, 9, 5.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41'),
(9, 2, 10, 5.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41'),
(10, 2, 8, 5.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41'),
(11, 2, 7, 5.00, 'No relevant skills but a full-time opportunity.', '2026-03-07 06:05:41');

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
(14, '2026-02-23-150000', 'App\\Database\\Migrations\\AddGoogleIdToUsers', 'default', 'App', 1771844544, 11),
(15, '2026-02-25-100000', 'App\\Database\\Migrations\\DropUnusedPhoneOtpColumnsFromUsers', 'default', 'App', 1772016233, 12),
(16, '2026-02-25-110000', 'App\\Database\\Migrations\\CreateRecruiterCandidateNotesTable', 'default', 'App', 1772022056, 13),
(17, '2026-02-27-120000', 'App\\Database\\Migrations\\CreateJobAlertsTables', 'default', 'App', 1772174422, 14),
(18, '2026-02-28-120000', 'App\\Database\\Migrations\\CreateCandidateResumeVersionsTable', 'default', 'App', 1772258325, 15),
(19, '2026-02-28-140000', 'App\\Database\\Migrations\\CreateCandidateProjectsTable', 'default', 'App', 1772263750, 16),
(20, '2026-03-02-120000', 'App\\Database\\Migrations\\AddWithdrawnStatusToApplications', 'default', 'App', 1772430665, 17),
(21, '2026-03-02-130000', 'App\\Database\\Migrations\\AddPasswordResetFieldsToUsers', 'default', 'App', 1772430851, 18),
(22, '2026-03-03-140000', 'App\\Database\\Migrations\\AddEmployerBrandingFieldsToCompanies', 'default', 'App', 1772519125, 19),
(23, '2026-03-01-100000', 'App\\Database\\Migrations\\AddCandidateCareerFields', 'default', 'App', 1772605523, 20),
(24, '2026-03-04-180000', 'App\\Database\\Migrations\\CreateCompanyReviewsTable', 'default', 'App', 1772624900, 21),
(25, '2026-03-05-090000', 'App\\Database\\Migrations\\AddReviewTypeToCompanyReviews', 'default', 'App', 1772695027, 22),
(26, '2026-03-06-120000', 'App\\Database\\Migrations\\AddSalaryRangeAndDeadlineToJobs', 'default', 'App', 1772786015, 23),
(27, '2026-03-06-130000', 'App\\Database\\Migrations\\CreateNormalizedUserProfileTables', 'default', 'App', 1772791448, 24),
(28, '2026-03-06-140000', 'App\\Database\\Migrations\\DropDeprecatedCandidateColumnsFromUsers', 'default', 'App', 1772792504, 25),
(29, '2026-03-06-150000', 'App\\Database\\Migrations\\DropDeprecatedRecruiterCompanyNameFromUsers', 'default', 'App', 1772792771, 26),
(30, '2026-03-06-160000', 'App\\Database\\Migrations\\DropPreferredLanguageFromUsers', 'default', 'App', 1772796868, 27),
(31, '2026-03-07-170000', 'App\\Database\\Migrations\\CleanupLegacyPhpInterviewArtifacts', 'default', 'App', 1772867685, 28);

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
(1, 2, NULL, '', 'Contact Viewed', 'Rohith Kumar viewed your contact details.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-07 04:29:14', '2026-03-07 08:30:49'),
(2, 2, NULL, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-03-07 04:29:17', NULL);

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
(1, 2, 1, NULL, NULL, 'contact_viewed', '2026-03-07 04:29:14'),
(2, 2, 1, NULL, NULL, 'profile_viewed', '2026-03-07 04:29:17');

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

-- --------------------------------------------------------

--
-- Table structure for table `recruiter_candidate_notes`
--

CREATE TABLE `recruiter_candidate_notes` (
  `id` int(11) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `tags` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `recruiter_company_map`
--

CREATE TABLE `recruiter_company_map` (
  `id` int(11) UNSIGNED NOT NULL,
  `recruiter_user_id` int(11) NOT NULL,
  `company_id` int(11) NOT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `recruiter_company_map`
--

INSERT INTO `recruiter_company_map` (`id`, `recruiter_user_id`, `company_id`, `is_admin`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, '2026-03-06 11:41:11', '2026-03-06 11:41:11'),
(2, 3, 2, 1, '2026-03-06 11:52:06', '2026-03-06 11:52:06'),
(3, 4, 1, 1, '2026-03-07 08:57:52', '2026-03-07 08:57:52');

-- --------------------------------------------------------

--
-- Table structure for table `recruiter_profiles`
--

CREATE TABLE `recruiter_profiles` (
  `user_id` int(11) NOT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `designation` varchar(150) DEFAULT NULL,
  `company_name_snapshot` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `recruiter_profiles`
--

INSERT INTO `recruiter_profiles` (`user_id`, `full_name`, `phone`, `designation`, `company_name_snapshot`, `created_at`, `updated_at`) VALUES
(1, 'Rohith Kumar', '+919544104305', 'Talent Aquisition Specialist', 'TechNova Solutions', '2026-03-06 11:41:11', '2026-03-06 11:47:46'),
(3, 'Arun Mohan', '+919544104305', 'HR Executive', 'PrecisionTech Industries', '2026-03-06 11:52:06', '2026-03-06 11:52:06'),
(4, 'Manu', '+919544104305', 'HR Manager', 'TechNova Solutions', '2026-03-07 08:57:52', '2026-03-07 08:57:52');

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
(1, 1, 'Applied', '2026-03-07 06:29:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `google_id` varchar(191) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('candidate','recruiter') NOT NULL,
  `company_id` int(11) DEFAULT NULL,
  `email_verification_token` varchar(128) DEFAULT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `password_reset_token` varchar(128) DEFAULT NULL,
  `password_reset_expires_at` datetime DEFAULT NULL,
  `phone_verified_at` datetime DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `google_id`, `phone`, `role`, `company_id`, `email_verification_token`, `email_verified_at`, `password_reset_token`, `password_reset_expires_at`, `phone_verified_at`, `password`, `created_at`) VALUES
(1, 'Rohith Kumar', 'rohith@technova.com', NULL, '+919544104305', 'recruiter', 1, NULL, '2026-03-06 11:41:15', NULL, NULL, '2026-03-06 11:41:29', '$2y$10$NdHPUx9Hxg4qfk3G2k2/oebDnZR0.SRgwBHLCwH4WF6HU4p6trfCO', '2026-03-06 17:11:11'),
(2, 'Manju Aravind', 'manju@gmail.com', NULL, '1234567890', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, '$2y$10$3ZtTFBBLs9HtT00GtL/GMeCbFrNFMTUEOKWyXnuWmxlvXhiOjrxVy', '2026-03-06 17:19:18'),
(3, 'Arun Mohan', 'arun@precisiontech.com', NULL, '+919544104305', 'recruiter', 2, NULL, '2026-03-06 11:52:11', NULL, NULL, '2026-03-06 11:52:21', '$2y$10$7VF7V5LOaW7K/js4HLtFVO2e8PtFrCKYX009kaWQUDGpfC.3DMkA6', '2026-03-06 17:22:06'),
(4, 'Manu', 'manu@technova.com', NULL, '+919544104305', 'recruiter', 1, NULL, '2026-03-07 08:57:54', NULL, NULL, '2026-03-07 08:58:07', '$2y$10$8dvzoelZOC5mfsC7J9LX3OleovjG6VzbIAS0k6vrUXSH.TkgUd.Ce', '2026-03-07 14:27:51');

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
(1, 2, 'Software Developer', 'SANDS Lab', 'Full-time', 'Thrissur, Kerala', '2020-02-03', '2022-06-24', 0, '', '2026-03-06 06:33:51', '2026-03-06 06:33:51'),
(2, 2, 'Web Developer', 'KJP Digital Solutions Pvt Ltd', 'Full-time', 'Thrissur, Kerala', '2023-06-26', '2024-06-29', 0, '', '2026-03-06 06:34:58', '2026-03-06 06:34:58');

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
-- Indexes for table `candidate_profiles`
--
ALTER TABLE `candidate_profiles`
  ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `candidate_projects`
--
ALTER TABLE `candidate_projects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `candidate_resume_versions`
--
ALTER TABLE `candidate_resume_versions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `career_transition_id` (`career_transition_id`);

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
-- Indexes for table `company_reviews`
--
ALTER TABLE `company_reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `company_id` (`company_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `company_id_candidate_id` (`company_id`,`candidate_id`);

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
-- Indexes for table `job_alerts`
--
ALTER TABLE `job_alerts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

--
-- Indexes for table `job_alert_deliveries`
--
ALTER TABLE `job_alert_deliveries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_alert_job_unique` (`job_alert_id`,`job_id`),
  ADD KEY `job_alert_id` (`job_alert_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `candidate_id` (`candidate_id`);

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
-- Indexes for table `recruiter_candidate_notes`
--
ALTER TABLE `recruiter_candidate_notes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `candidate_id_recruiter_id` (`candidate_id`,`recruiter_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `recruiter_id` (`recruiter_id`);

--
-- Indexes for table `recruiter_company_map`
--
ALTER TABLE `recruiter_company_map`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `recruiter_company_map_unique` (`recruiter_user_id`,`company_id`),
  ADD KEY `recruiter_user_id` (`recruiter_user_id`),
  ADD KEY `company_id` (`company_id`);

--
-- Indexes for table `recruiter_profiles`
--
ALTER TABLE `recruiter_profiles`
  ADD PRIMARY KEY (`user_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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
-- AUTO_INCREMENT for table `candidate_projects`
--
ALTER TABLE `candidate_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidate_resume_versions`
--
ALTER TABLE `candidate_resume_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `career_transitions`
--
ALTER TABLE `career_transitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `company_reviews`
--
ALTER TABLE `company_reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

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
-- AUTO_INCREMENT for table `interview_slots`
--
ALTER TABLE `interview_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `job_alerts`
--
ALTER TABLE `job_alerts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_alert_deliveries`
--
ALTER TABLE `job_alert_deliveries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_suggestions`
--
ALTER TABLE `job_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recruiter_candidate_actions`
--
ALTER TABLE `recruiter_candidate_actions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `recruiter_candidate_messages`
--
ALTER TABLE `recruiter_candidate_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruiter_candidate_notes`
--
ALTER TABLE `recruiter_candidate_notes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruiter_company_map`
--
ALTER TABLE `recruiter_company_map`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `reschedule_history`
--
ALTER TABLE `reschedule_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `saved_jobs`
--
ALTER TABLE `saved_jobs`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stage_history`
--
ALTER TABLE `stage_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
-- Constraints for table `candidate_profiles`
--
ALTER TABLE `candidate_profiles`
  ADD CONSTRAINT `candidate_profiles_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidate_projects`
--
ALTER TABLE `candidate_projects`
  ADD CONSTRAINT `candidate_projects_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `candidate_resume_versions`
--
ALTER TABLE `candidate_resume_versions`
  ADD CONSTRAINT `candidate_resume_versions_application_id_foreign` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`) ON DELETE CASCADE ON UPDATE SET NULL,
  ADD CONSTRAINT `candidate_resume_versions_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `candidate_resume_versions_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE SET NULL;

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
-- Constraints for table `company_reviews`
--
ALTER TABLE `company_reviews`
  ADD CONSTRAINT `company_reviews_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `company_reviews_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `job_alerts`
--
ALTER TABLE `job_alerts`
  ADD CONSTRAINT `job_alerts_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `job_alert_deliveries`
--
ALTER TABLE `job_alert_deliveries`
  ADD CONSTRAINT `job_alert_deliveries_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_alert_deliveries_job_alert_id_foreign` FOREIGN KEY (`job_alert_id`) REFERENCES `job_alerts` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `job_alert_deliveries_job_id_foreign` FOREIGN KEY (`job_id`) REFERENCES `jobs` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Constraints for table `recruiter_candidate_notes`
--
ALTER TABLE `recruiter_candidate_notes`
  ADD CONSTRAINT `recruiter_candidate_notes_candidate_id_foreign` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recruiter_candidate_notes_recruiter_id_foreign` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recruiter_company_map`
--
ALTER TABLE `recruiter_company_map`
  ADD CONSTRAINT `recruiter_company_map_company_fk` FOREIGN KEY (`company_id`) REFERENCES `companies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `recruiter_company_map_user_fk` FOREIGN KEY (`recruiter_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recruiter_profiles`
--
ALTER TABLE `recruiter_profiles`
  ADD CONSTRAINT `recruiter_profiles_user_fk` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

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
