-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 11:30 AM
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
  `status` enum('applied','ai_interview_started','ai_interview_completed','ai_evaluated','shortlisted','hold','rejected','interview_slot_booked','selected','hired','withdrawn') DEFAULT NULL,
  `interview_slot` datetime DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `candidate_id`, `resume_version_id`, `job_id`, `status`, `interview_slot`, `booking_id`, `applied_at`) VALUES
(1, 2, NULL, 6, 'applied', NULL, NULL, '2026-03-07 06:29:46'),
(2, 7, NULL, 6, 'applied', NULL, NULL, '2026-03-14 05:44:29'),
(3, 7, NULL, 2, 'applied', NULL, NULL, '2026-03-14 08:19:25'),
(4, 8, 2, 6, 'applied', NULL, NULL, '2026-03-21 10:45:10'),
(5, 8, 2, 1, 'shortlisted', '2026-03-23 17:00:00', 1, '2026-03-21 10:59:33');

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
(1, 2, 'rinudeepak', 3, 62, 'HTML,PHP,CSS,JavaScript,Hack', 3, '2026-03-06 12:02:32'),
(2, 5, 'rinudeepak', 3, 64, 'PHP,HTML,CSS,JavaScript,Hack', 3, '2026-03-13 08:56:01'),
(4, 8, 'rinudeepak', 3, 67, 'PHP,CSS,JavaScript,HTML,Hack', 3, '2026-03-21 10:01:49');

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
  `gender` varchar(20) DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `resume_path` varchar(255) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `key_skills` text DEFAULT NULL,
  `preferred_job_titles` varchar(255) DEFAULT NULL,
  `preferred_locations` varchar(500) DEFAULT NULL,
  `preferred_employment_type` varchar(50) DEFAULT NULL,
  `current_salary` decimal(10,2) DEFAULT NULL,
  `expected_salary` decimal(10,2) DEFAULT NULL,
  `notice_period` varchar(50) DEFAULT NULL,
  `allow_public_recruiter_visibility` tinyint(1) NOT NULL DEFAULT 1,
  `job_alerts_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `job_alert_notify_in_app` tinyint(1) NOT NULL DEFAULT 1,
  `job_alert_notify_email` tinyint(1) NOT NULL DEFAULT 1,
  `is_fresher_candidate` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `candidate_profiles`
--

INSERT INTO `candidate_profiles` (`user_id`, `headline`, `location`, `bio`, `gender`, `date_of_birth`, `resume_path`, `profile_photo`, `key_skills`, `preferred_job_titles`, `preferred_locations`, `preferred_employment_type`, `current_salary`, `expected_salary`, `notice_period`, `allow_public_recruiter_visibility`, `job_alerts_enabled`, `job_alert_notify_in_app`, `job_alert_notify_email`, `is_fresher_candidate`, `created_at`, `updated_at`) VALUES
(2, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Bangalore', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', NULL, NULL, 'uploads/resumes/Rinu_George_Resume_17.pdf', 'uploads/profiles/2_1774244525.jpg', NULL, NULL, 'Bangalore', NULL, 3.30, 4.00, 'Immediate', 1, 1, 1, 1, 0, '2026-03-06 12:01:26', '2026-03-23 05:42:05'),
(5, 'aaaaaaaaaaaaaaa', 'Kochi', 'wwwwwwwwwwwwwwwwwwwwwwwwwwww', 'Female', '1994-11-21', 'uploads/resumes/Rinu_George_Resume_19.pdf', NULL, NULL, NULL, 'Bangalore', NULL, NULL, 4.00, '1 Month', 0, 1, 1, 1, 1, '2026-03-13 06:29:36', '2026-03-13 09:53:28'),
(6, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Thrissur, Kerala', 'fffffffffffffffffffffffffffffffffffffffffffffffffffffffffff', 'Female', '1989-01-02', 'uploads/resumes/Rinu_George_Resume_22.pdf', NULL, NULL, 'web developer, software developer', 'Bangalore', 'Part-time', NULL, 5.00, 'Immediate', 1, 1, 1, 1, 1, '2026-03-20 09:06:46', '2026-03-20 09:08:32'),
(7, 'hhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 'Kochi', 'bvbbbbbbbbbbbbbbjkkkkkkkkkkkkkkknbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbbb', 'Male', '2026-03-04', 'uploads/resumes/Rinu_George_Resume_21.pdf', NULL, NULL, 'web developer, software developer', 'Bangalore', 'Full-time', NULL, NULL, 'Immediate', 1, 1, 1, 1, 1, '2026-03-14 05:17:03', '2026-03-14 07:22:39'),
(8, 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 'Hyderabad', 'kkkkkkkkkkkkkkkkkkkkkkkkkkkkkkk', 'Female', '2002-02-21', 'uploads/resumes/Rinu_George_Resume_23.pdf', 'uploads/profiles/8_1774244856.jpg', NULL, 'web developer, software developer', 'Hyderabad', 'Contract', NULL, 3.00, '1 Month', 1, 1, 1, 1, 1, '2026-03-21 08:50:39', '2026-03-23 05:47:36');

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

--
-- Dumping data for table `candidate_resume_versions`
--

INSERT INTO `candidate_resume_versions` (`id`, `candidate_id`, `job_id`, `application_id`, `career_transition_id`, `title`, `target_role`, `source_role`, `generation_source`, `base_resume_path`, `summary`, `highlight_skills`, `content`, `is_primary`, `last_synced_at`, `created_at`, `updated_at`) VALUES
(1, 5, 9, NULL, NULL, 'Java Developer Resume', 'Java Developer', 'Candidate', 'job_version', 'uploads/resumes/Rinu_George_Resume_19.pdf', 'Results-driven Java Developer with a strong foundation in PHP and a keen interest in building scalable applications. Proven ability to design intuitive user interfaces and enhance user experiences for web and mobile applications. Seeking to leverage technical skills in a challenging Java development role.', 'Java, UI/UX Design', '{\"template_key\":\"modern_professional\",\"name\":\"Arun George\",\"target_role\":\"Java Developer\",\"title\":\"Java Developer Resume\",\"summary\":\"Results-driven Java Developer with a strong foundation in PHP and a keen interest in building scalable applications. Proven ability to design intuitive user interfaces and enhance user experiences for web and mobile applications. Seeking to leverage technical skills in a challenging Java development role.\",\"highlight_skills\":[\"Java\",\"UI/UX Design\"],\"sections\":{\"skills\":{\"title\":\"Technical Skills\",\"groups\":[{\"label\":\"Languages\",\"items\":[\"Java\",\"PHP\",\"JavaScript\"]},{\"label\":\"Frameworks\",\"items\":[\"Spring\",\"React\"]}]},\"education\":{\"title\":\"Education\",\"items\":[{\"headline\":\"MBA\",\"subhead\":\"Adi Shankara Institute of Engineering and Technology\",\"meta\":\"2015 - 2017\",\"bullets\":[]}]}}}', 1, '2026-03-13 10:00:43', '2026-03-13 09:57:15', '2026-03-13 10:00:43'),
(2, 8, NULL, NULL, NULL, 'PHP Developer Resume', 'php developer', 'Candidate', 'role_based', 'uploads/resumes/Rinu_George_Resume_23.pdf', 'Detail-oriented PHP Developer with a strong foundation in Core PHP, MySQL, and WordPress. Proven ability to develop and maintain web applications, ensuring high performance and responsiveness. Seeking to leverage expertise in a dynamic team environment.', 'Core PHP, MySQL, WordPress', '{\"template_key\":\"modern_professional\",\"name\":\"Karthika\",\"target_role\":\"php developer\",\"title\":\"PHP Developer Resume\",\"summary\":\"Detail-oriented PHP Developer with a strong foundation in Core PHP, MySQL, and WordPress. Proven ability to develop and maintain web applications, ensuring high performance and responsiveness. Seeking to leverage expertise in a dynamic team environment.\",\"highlight_skills\":[\"Core PHP\",\"MySQL\",\"WordPress\"],\"sections\":{\"skills\":{\"title\":\"Technical Skills\",\"groups\":[{\"label\":\"Languages\",\"items\":[\"Core PHP\",\"MySQL\"]},{\"label\":\"CMS\",\"items\":[\"WordPress\"]}]},\"education\":{\"title\":\"Education\",\"items\":[{\"headline\":\"MBA in Marketing\",\"subhead\":\"Adi Shankara Institute of Engineering and Technology\",\"meta\":\"2019 - 2021\",\"bullets\":[]}]}}}', 0, '2026-03-21 09:45:17', '2026-03-21 09:45:17', '2026-03-21 11:31:13'),
(3, 8, NULL, NULL, NULL, 'Full Stack Developer', 'Full stach developer', 'Candidate', 'role_based', 'uploads/resumes/Rinu_George_Resume_23.pdf', 'Results-driven Full Stack Developer with a strong background in PHP and MySQL, seeking to leverage expertise in web development to create innovative solutions. Proven ability to deliver high-quality projects on time.', 'Core PHP, MySQL, WordPress', '{\"template_key\":\"modern_professional\",\"name\":\"Karthika\",\"target_role\":\"Full stach developer\",\"title\":\"Full Stack Developer\",\"summary\":\"Results-driven Full Stack Developer with a strong background in PHP and MySQL, seeking to leverage expertise in web development to create innovative solutions. Proven ability to deliver high-quality projects on time.\",\"highlight_skills\":[\"Core PHP\",\"MySQL\",\"WordPress\"],\"sections\":{\"skills\":{\"title\":\"Technical Skills\",\"groups\":[{\"label\":\"Languages\",\"items\":[\"Core PHP\",\"MySQL\"]},{\"label\":\"CMS\",\"items\":[\"WordPress\"]}]},\"education\":{\"title\":\"Education\",\"items\":[{\"headline\":\"MBA in Marketing\",\"subhead\":\"Adi Shankara Institute of Engineering and Technology\",\"meta\":\"2019 - 2021\",\"bullets\":[]}]}}}', 0, '2026-03-21 09:47:51', '2026-03-21 09:47:51', '2026-03-21 11:31:13'),
(4, 8, NULL, NULL, 1, 'Karthika - DevOps Engineer', 'DevOps Engineer', 'Core PHP, MySQL, WordPress', 'career_transition', 'uploads/resumes/Rinu_George_Resume_23.pdf', 'Transitioning from Core PHP and MySQL development to a DevOps Engineer role, leveraging strong programming skills and a focus on modern infrastructure practices including containerization and CI/CD.', 'Core PHP, MySQL, WordPress, Docker, Kubernetes', '{\"template_key\":\"modern_professional\",\"name\":\"Karthika\",\"target_role\":\"DevOps Engineer\",\"title\":\"Karthika - DevOps Engineer\",\"summary\":\"Transitioning from Core PHP and MySQL development to a DevOps Engineer role, leveraging strong programming skills and a focus on modern infrastructure practices including containerization and CI/CD.\",\"highlight_skills\":[\"Core PHP\",\"MySQL\",\"WordPress\",\"Docker\",\"Kubernetes\"],\"sections\":{\"skills\":{\"title\":\"Technical Skills\",\"groups\":[{\"label\":\"Languages\",\"items\":[\"PHP\",\"JavaScript\",\"CSS\",\"HTML\",\"Hack\"]},{\"label\":\"Frameworks\",\"items\":[\"WordPress\"]},{\"label\":\"DevOps Tools\",\"items\":[\"Docker\",\"Kubernetes\",\"Terraform\",\"Ansible\",\"Prometheus\",\"Grafana\"]}]},\"education\":{\"title\":\"Education\",\"items\":[{\"headline\":\"MBA in Marketing\",\"subhead\":\"Adi Shankara Institute of Engineering and Technology\",\"meta\":\"2019 - 2021\",\"bullets\":[]}]}}}', 1, '2026-03-21 11:31:13', '2026-03-21 11:31:13', '2026-03-21 11:31:13');

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
(2, 2, 'PHP, MySQL, JavaScript', '2026-03-07 04:46:03'),
(3, 5, 'PHP', '2026-03-13 06:30:02'),
(5, 7, 'PHP, MySQL, JavaScript', '2026-03-14 07:22:42'),
(6, 6, 'Core PHP, MySQL', '2026-03-20 09:07:24'),
(7, 8, 'Core PHP, MySQL, WordPress', '2026-03-21 08:51:19');

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

--
-- Dumping data for table `career_transitions`
--

INSERT INTO `career_transitions` (`id`, `candidate_id`, `current_role`, `target_role`, `skill_gaps`, `learning_roadmap`, `status`, `progress_percentage`, `created_at`, `deactivated_at`, `reactivated_at`, `reactivation_count`, `updated_at`, `course_status`) VALUES
(1, 8, 'Core PHP, MySQL, WordPress', 'DevOps Engineer', '[\"Linux command line proficiency\",\"Containerization (Docker, Kubernetes)\",\"CI\\/CD pipelines understanding\",\"Infrastructure as Code (Terraform, Ansible)\",\"Monitoring and logging tools (Prometheus, Grafana)\"]', '[{\"phase\":\"Phase 1\",\"duration\":\"4 weeks\",\"focus\":\"Learn the fundamentals of Linux and command line tools, essential for server management.\"},{\"phase\":\"Phase 2\",\"duration\":\"4 weeks\",\"focus\":\"Gain knowledge in containerization technologies, specifically Docker and Kubernetes.\"},{\"phase\":\"Phase 3\",\"duration\":\"4 weeks\",\"focus\":\"Understand CI\\/CD concepts and tools, and learn Infrastructure as Code practices.\"}]', 'active', 0, '2026-03-21 10:04:47', NULL, NULL, 0, '2026-03-21 10:04:47', 'pending'),
(2, 2, 'Web Developer', 'Data Analyst', '[\"Statistical Analysis\",\"Data Visualization\",\"SQL\"]', '[{\"phase\":\"Phase 1\",\"duration\":\"2 weeks\",\"focus\":\"Introduction to Data Analysis and Statistics\"},{\"phase\":\"Phase 2\",\"duration\":\"3 weeks\",\"focus\":\"Learning SQL and Database Management\"},{\"phase\":\"Phase 3\",\"duration\":\"2 weeks\",\"focus\":\"Data Visualization Techniques\"},{\"phase\":\"Phase 4\",\"duration\":\"1 week\",\"focus\":\"Capstone Project and Portfolio Development\"}]', 'active', 0, '2026-03-23 06:23:00', NULL, NULL, 0, '2026-03-23 06:23:00', 'pending');

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
  `office_tour_title` varchar(180) DEFAULT NULL,
  `office_tour_url` varchar(255) DEFAULT NULL,
  `office_tour_summary` text DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `contact_phone` varchar(30) DEFAULT NULL,
  `contact_public` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `companies`
--

INSERT INTO `companies` (`id`, `name`, `logo`, `website`, `industry`, `size`, `hq`, `branches`, `short_description`, `what_we_do`, `mission_values`, `culture_summary`, `employee_benefits`, `workplace_photos`, `office_tour_title`, `office_tour_url`, `office_tour_summary`, `contact_email`, `contact_phone`, `contact_public`, `created_at`, `updated_at`) VALUES
(1, 'TechNova Solutions', 'uploads/company_logos/1772797666_1de3e808ef5b10255dc2.jpg', '', '', '', 'Bangalore', 'Bangalore, Kochi, Pune, Hyderabad, Chennai', '', '', '', '', '', '[\"uploads\\/company_branding\\/1772797666_bbda61ca6960368af974.jpg\",\"uploads\\/company_branding\\/1772797666_69ee990c60d9e2e4a4d0.jpg\",\"uploads\\/company_branding\\/1772797666_9589276903dce23ee7f1.jpg\"]', 'Explore Our Workspace', 'https://vimeo.com/areaworkplaces/videos', 'Take a quick look at the team environment and workplace setup.', '', '', 0, '2026-03-06 11:41:11', '2026-03-14 06:25:58'),
(2, 'PrecisionTech Industries', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-06 11:52:06', '2026-03-06 11:52:06'),
(3, 'InsightData Labs', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-03-23 05:51:54', '2026-03-23 05:51:54');

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

--
-- Dumping data for table `company_reviews`
--

INSERT INTO `company_reviews` (`id`, `company_id`, `candidate_id`, `review_type`, `rating`, `headline`, `review_text`, `pros`, `cons`, `status`, `created_at`, `updated_at`) VALUES
(1, 2, 8, 'interview', 4, 'Good Interview board', 'Good Interview board', '', '', 'published', '2026-03-21 11:44:37', '2026-03-21 11:44:37');

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

--
-- Dumping data for table `course_lessons`
--

INSERT INTO `course_lessons` (`id`, `module_id`, `lesson_number`, `title`, `content`, `resources`, `exercises`, `is_completed`) VALUES
(1, 1, 1, 'Getting Started with Linux Command Line', '## Overview\nIn the world of DevOps, familiarity with the Linux command line is essential. Many servers run on Linux, and understanding how to navigate and manage these environments via the command line will vastly improve your efficiency.\n\n## Step-by-Step Guide\n### Step 1: Accessing the Command Line\n- If you’re on Windows, you can use the Windows Subsystem for Linux (WSL) or install a terminal emulator like Git Bash.\n- On macOS, open the Terminal application.\n- For Linux, the terminal is typically available by default.\n\n### Step 2: Basic Commands\n- **Navigating Directories**: Use `cd` to change directories. For example, `cd /var/www/html` navigates to the web root directory.\n- **Listing Files**: Use `ls` to list files in a directory. Add the `-l` flag for more details: `ls -l`.\n- **Creating and Removing Directories**: Use `mkdir new_folder` to create a new directory and `rmdir old_folder` to remove it.\n\n### Step 3: File Operations\n- **Copying Files**: Use `cp source.txt destination.txt` to copy files.\n- **Moving Files**: Use `mv oldname.txt newname.txt` to rename or move files.\n- **Editing Files**: Use `nano filename.txt` or `vim filename.txt` to edit text files directly in the terminal.\n\n## Real-World Application\nIn a DevOps role, you\'ll frequently need to navigate, manage, and configure servers using the command line. For instance, deploying applications, checking logs, and managing user permissions all require command line proficiency.\n\n## Common Challenges\n- **Syntax Errors**: Beginners often struggle with command syntax. Ensure to double-check commands before execution.\n- **File Permissions**: Understanding permissions (read/write/execute) is crucial. Use `chmod` and `chown` to modify them.\n\n## Key Takeaways\nBecoming proficient in the Linux command line will enhance your ability to manage server environments efficiently, paving the way for a successful career in DevOps.', '[\"https:\\/\\/linuxcommand.org\\/\",\"https:\\/\\/www.codecademy.com\\/learn\\/learn-the-command-line\"]', '[\"Practice navigating the filesystem using `cd`, `ls`, and `mkdir`.\",\"Create and edit a text file using `nano`.\"]', 0),
(2, 1, 2, 'Advanced Linux Commands for DevOps', '## Overview\nBuilding on basic command line skills, this lesson introduces advanced commands and tools that are vital for DevOps engineers.\n\n## Step-by-Step Guide\n### Step 1: Searching and Finding Files\n- Use `find` to search for files: `find /path -name \'filename\'`. This command helps locate files across the filesystem.\n- Use `grep` to search within files: `grep \'search_term\' filename` searches for a term within a file.\n\n### Step 2: File Compression and Archiving\n- Compress files using `tar`: `tar -czvf archive.tar.gz /path/to/directory` to create a compressed archive.\n- Uncompress with `tar -xzvf archive.tar.gz`.\n\n### Step 3: Monitoring System Performance\n- Use `top` to view active processes and their resource usage.\n- Use `df -h` to check disk space usage and `free -m` to check memory usage.\n\n## Real-World Application\nAdvanced command line skills are often required for troubleshooting and system maintenance in a DevOps context. For instance, if a server is slow, you may need to investigate resource usage using `top` and `grep` through logs with `tail`.\n\n## Common Challenges\n- **Understanding Output**: The output of commands like `top` can be overwhelming. Familiarize yourself with the columns and what they represent.\n- **File Paths**: Knowing absolute vs relative paths is crucial. Use absolute paths to avoid confusion in scripts.\n\n## Key Takeaways\nAdvanced Linux commands are essential for monitoring and managing systems efficiently, leading to quicker troubleshooting and better system performance.', '[\"https:\\/\\/www.linux.com\\/learn\\/advanced-linux-commands\\/\",\"https:\\/\\/www.tldp.org\\/LDP\\/abs\\/html\\/index.html\"]', '[\"Use `find` and `grep` to locate specific log entries on your system.\",\"Create and extract a tar.gz archive.\"]', 0),
(3, 2, 1, 'Introduction to Docker', '## Overview\nDocker is a platform that enables developers to automate the deployment of applications inside lightweight, portable containers. This lesson will introduce you to Docker basics.\n\n## Step-by-Step Guide\n### Step 1: Installing Docker\n- Install Docker Desktop for Windows or macOS from the Docker website.\n- For Linux, use the package manager: `sudo apt-get install docker.io`.\n\n### Step 2: Understanding Docker Concepts\n- **Images**: Docker images are the blueprints of your application. You can pull images from Docker Hub: `docker pull ubuntu`.\n- **Containers**: Run containers from images using `docker run -it ubuntu`. This command launches an interactive terminal within the Ubuntu container.\n\n### Step 3: Managing Docker Containers\n- List running containers with `docker ps`, and all containers with `docker ps -a`.\n- Stop a running container with `docker stop <container_id>`.\n- Remove a container with `docker rm <container_id>`.\n\n## Real-World Application\nIn DevOps, Docker is used to create consistent development environments and streamline application deployment processes. For example, you can package your PHP application in a container with all its dependencies.\n\n## Common Challenges\n- **Understanding Networking**: Networking in Docker can be confusing. Familiarize yourself with bridge and host networks.\n- **Storage Management**: Managing volumes can be tricky; understand how to persist data using Docker volumes.\n\n## Key Takeaways\nDocker simplifies the deployment and management of applications, making it a vital tool for any DevOps engineer.', '[\"https:\\/\\/docs.docker.com\\/get-started\\/\",\"https:\\/\\/www.docker.com\\/101-tutorial\"]', '[\"Create a simple Dockerfile for a PHP application.\",\"Run a container from your Docker image and access it via a browser.\"]', 0),
(4, 2, 2, 'Kubernetes Basics', '## Overview\nKubernetes is an open-source container orchestration platform that automates deploying, scaling, and managing containerized applications. This lesson covers Kubernetes fundamentals.\n\n## Step-by-Step Guide\n### Step 1: Setting Up Kubernetes\n- Install Minikube for local Kubernetes development: `minikube start`.\n- Ensure kubectl is installed: `kubectl version` checks the version of your Kubernetes client.\n\n### Step 2: Creating Your First Deployment\n- Use the following command to create a deployment: `kubectl create deployment php-app --image=php:7.4-apache`.\n- Expose it via a service: `kubectl expose deployment php-app --type=NodePort --port=80`.\n\n### Step 3: Managing Deployments\n- Check your deployments with `kubectl get deployments`.\n- Scale your deployment: `kubectl scale deployment php-app --replicas=3` to increase instances.\n\n## Real-World Application\nKubernetes allows you to manage multi-container applications at scale, handling service discovery, load balancing, and scaling on demand. It is widely used in production environments.\n\n## Common Challenges\n- **Complexity**: Kubernetes has a steep learning curve. Start with simple deployments before diving deeper.\n- **Configuration Management**: Managing YAML configuration files can be daunting. Get comfortable with writing and modifying these.\n\n## Key Takeaways\nUnderstanding Kubernetes is critical for deploying and managing applications in a microservices architecture, a common pattern in DevOps.', '[\"https:\\/\\/kubernetes.io\\/docs\\/tutorials\\/kubernetes-basics\\/\",\"https:\\/\\/kubernetes.io\\/docs\\/setup\\/learning-environment\\/minikube\\/\"]', '[\"Deploy a simple PHP application using Kubernetes.\",\"Scale your application and expose it to the internet via a NodePort service.\"]', 0),
(5, 3, 1, 'Introduction to CI/CD', '## Overview\nCI/CD is an essential DevOps practice that enables development teams to deliver code changes more frequently and reliably. This lesson introduces the core concepts and benefits of CI/CD.\n\n## Step-by-Step Guide\n### Step 1: Understanding CI/CD Concepts\n- **Continuous Integration (CI)**: Automating the integration of code changes from multiple contributors into a shared repository.\n- **Continuous Deployment (CD)**: Automated delivery of applications to production after passing tests.\n\n### Step 2: Tools for CI/CD\n- Familiarize yourself with tools like Jenkins, GitLab CI, and GitHub Actions. Each provides capabilities for setting up CI/CD pipelines.\n- For example, Jenkins allows you to create a pipeline through a web interface or Jenkinsfile.\n\n### Step 3: Setting Up a Simple CI/CD Pipeline\n- Create a basic CI/CD pipeline using GitHub Actions. Use the following `main.yml` file:\nyaml\nname: CI/CD Pipeline\non: [push]\njobs:\n  build:\n    runs-on: ubuntu-latest\n    steps:\n      - name: Checkout code\n        uses: actions/checkout@v2\n      - name: Set up PHP\n        uses: shivammathur/setup-php@v2\n        with:\n          php-version: \'7.4\'\n      - name: Run tests\n        run: | \n          composer install\n          ./vendor/bin/phpunit\n\n\n## Real-World Application\nCI/CD pipelines automate the testing and deployment of applications, which increases development speed and reduces errors. For example, when you push code to GitHub, it can automatically run tests and deploy if they pass.\n\n## Common Challenges\n- **Pipeline Configuration**: Setting up a CI/CD pipeline can be complex. Start with a simple pipeline and gradually add complexity.\n- **Debugging Failures**: If your pipeline fails, understanding logs and errors is crucial. Pay attention to output logs for clues.\n\n## Key Takeaways\nImplementing CI/CD practices will help streamline your development process, allowing for faster and more reliable releases.', '[\"https:\\/\\/www.atlassian.com\\/continuous-delivery\\/ci-vs-ci-vs-cd\",\"https:\\/\\/www.jenkins.io\\/doc\\/book\\/pipeline\\/\"]', '[\"Set up a simple CI\\/CD workflow using GitHub Actions for your PHP application.\",\"Integrate automated testing into your CI\\/CD pipeline.\"]', 0),
(6, 3, 2, 'Advanced CI/CD Practices', '## Overview\nThis lesson explores advanced CI/CD practices, including testing strategies, versioning, and monitoring deployments.\n\n## Step-by-Step Guide\n### Step 1: Testing Strategies\n- Implement unit tests, integration tests, and end-to-end tests. Use PHPUnit for unit tests in PHP applications.\n- Add tests to your CI/CD pipeline to ensure code quality.\n\n### Step 2: Versioning\n- Use semantic versioning for your deployments. Tag your releases in Git with versions: `git tag -a v1.0 -m \'Release version 1.0\'`.\n- Automate versioning in your CI/CD pipeline.\n\n### Step 3: Monitoring and Rollbacks\n- Implement monitoring for your applications using tools like Prometheus and Grafana.\n- Use Helm for managing Kubernetes applications, enabling easy rollbacks: `helm rollback <release_name> <revision>`.\n\n## Real-World Application\nAdvanced CI/CD practices ensure that your applications are resilient and maintainable. For example, automated testing prevents faulty code from reaching production, while monitoring ensures you stay informed about application performance.\n\n## Common Challenges\n- **Test Coverage**: Achieving high test coverage can be challenging. Focus on critical paths initially.\n- **Deployment Failures**: Be prepared for failures. Implement a rollback strategy to return to the last stable version.\n\n## Key Takeaways\nAdvanced CI/CD practices enhance the reliability and quality of your deployments, which is essential for successful DevOps implementations.', '[\"https:\\/\\/martinfowler.com\\/articles\\/continuousDelivery.html\",\"https:\\/\\/helm.sh\\/docs\\/\"]', '[\"Implement a comprehensive testing strategy in your CI\\/CD pipeline.\",\"Set up monitoring for your application and create alerts for failures.\"]', 0),
(7, 4, 1, 'Understanding Descriptive Statistics', '## Overview\nStatistical analysis is crucial for data analysts to derive insights from data. Descriptive statistics helps summarize and describe the features of a dataset, providing a quick overview of its key characteristics.\n\n## Step-by-Step Guide\n### Step 1: Collect and Prepare Your Data\nStart by gathering a sample dataset. You can use publicly available datasets from sources like Kaggle or UCI Machine Learning Repository. Use tools like Python with Pandas or Excel to organize your data.\n\n**Example:**\nIf you\'re analyzing a dataset of customer purchases, ensure your data includes variables like purchase amount, date, and customer demographics.\n\n### Step 2: Calculate Basic Statistics\nUsing your chosen tool, calculate the mean, median, mode, range, and standard deviation for your dataset. In Python, this can be done using Pandas:\npython\nimport pandas as pd\n\ndata = pd.read_csv(\'customer_purchases.csv\')\nprint(data[\'purchase_amount\'].describe())\n\nThis will give you the count, mean, std, min, 25%, 50%, 75%, and max values.\n\n## Real-World Application\nDescriptive statistics provide a snapshot of data, helping data analysts make informed decisions. For instance, knowing the average spend of customers can guide marketing strategies.\n\n## Common Challenges\n- **Challenge**: Misinterpreting statistics can lead to incorrect conclusions.\n- **Solution**: Always cross-reference your findings with visualizations to ensure consistency.\n\n## Key Takeaways\nDescriptive statistics are the first step in data analysis, helping you summarize and understand your data better.', '[\"https:\\/\\/www.kaggle.com\\/datasets\",\"https:\\/\\/towardsdatascience.com\\/pandas-descriptive-statistics-3e9d5ffb8c3e\"]', '[\"Gather a dataset and calculate the descriptive statistics.\",\"Create visual representations of your statistics using histograms.\"]', 0),
(8, 4, 2, 'Inferential Statistics and Hypothesis Testing', '## Overview\nInferential statistics allows analysts to make predictions or inferences about a population based on a sample. Hypothesis testing is a key component that validates assumptions using statistical evidence.\n\n## Step-by-Step Guide\n### Step 1: Formulate Hypotheses\nStart by formulating a null hypothesis (H0) and an alternative hypothesis (H1). For example, if you want to test if a new marketing strategy increases sales, your hypotheses might be:\n- H0: There is no difference in sales.\n- H1: The new strategy increases sales.\n\n### Step 2: Choose a Significance Level\nTypically, a significance level of 0.05 is used. This means you accept a 5% chance of incorrectly rejecting the null hypothesis.\n\n### Step 3: Conduct the Test\nUsing Python’s Scipy library, you can perform a t-test:\npython\nfrom scipy import stats\n\n# Assuming you have sales data before and after the marketing strategy\nbefore = [10, 12, 13, 15, 12]\nafter = [14, 15, 16, 18, 17]\n\nt_stat, p_value = stats.ttest_ind(before, after)\nprint(\'p-value:\', p_value)\n\nIf the p-value is less than 0.05, you reject the null hypothesis.\n\n## Real-World Application\nInferential statistics are used to determine the effectiveness of marketing campaigns, product launches, and other business strategies.\n\n## Common Challenges\n- **Challenge**: Not understanding the assumptions behind statistical tests can lead to incorrect conclusions.\n- **Solution**: Familiarize yourself with the assumptions of each test you use.\n\n## Key Takeaways\nInferential statistics and hypothesis testing allow data analysts to make informed decisions and predictions based on data samples.', '[\"https:\\/\\/www.sciencedirect.com\\/topics\\/computer-science\\/hypothesis-testing\",\"https:\\/\\/towardsdatascience.com\\/a-b-testing-with-python-3e9e4684f1f6\"]', '[\"Develop your own hypothesis based on a dataset and conduct a t-test.\",\"Interpret the results of your test and draw conclusions.\"]', 0),
(9, 5, 1, 'Introduction to Data Visualization', '## Overview\nData visualization is crucial for data analysts to communicate findings effectively. By translating data into visual formats, complex information becomes accessible and understandable.\n\n## Step-by-Step Guide\n### Step 1: Choose Your Visualization Tool\nFamiliarize yourself with tools like Tableau, Power BI, or Python’s Matplotlib and Seaborn libraries. For web developers, using D3.js can also be an option.\n\n### Step 2: Understand Your Data\nDetermine what you want to convey with your data visualization. For example, if you want to show sales trends over time, a line chart might be appropriate.\n\n### Step 3: Create Your Visualization\nUsing Python with Matplotlib, you can create a simple line chart:\npython\nimport matplotlib.pyplot as plt\n\nmonths = [\'January\', \'February\', \'March\', \'April\']\nsales = [200, 300, 400, 500]\nplt.plot(months, sales)\nplt.title(\'Sales Trend\')\nplt.xlabel(\'Month\')\nplt.ylabel(\'Sales\')\nplt.show()\n\n\n## Real-World Application\nData visualization is used in reports and presentations to convey insights to stakeholders, making it easier for them to understand data trends and patterns.\n\n## Common Challenges\n- **Challenge**: Overcomplicating visualizations can confuse the audience.\n- **Solution**: Stick to simplicity and ensure that your visuals are clear and easy to interpret.\n\n## Key Takeaways\nEffective data visualization is critical for communicating data insights clearly and succinctly.', '[\"https:\\/\\/www.tableau.com\\/learn\\/articles\\/data-visualization\",\"https:\\/\\/realpython.com\\/python-matplotlib-guide\\/\"]', '[\"Create a visualization for a dataset of your choice using Matplotlib.\",\"Experiment with different types of charts to see which one best represents your data.\"]', 0),
(10, 5, 2, 'Advanced Data Visualization: Dashboards and Interactive Visuals', '## Overview\nAdvanced data visualization techniques involve creating dashboards and interactive graphics, which are powerful tools for data analysis and presentation.\n\n## Step-by-Step Guide\n### Step 1: Define Your Dashboard Goals\nDetermine what insights you want your dashboard to convey. For instance, it might be the performance of different products over time.\n\n### Step 2: Choose a Tool\nUse Tableau, Power BI, or Python’s Dash framework to create interactive dashboards. These tools allow users to explore data dynamically.\n\n### Step 3: Build the Dashboard\nFor instance, using Dash, you can create a simple dashboard:\npython\nimport dash\nfrom dash import dcc, html\n\napp = dash.Dash(__name__)\n\napp.layout = html.Div([\n    dcc.Graph(\n        id=\'example-graph\',\n        figure={\n            \'data\': [{\'x\': [\'January\', \'February\'], \'y\': [10, 15], \'type\': \'bar\'}],\n            \'layout\': {\'title\': \'Sales Data\'}\n        }\n    )\n])\n\nif __name__ == \'__main__\':\n    app.run_server(debug=True)\n\n\n## Real-World Application\nDashboards allow stakeholders to monitor key metrics at a glance, leading to quicker decision-making.\n\n## Common Challenges\n- **Challenge**: Overloading dashboards with too much information.\n- **Solution**: Focus on key metrics and ensure that the dashboard is user-friendly.\n\n## Key Takeaways\nCreating interactive dashboards enhances user engagement and allows for deeper data exploration.', '[\"https:\\/\\/dash.plotly.com\\/\",\"https:\\/\\/www.tableau.com\\/learn\\/articles\\/dashboard-design\"]', '[\"Design a dashboard for a dataset of your choice using Tableau or Dash.\",\"Include key performance indicators that are relevant to your analysis.\"]', 0),
(11, 6, 1, 'Getting Started with SQL Queries', '## Overview\nSQL (Structured Query Language) is essential for data analysts to interact with databases. It allows you to retrieve and manipulate data efficiently.\n\n## Step-by-Step Guide\n### Step 1: Setting Up Your Environment\nInstall a database management system like MySQL or PostgreSQL. You can also use online platforms like SQL Fiddle to practice.\n\n### Step 2: Writing Basic Queries\nStart with simple SELECT statements to retrieve data from tables. For example:\nsql\nSELECT * FROM customers;\n\nThis retrieves all columns from the \'customers\' table.\n\n### Step 3: Filtering Data\nUse the WHERE clause to filter results. For instance, to find customers from a specific city:\nsql\nSELECT * FROM customers WHERE city = \'New York\';\n\n\n## Real-World Application\nSQL is used extensively in data analysis for querying databases, allowing analysts to quickly access the information they need for reports.\n\n## Common Challenges\n- **Challenge**: Writing complex queries can be daunting.\n- **Solution**: Break down your queries into smaller parts and test each part.\n\n## Key Takeaways\nMastering basic SQL queries is foundational for data analysis, enabling quick data retrieval and manipulation.', '[\"https:\\/\\/www.w3schools.com\\/sql\\/\",\"https:\\/\\/www.sqltutorial.org\\/\"]', '[\"Write SQL queries to extract specific data from a sample database.\",\"Practice filtering and sorting data using various SQL commands.\"]', 0),
(12, 6, 2, 'Advanced SQL for Data Analysis', '## Overview\nBuilding on basic SQL knowledge, advanced SQL techniques help data analysts perform complex data manipulations and aggregations.\n\n## Step-by-Step Guide\n### Step 1: Understanding Joins\nLearn how to combine data from multiple tables using JOINs. For example:\nsql\nSELECT customers.name, orders.amount\nFROM customers\nJOIN orders ON customers.id = orders.customer_id;\n\nThis retrieves customer names along with their order amounts.\n\n### Step 2: Using Aggregate Functions\nUtilize aggregate functions like COUNT, SUM, AVG, and GROUP BY for data summarization:\nsql\nSELECT city, COUNT(*) as customer_count\nFROM customers\nGROUP BY city;\n\nThis query counts the number of customers in each city.\n\n## Real-World Application\nAdvanced SQL is used for creating comprehensive reports, enabling analysts to summarize large datasets quickly.\n\n## Common Challenges\n- **Challenge**: Understanding the logic of JOINs and aggregations.\n- **Solution**: Practice with different datasets and visualize the relationships between tables.\n\n## Key Takeaways\nAdvanced SQL skills are essential for data analysts to perform complex analyses and gain deeper insights from their data.', '[\"https:\\/\\/mode.com\\/sql-tutorial\\/sql-joins\\/\",\"https:\\/\\/www.sqlshack.com\\/sql-aggregate-functions\\/\"]', '[\"Create complex queries using JOINs and aggregate functions on a sample database.\",\"Build a report that summarizes data for analysis using SQL.\"]', 0);

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

--
-- Dumping data for table `course_modules`
--

INSERT INTO `course_modules` (`id`, `transition_id`, `module_number`, `title`, `description`, `duration_weeks`, `content`) VALUES
(1, 1, 1, 'Linux Command Line Proficiency', 'Learn the essentials of using the Linux command line, which is crucial for every DevOps engineer.', 2, NULL),
(2, 1, 2, 'Containerization with Docker and Kubernetes', 'Understand the fundamentals of Docker and Kubernetes for deploying and managing applications in containers.', 2, NULL),
(3, 1, 3, 'CI/CD Pipelines Understanding', 'Gain insights into Continuous Integration and Continuous Deployment (CI/CD) practices and tools used in DevOps.', 2, NULL),
(4, 2, 1, 'Statistical Analysis Fundamentals', 'Learn the foundational concepts of statistical analysis that are essential for interpreting data.', 2, NULL),
(5, 2, 2, 'Data Visualization Techniques', 'Learn how to effectively visualize data to communicate insights clearly and effectively.', 2, NULL),
(6, 2, 3, 'SQL for Data Analysis', 'Master SQL to manipulate and query data effectively as a data analyst.', 2, NULL);

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

--
-- Dumping data for table `daily_tasks`
--

INSERT INTO `daily_tasks` (`id`, `transition_id`, `task_title`, `task_description`, `duration_minutes`, `day_number`, `module_number`, `lesson_number`, `is_completed`, `completed_at`) VALUES
(1, 1, 'Module 1 - Lesson 1', 'Complete lesson', 10, 1, 1, 1, 1, '2026-03-21 10:25:35'),
(2, 1, 'Module 1 - Lesson 2', 'Complete lesson', 10, 2, 1, 2, 0, NULL),
(3, 1, 'Module 2 - Lesson 1', 'Complete lesson', 10, 3, 2, 1, 0, NULL),
(4, 1, 'Module 2 - Lesson 2', 'Complete lesson', 10, 4, 2, 2, 0, NULL),
(5, 1, 'Module 3 - Lesson 1', 'Complete lesson', 10, 5, 3, 1, 0, NULL),
(6, 1, 'Module 3 - Lesson 2', 'Complete lesson', 10, 6, 3, 2, 0, NULL),
(7, 2, 'Module 1 - Lesson 1', 'Complete lesson', 10, 1, 1, 1, 0, NULL),
(8, 2, 'Module 1 - Lesson 2', 'Complete lesson', 10, 2, 1, 2, 0, NULL),
(9, 2, 'Module 2 - Lesson 1', 'Complete lesson', 10, 3, 2, 1, 0, NULL),
(10, 2, 'Module 2 - Lesson 2', 'Complete lesson', 10, 4, 2, 2, 0, NULL),
(11, 2, 'Module 3 - Lesson 1', 'Complete lesson', 10, 5, 3, 1, 0, NULL),
(12, 2, 'Module 3 - Lesson 2', 'Complete lesson', 10, 6, 3, 2, 0, NULL);

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
(1, 2, 'B.Sc.', 'Computer Science', 'Prajyoti Niketan College, Pudukad, Thrissur', '2012', '2015', '', '2026-03-06 06:34:14', '2026-03-13 00:51:34'),
(3, 5, 'MBA', 'Marketing', 'Adi Shankara Institute of Engineering and Technology', '2015', '2017', '', '2026-03-13 01:13:10', '2026-03-13 01:13:10'),
(4, 7, 'MBA', 'Marketing', 'Adi Shankara Institute of Engineering and Technology', '2015', '2017', '', '2026-03-13 23:47:56', '2026-03-13 23:47:56'),
(5, 6, 'MBA', 'Marketing', 'Adi Shankara Institute of Engineering and Technology', '2015', '2017', '', '2026-03-20 03:37:55', '2026-03-20 03:37:55'),
(6, 8, 'MBA', 'Marketing', 'Adi Shankara Institute of Engineering and Technology', '2019', '2021', '', '2026-03-21 03:21:50', '2026-03-21 03:21:50');

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
  `booking_status` enum('booked','confirmed','rescheduled','completed','no_show','cancelled') DEFAULT 'booked',
  `reschedule_count` int(11) DEFAULT 0,
  `max_reschedules` int(11) DEFAULT 2,
  `can_reschedule` tinyint(1) DEFAULT 1,
  `booked_at` datetime NOT NULL,
  `last_rescheduled_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `interview_bookings`
--

INSERT INTO `interview_bookings` (`id`, `application_id`, `user_id`, `job_id`, `slot_id`, `slot_datetime`, `booking_status`, `reschedule_count`, `max_reschedules`, `can_reschedule`, `booked_at`, `last_rescheduled_at`) VALUES
(1, 5, 8, 1, 2, '2026-03-23 14:00:00', 'completed', 2, 2, 1, '2026-03-21 11:00:25', '2026-03-23 08:36:03');

-- --------------------------------------------------------

--
-- Table structure for table `interview_booking_reviews`
--

CREATE TABLE `interview_booking_reviews` (
  `id` int(11) UNSIGNED NOT NULL,
  `booking_id` int(11) NOT NULL,
  `application_id` int(11) NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `job_id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `attendance_status` enum('attended','late','no_show') NOT NULL DEFAULT 'attended',
  `decision` enum('shortlisted','selected','rejected') DEFAULT NULL,
  `strengths` text DEFAULT NULL,
  `concerns` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `reviewed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `interview_booking_reviews`
--

INSERT INTO `interview_booking_reviews` (`id`, `booking_id`, `application_id`, `candidate_id`, `job_id`, `recruiter_id`, `attendance_status`, `decision`, `strengths`, `concerns`, `notes`, `reviewed_at`, `created_at`, `updated_at`) VALUES
(1, 1, 5, 8, 1, 1, 'attended', 'shortlisted', 'Strong communication', 'lacks technical skills', 'can be considered', '2026-03-23 10:17:52', '2026-03-23 09:54:31', '2026-03-23 10:17:52');

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

--
-- Dumping data for table `interview_slots`
--

INSERT INTO `interview_slots` (`id`, `job_id`, `slot_date`, `slot_time`, `slot_datetime`, `capacity`, `booked_count`, `is_available`, `created_by`, `created_at`) VALUES
(1, 1, '2026-03-25', '16:00:00', '2026-03-25 16:00:00', 1, 0, 1, 1, '2026-03-21 09:25:56'),
(2, 1, '2026-03-23', '14:00:00', '2026-03-23 14:00:00', 1, 1, 0, 1, '2026-03-21 09:25:56'),
(3, 1, '2026-03-27', '14:00:00', '2026-03-27 14:00:00', 1, 0, 1, 1, '2026-03-21 09:25:56'),
(4, 3, '2026-03-28', '12:00:00', '2026-03-28 12:00:00', 2, 0, 1, 1, '2026-03-21 09:50:20');

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
(1, 1, 1, 'Quality Inspector', 'Manufacturing', 'TechNova Solutions', 'Pune, India', 'Inspect manufactured products and ensure they meet quality standards. Identify defects and improve production quality.', 'Quality Control, Inspection Tools, Documentation, ISO Standards', '1-2 Years', 4, 75, 'OPTIONAL', 'open', '2026-03-06 17:14:15', 'Full-time', '4 LPA', NULL),
(2, 1, 1, 'Frontend Developer', 'IT / Software', 'TechNova Solutions', 'Hyderabad, India', 'Develop responsive web interfaces and collaborate with backend developers to build modern web applications.', 'HTML, CSS, JavaScript, Bootstrap, React', '2-4 Years', 2, 82, 'OPTIONAL', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(3, 1, 1, 'Data Analyst', 'Analytics', 'TechNova Solutions', 'Chennai, India', 'Analyze business data, identify trends, and create reports for decision making.', 'Python, SQL, Excel, Power BI', '1-3 Years', 3, 80, 'REQUIRED_SOFT', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(4, 1, 1, 'HR Executive', 'Human Resources', 'TechNova Solutions', 'Kochi, India', 'Handle recruitment processes, employee engagement, and HR documentation.', 'Recruitment, Communication, HRMS, Interview Coordination', '1-2 Years', 1, 70, 'OPTIONAL', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(5, 1, 1, 'DevOps Engineer', 'IT / Cloud', 'TechNova Solutions', 'Bangalore, India', 'Manage CI/CD pipelines, cloud infrastructure, and deployment automation.', 'Docker, Kubernetes, AWS, CI/CD, Linux', '3-5 Years', 2, 85, 'REQUIRED_HARD', 'open', '2026-03-06 17:14:15', 'Full-time', NULL, NULL),
(6, 3, 2, 'Backend Developer', 'IT / Software', 'PrecisionTech Industries', 'Bangalore, India', 'Develop and maintain server-side logic and APIs for scalable web applications.', 'PHP, Laravel, MySQL, JavaScript', '2-3 years', 1, 80, 'OPTIONAL', 'open', '2026-03-06 17:25:19', 'Full-time', '3 LPA', '2026-03-31'),
(7, 3, 2, 'Electrical Engineer', 'Engineering', 'PrecisionTech Industries', 'Chennai, India', 'Design, develop, and maintain electrical systems for industrial applications.', 'Circuit Design, PLC, Electrical Testing, AutoCAD', '1-3 Years', 2, 78, 'REQUIRED_HARD', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(8, 3, 2, 'Digital Marketing Executive', 'Marketing', 'PrecisionTech Industries', 'Chennai, India', 'Plan and execute digital marketing campaigns including SEO, SEM, and social media.', 'SEO, Google Ads, Social Media Marketing, Content Marketing', '1-2 Years', 2, 70, 'OPTIONAL', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(9, 3, 2, 'UI/UX Designer', 'Design', 'PrecisionTech Industries', 'Chennai, India', 'Design intuitive user interfaces and improve user experience for web and mobile apps.', 'Figma, Adobe XD, Wireframing, Prototyping', '2-3 Years', 1, 75, 'OPTIONAL', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(10, 3, 2, 'Mechanical Design Engineer', 'Manufacturing', 'PrecisionTech Industries', 'Chennai, India', 'Design mechanical components and develop product prototypes.', 'SolidWorks, AutoCAD, Product Design, Mechanical Analysis', '2-5 Years', 2, 82, 'REQUIRED_HARD', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(11, 3, 2, 'Python Developer', 'IT / Software', 'PrecisionTech Industries', 'Chennai, India', 'Build backend services and data processing pipelines using Python.', 'Python, Django, Flask, PostgreSQL, REST API', '2-4 Years', 3, 85, 'OPTIONAL', 'open', '2026-03-06 17:28:44', 'Full-time', NULL, NULL),
(12, 9, 3, 'Java Developer', 'IT / Software', 'InsightData Labs', 'Bangalore, India', 'Develop enterprise-level Java applications and maintain backend services.', 'Java, Spring Boot, MySQL, REST API, Git', '2-5 Years', 1, 78, 'OPTIONAL', 'open', '2026-03-23 11:28:10', 'Full-time', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `job_alerts`
--

CREATE TABLE `job_alerts` (
  `id` int(11) UNSIGNED NOT NULL,
  `candidate_id` int(11) NOT NULL,
  `role_keywords` varchar(255) DEFAULT NULL,
  `location_keywords` varchar(255) DEFAULT NULL,
  `employment_type` varchar(50) DEFAULT NULL,
  `skills_keywords` varchar(255) DEFAULT NULL,
  `salary_min` int(11) DEFAULT NULL,
  `salary_max` int(11) DEFAULT NULL,
  `notify_email` tinyint(1) NOT NULL DEFAULT 1,
  `notify_in_app` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `job_alerts`
--

INSERT INTO `job_alerts` (`id`, `candidate_id`, `role_keywords`, `location_keywords`, `employment_type`, `skills_keywords`, `salary_min`, `salary_max`, `notify_email`, `notify_in_app`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 7, 'web developer, software developer', 'Bangalore', 'Full-time', 'PHP, MySQL, JavaScript', NULL, NULL, 1, 1, 1, '2026-03-14 05:18:55', '2026-03-14 07:22:42'),
(2, 6, 'web developer, software developer', 'Bangalore', 'Part-time', 'Core PHP, MySQL', 500000, NULL, 1, 1, 1, '2026-03-20 09:08:32', '2026-03-20 09:08:32'),
(3, 8, 'web developer, software developer', 'Hyderabad', 'Contract', 'Core PHP, MySQL, WordPress', 300000, NULL, 1, 1, 1, '2026-03-21 08:52:31', '2026-03-21 08:52:31');

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
(31, '2026-03-07-170000', 'App\\Database\\Migrations\\CleanupLegacyPhpInterviewArtifacts', 'default', 'App', 1772867685, 28),
(32, '2026-03-12-120000', 'App\\Database\\Migrations\\AddCandidatePrivacyAndAlertSettings', 'default', 'App', 1773317901, 29),
(33, '2026-03-13-110000', 'App\\Database\\Migrations\\AddCandidateNotificationChannelSettings', 'default', 'App', 1773380765, 30),
(34, '2026-03-13-160000', 'App\\Database\\Migrations\\AddCandidateOnboardingState', 'default', 'App', 1773382243, 31),
(35, '2026-03-13-173000', 'App\\Database\\Migrations\\AddCandidatePersonalDetailsFields', 'default', 'App', 1773384125, 32),
(36, '2026-03-13-190000', 'App\\Database\\Migrations\\AddCandidatePreferenceFields', 'default', 'App', 1773389870, 33),
(37, '2026-03-13-200000', 'App\\Database\\Migrations\\AddMissingCandidatePreferenceColumns', 'default', 'App', 1773390690, 34),
(38, '2026-03-14-120000', 'App\\Database\\Migrations\\AddOfficeTourFieldsToCompanies', 'default', 'App', 1773469023, 35),
(39, '2026-03-23-120000', 'App\\Database\\Migrations\\CreateInterviewBookingReviewsTable', 'default', 'App', 1774258446, 36),
(40, '2026-03-23-130000', 'App\\Database\\Migrations\\UpdateApplicationStatusEnumForHold', 'default', 'App', 1774260543, 37);

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
(2, 2, NULL, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-03-07 04:29:17', NULL),
(3, 5, NULL, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-03-13 08:51:48', NULL),
(4, 8, NULL, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-21 09:00:54', '2026-03-23 10:26:46'),
(5, 8, NULL, '', 'Contact Viewed', 'Rohith Kumar viewed your contact details.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-21 09:18:51', '2026-03-23 10:26:46'),
(6, 8, NULL, '', 'Resume Downloaded', 'Rohith Kumar downloaded your resume.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-21 09:19:09', '2026-03-23 10:26:46'),
(7, 8, 5, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-23 06:24:42', '2026-03-23 10:26:46'),
(8, 8, 5, '', 'Contact Viewed', 'Rohith Kumar viewed your contact details.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-23 06:26:18', '2026-03-23 10:26:46'),
(9, 8, 5, '', 'Interview Reviewed', 'Your interview review is complete. Final status: Shortlisted.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-23 10:17:52', '2026-03-23 10:26:46'),
(10, 8, 5, '', 'Application Status Updated', 'Your application status was updated to Shortlisted.', 'http://localhost/ai-job-portal/public/candidate/applications', 1, '2026-03-23 10:17:58', '2026-03-23 10:26:46');

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
(2, 2, 1, NULL, NULL, 'profile_viewed', '2026-03-07 04:29:17'),
(3, 5, 1, NULL, NULL, 'profile_viewed', '2026-03-13 08:51:48'),
(4, 8, 1, NULL, NULL, 'profile_viewed', '2026-03-21 09:00:54'),
(5, 8, 1, NULL, NULL, 'contact_viewed', '2026-03-21 09:18:51'),
(6, 8, 1, NULL, NULL, 'resume_downloaded', '2026-03-21 09:19:09'),
(7, 8, 1, 5, 1, 'profile_viewed', '2026-03-23 06:24:42'),
(8, 8, 1, 5, 1, 'contact_viewed', '2026-03-23 06:26:18');

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

--
-- Dumping data for table `recruiter_candidate_notes`
--

INSERT INTO `recruiter_candidate_notes` (`id`, `candidate_id`, `recruiter_id`, `tags`, `notes`, `created_at`, `updated_at`) VALUES
(1, 8, 1, 'Strong communication', 'Very Talented', '2026-03-23 06:26:45', '2026-03-23 06:26:45');

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
(3, 4, 1, 1, '2026-03-07 08:57:52', '2026-03-07 08:57:52'),
(4, 9, 3, 1, '2026-03-23 05:51:54', '2026-03-23 05:51:54');

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
(1, 'Rohith Kumar', '+919544104305', 'Talent Aquisition Specialist', 'TechNova Solutions', '2026-03-06 11:41:11', '2026-03-14 06:25:58'),
(3, 'Arun Mohan', '+919544104305', 'HR Executive', 'PrecisionTech Industries', '2026-03-06 11:52:06', '2026-03-06 11:52:06'),
(4, 'Manu', '+919544104305', 'HR Manager', 'TechNova Solutions', '2026-03-07 08:57:52', '2026-03-07 08:57:52'),
(9, 'Vishnu', '+919544104305', 'HR Recruiter', 'InsightData Labs', '2026-03-23 05:51:54', '2026-03-23 05:51:54');

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

--
-- Dumping data for table `reschedule_history`
--

INSERT INTO `reschedule_history` (`id`, `booking_id`, `old_slot_id`, `new_slot_id`, `old_slot_datetime`, `new_slot_datetime`, `reason`, `rescheduled_by`, `rescheduled_at`) VALUES
(1, 1, 2, 1, '2026-03-26 14:00:00', '2026-03-25 16:00:00', '', 'candidate', '2026-03-21 11:05:23'),
(2, 1, 1, 2, '2026-03-25 16:00:00', '2026-03-23 17:00:00', '', 'candidate', '2026-03-23 08:36:03');

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
(1, 2, 11, '2026-03-20 05:03:22', '2026-03-20 05:03:22'),
(2, 2, 2, '2026-03-20 05:03:29', '2026-03-20 05:03:29');

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
(1, 1, 'Applied', '2026-03-07 06:29:46', NULL),
(2, 2, 'Applied', '2026-03-14 05:44:29', NULL),
(3, 3, 'Applied', '2026-03-14 08:19:26', NULL),
(4, 4, 'Applied', '2026-03-21 10:45:10', NULL),
(5, 5, 'Applied', '2026-03-21 10:59:33', '2026-03-21 11:00:25'),
(6, 5, 'Interview Slot Booked', '2026-03-21 11:00:25', '2026-03-23 09:54:31'),
(7, 5, 'HR Interview Reviewed - Hold', '2026-03-23 09:54:32', '2026-03-23 09:57:44'),
(8, 5, 'HR Interview Reviewed - Shortlisted', '2026-03-23 09:57:44', '2026-03-23 09:58:27'),
(9, 5, 'HR Interview Reviewed - Hold', '2026-03-23 09:58:27', '2026-03-23 10:01:15'),
(10, 5, 'HR Interview Reviewed - Hold', '2026-03-23 10:01:15', '2026-03-23 10:02:19'),
(11, 5, 'HR Interview Reviewed - Hold', '2026-03-23 10:02:19', '2026-03-23 10:04:44'),
(12, 5, 'HR Interview Reviewed - Hold', '2026-03-23 10:04:44', '2026-03-23 10:07:44'),
(13, 5, 'HR Interview Reviewed - Hold', '2026-03-23 10:07:44', '2026-03-23 10:09:17'),
(14, 5, 'HR Interview Reviewed - Hold', '2026-03-23 10:09:17', '2026-03-23 10:17:52'),
(15, 5, 'HR Interview Reviewed - Shortlisted', '2026-03-23 10:17:52', NULL);

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
  `onboarding_completed` tinyint(1) NOT NULL DEFAULT 0,
  `onboarding_step` varchar(50) DEFAULT NULL,
  `onboarding_completed_at` datetime DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `google_id`, `phone`, `role`, `company_id`, `email_verification_token`, `email_verified_at`, `password_reset_token`, `password_reset_expires_at`, `phone_verified_at`, `onboarding_completed`, `onboarding_step`, `onboarding_completed_at`, `password`, `created_at`) VALUES
(1, 'Rohith Kumar', 'rohith@technova.com', NULL, '+919544104305', 'recruiter', 1, NULL, '2026-03-06 11:41:15', NULL, NULL, '2026-03-06 11:41:29', 0, NULL, NULL, '$2y$10$NdHPUx9Hxg4qfk3G2k2/oebDnZR0.SRgwBHLCwH4WF6HU4p6trfCO', '2026-03-06 17:11:11'),
(2, 'Manju Aravind', 'manju@gmail.com', NULL, '1234567890', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'review', '2026-03-13 06:22:41', '$2y$10$3ZtTFBBLs9HtT00GtL/GMeCbFrNFMTUEOKWyXnuWmxlvXhiOjrxVy', '2026-03-06 17:19:18'),
(3, 'Arun Mohan', 'arun@precisiontech.com', NULL, '+919544104305', 'recruiter', 2, NULL, '2026-03-06 11:52:11', NULL, NULL, '2026-03-06 11:52:21', 0, NULL, NULL, '$2y$10$7VF7V5LOaW7K/js4HLtFVO2e8PtFrCKYX009kaWQUDGpfC.3DMkA6', '2026-03-06 17:22:06'),
(4, 'Manu', 'manu@technova.com', NULL, '+919544104305', 'recruiter', 1, NULL, '2026-03-07 08:57:54', NULL, NULL, '2026-03-07 08:58:07', 0, NULL, NULL, '$2y$10$8dvzoelZOC5mfsC7J9LX3OleovjG6VzbIAS0k6vrUXSH.TkgUd.Ce', '2026-03-07 14:27:51'),
(5, 'Arun George', 'arun@gmail.com', NULL, '1472583666', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'review', '2026-03-13 06:50:00', '$2y$10$IXDyXlPlFfvRDMq2no0rjeRiWEEDuRuH3TyraT1aHChZnrbbkMVaW', '2026-03-13 11:59:20'),
(6, 'John', 'john@gmail.com', NULL, '1472583690', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'review', '2026-03-20 09:08:36', '$2y$10$7oWPeXA7Os9Fb.Pg2CTnSOIDq1IBhDjnwARivjMAeG/1RHFGm1khy', '2026-03-13 18:02:17'),
(7, 'Jacob', 'jacob@gmail.com', NULL, '3692814700', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'review', '2026-03-14 05:19:00', '$2y$10$lglLVI4I4/iayT7LO9NrPOIxRpPk9eQmrgv9eML7ioX8CNVDy/hdC', '2026-03-14 10:24:50'),
(8, 'Karthika', 'karthika@gmail.com', NULL, '9876543210', 'candidate', NULL, NULL, NULL, NULL, NULL, NULL, 1, 'review', '2026-03-21 08:52:37', '$2y$10$YsLg0VS0fZzYrbBtMTj5/ednzAb07wKv//MGElnDhU.63NdR0hNym', '2026-03-21 14:19:46'),
(9, 'Vishnu', 'vishnu@insightdata.com', NULL, '+919544104305', 'recruiter', 3, NULL, '2026-03-23 05:52:08', NULL, NULL, '2026-03-23 05:52:22', 0, NULL, NULL, '$2y$10$5Q/BscgPqD4RhFXf8AnvdOU8VPV8Q7DCY1D4RUvP5UZE4q/fKUAaa', '2026-03-23 11:21:54');

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
(1, 2, 'Software Developer', 'SANDS Lab', 'Full-time', 'Thrissur, Kerala', '2020-02-03', '2022-06-24', 0, '', '2026-03-06 06:33:51', '2026-03-13 00:51:45'),
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
-- Indexes for table `interview_booking_reviews`
--
ALTER TABLE `interview_booking_reviews`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_id` (`booking_id`),
  ADD KEY `application_id` (`application_id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`),
  ADD KEY `recruiter_id` (`recruiter_id`);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `career_transitions`
--
ALTER TABLE `career_transitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `company_reviews`
--
ALTER TABLE `company_reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `daily_tasks`
--
ALTER TABLE `daily_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `interview_bookings`
--
ALTER TABLE `interview_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interview_booking_reviews`
--
ALTER TABLE `interview_booking_reviews`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interview_slots`
--
ALTER TABLE `interview_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `job_alerts`
--
ALTER TABLE `job_alerts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `recruiter_candidate_actions`
--
ALTER TABLE `recruiter_candidate_actions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `recruiter_candidate_messages`
--
ALTER TABLE `recruiter_candidate_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `recruiter_candidate_notes`
--
ALTER TABLE `recruiter_candidate_notes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `recruiter_company_map`
--
ALTER TABLE `recruiter_company_map`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `reschedule_history`
--
ALTER TABLE `reschedule_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
