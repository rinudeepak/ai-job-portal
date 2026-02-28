-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 11:40 AM
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
  `status` enum('applied','ai_interview_started','ai_interview_completed','ai_evaluated','shortlisted','rejected','interview_slot_booked') DEFAULT NULL,
  `interview_slot` datetime DEFAULT NULL,
  `ai_interview_id` int(11) DEFAULT NULL,
  `booking_id` int(11) DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `candidate_id`, `resume_version_id`, `job_id`, `status`, `interview_slot`, `ai_interview_id`, `booking_id`, `applied_at`) VALUES
(74, 47, NULL, 56, 'applied', NULL, NULL, NULL, '2026-02-21 10:51:48'),
(75, 47, NULL, 57, 'rejected', NULL, NULL, NULL, '2026-02-23 07:15:19'),
(76, 49, NULL, 56, 'shortlisted', NULL, NULL, NULL, '2026-02-23 11:30:51'),
(77, 66, NULL, 60, 'applied', NULL, NULL, NULL, '2026-02-26 09:30:54'),
(78, 67, NULL, 62, 'shortlisted', NULL, NULL, NULL, '2026-02-27 07:43:26'),
(79, 47, 1, 62, 'shortlisted', NULL, NULL, NULL, '2026-02-28 06:18:56');

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
(1, 47, 'rinudeepak', 3, 54, 'HTML,PHP,CSS,JavaScript,Hack', 3, '2026-02-23 06:53:38'),
(2, 67, 'rinudeepak', 3, 59, 'HTML,PHP,CSS,JavaScript,Hack', 3, '2026-02-27 06:51:04'),
(4, 68, 'rinudeepak', 3, 59, 'HTML,PHP,CSS,JavaScript,Hack', 3, '2026-02-28 07:12:26');

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
(1, 47, 'UI/UX Design, Backend Development, Full Stack'),
(2, 68, 'Web Development');

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

--
-- Dumping data for table `candidate_projects`
--

INSERT INTO `candidate_projects` (`id`, `user_id`, `project_name`, `role_name`, `tech_stack`, `project_url`, `project_summary`, `impact_metrics`, `start_date`, `end_date`, `created_at`, `updated_at`) VALUES
(1, 68, 'HireMatrix', 'Lead Developer', '', 'https://jobportalrinu.allytechcourses.com/', 'This project is a Job Portal System designed to help candidates search for jobs and transition into new career roles based on their skills and interests.', '', NULL, NULL, '2026-02-28 07:31:01', '2026-02-28 07:31:01');

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
(1, 47, NULL, NULL, NULL, 'PHP Developer', 'PHP Developer', 'Web Developer', 'role_based', 'uploads/resumes/Rinu_George_Resume_14.pdf', 'Detail-oriented PHP Developer with a strong foundation in web development and a passion for backend solutions. Proven ability to deliver high-quality web applications, optimize performance, and enhance user experiences.', 'PHP, MySQL, JavaScript', '# Professional Summary\nDynamic PHP Developer with extensive experience in developing robust web applications. Expertise in PHP and MySQL, complemented by a background in JavaScript for front-end functionality. Committed to leveraging technical skills to contribute to effective web solutions.\n\n# Core Skills\n- Proficient in PHP and MySQL for backend development\n- Strong understanding of JavaScript for interactive web features\n- Experience with UI/UX design principles\n- Ability to optimize web applications for speed and efficiency\n\n# Experience\n## Web Developer  \n**ABC Company**  \n*Full-time | Bangalore*  \n*December 2025 - Present*  \n- Spearheaded the development of a PHP-based web application that improved user engagement by 30% through enhanced UI/UX design.\n- Collaborated with cross-functional teams to integrate MySQL databases, ensuring data integrity and security.\n- Optimized existing codebase, resulting in a 20% reduction in load times and improved overall application performance.\n\n# Education\n**B.Sc. in Computer Science**  \nPrajyoti Niketan College, Pudukad, Thrissur  \n*2012 - 2015*  \n\n# Certifications\n- (No certifications listed)\n\n# Transition Narrative\nTransitioning from a general web development role to a specialized PHP Developer position, I aim to apply my skills in PHP and MySQL to create efficient and scalable web applications.', 1, '2026-02-28 06:00:22', '2026-02-28 06:00:22', '2026-02-28 06:00:22'),
(2, 47, 57, NULL, NULL, 'Mechanical Engineer', 'Mechanical Engineer', 'Web Developer', 'job_version', 'uploads/resumes/Rinu_George_Resume_14.pdf', 'Detail-oriented Mechanical Engineer with a strong foundation in web development and a passion for improving manufacturing machinery. Seeking to leverage technical skills and analytical thinking to enhance operational efficiency in mechanical systems.', 'Problem Solving, Analytical Thinking, Project Management', '# Professional Summary\nDedicated Mechanical Engineer with a background in web development, bringing a unique perspective to mechanical systems. Proven ability to analyze and improve machinery performance through innovative solutions.\n\n# Core Skills\n- Mechanical Design\n- Manufacturing Processes\n- CAD Software\n- Project Management\n- Problem Solving\n\n# Experience\n## Web Developer  \n**ABC Company**  \n*December 2025 - Present*  \n- Developed and maintained web applications, enhancing user experience and functionality.  \n- Collaborated with cross-functional teams to identify and implement process improvements, leading to a 20% increase in efficiency.\n\n# Education\n**B.Sc. in Computer Science**  \nPrajyoti Niketan College, Pudukad, Thrissur  \n*2012 - 2015*  \n\n# Certifications\n*None*  \n\n# Transition Narrative\nWhile my background is primarily in web development, I possess a strong analytical skill set and a passion for mechanical systems. I am eager to apply my problem-solving abilities and project management experience to the field of mechanical engineering.', 0, '2026-02-28 06:02:41', '2026-02-28 06:02:41', '2026-02-28 06:02:41'),
(6, 68, NULL, NULL, NULL, 'Web Developer Transitioning to Data Scientist', 'Data Sccientist', 'Web Developer', 'role_based', '', 'Detail-oriented web developer with a strong foundation in software development and a keen interest in data science. Proven track record of delivering high-quality web applications and eager to leverage analytical skills in data-driven environments.', 'Web Development, Software Development, Data Analysis', '{\"template_key\":\"minimal_timeline\",\"name\":\"Praveen \",\"target_role\":\"Data Sccientist\",\"title\":\"Web Developer Transitioning to Data Scientist\",\"summary\":\"Detail-oriented web developer with a strong foundation in software development and a keen interest in data science. Proven track record of delivering high-quality web applications and eager to leverage analytical skills in data-driven environments.\",\"highlight_skills\":[\"Web Development\",\"Software Development\",\"Data Analysis\"],\"sections\":{\"experience\":{\"title\":\"Experience\",\"items\":[{\"headline\":\"Web Developer\",\"subhead\":\"KJP Digital Solutions Pvt Ltd\",\"meta\":\"June 2023 - Present | Location Not Specified\",\"bullets\":[\"Developed responsive web applications, enhancing user engagement by 30%.\",\"Collaborated with cross-functional teams to deliver projects ahead of deadlines.\"]},{\"headline\":\"Software Developer\",\"subhead\":\"SANDS Lab\",\"meta\":\"February 2021 - December 2026 | Location Not Specified\",\"bullets\":[\"Designed and implemented software solutions that improved operational efficiency by 25%.\",\"Contributed to the development of a data management system, streamlining data processes.\"]}]},\"education\":{\"title\":\"Education\",\"items\":[{\"headline\":\"MBA in Marketing\",\"subhead\":\"Adi Shankara Institute of Engineering and Technology, Kalady\",\"meta\":\"2015 - 2017\",\"bullets\":[]},{\"headline\":\"B.Sc. in Computer Science\",\"subhead\":\"Prajyoti Niketan College, Pudukad, Thrissur\",\"meta\":\"2012 - 2015\",\"bullets\":[]}]}}}', 1, '2026-02-28 07:08:19', '2026-02-28 07:08:19', '2026-02-28 07:08:19');

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
(2, 49, 'PHP, MySQL, JavaScript', '2026-02-23 11:30:35'),
(3, 66, 'PHP', '2026-02-26 09:29:47'),
(4, 67, 'PHP', '2026-02-27 06:50:39');

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
(171, 66, 'PHP', 'job3', '[\"JavaScript\",\"Node.js\",\"React\"]', '[{\"phase\":\"Phase 1\",\"duration\":\"4 weeks\",\"focus\":\"Learn JavaScript fundamentals and ES6 features\"},{\"phase\":\"Phase 2\",\"duration\":\"4 weeks\",\"focus\":\"Get hands-on with Node.js and Express.js for backend development\"},{\"phase\":\"Phase 3\",\"duration\":\"4 weeks\",\"focus\":\"Learn React for frontend development and build a portfolio project\"}]', 'inactive', 0, '2026-02-26 10:37:35', '2026-02-26 17:23:40', NULL, 0, '2026-02-26 10:37:35', 'pending'),
(172, 66, 'PHP', 'Python Developer', '[\"Understanding Python syntax and semantics\",\"Familiarity with Python libraries (e.g., NumPy, Pandas)\",\"Experience with Python web frameworks (e.g., Django, Flask)\"]', '[{\"phase\":\"Phase 1\",\"duration\":\"2 weeks\",\"focus\":\"Learn Python basics and syntax\"},{\"phase\":\"Phase 2\",\"duration\":\"3 weeks\",\"focus\":\"Explore Python libraries and frameworks\"},{\"phase\":\"Phase 3\",\"duration\":\"3 weeks\",\"focus\":\"Build projects to apply Python skills\"}]', 'active', 0, '2026-02-26 11:55:35', NULL, NULL, 0, '2026-02-26 11:55:35', 'pending'),
(173, 68, 'Web Developer', 'Data Scientist', '[\"statistical analysis\",\"machine learning algorithms\",\"data visualization\"]', '[{\"phase\":\"Phase 1\",\"duration\":\"4 weeks\",\"focus\":\"Foundational knowledge in statistics and programming in Python\"},{\"phase\":\"Phase 2\",\"duration\":\"4 weeks\",\"focus\":\"Advanced data analysis techniques and introduction to machine learning\"},{\"phase\":\"Phase 3\",\"duration\":\"4 weeks\",\"focus\":\"Practical application of data science concepts through projects\"}]', 'active', 0, '2026-02-28 07:02:44', NULL, NULL, 0, '2026-02-28 07:02:44', 'pending');

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
(1, 'TechNova Solutions', 'uploads/company_logos/1771669764_20d47a35f4f9596afa95.jpg', 'https://www.technovasolutions.com', 'IT Services', '50-200', 'Bangalore, India', 'Kochi, Chennai', 'We build scalable digital solutions for modern businesses.', 'We develop web applications, mobile apps, and cloud solutions for startups and enterprises.', 'Innovation, Transparency, Customer Success', 'hr@technovasolutions.com', '+91-9876543210', 1, '2026-02-21 09:27:53', '2026-02-26 07:17:40'),
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
(26, 'GreenLeaf Industries', NULL, 'https://www.greenleafindustries.com', 'Manufacturing', '200-500', 'Coimbatore, India', 'Hyderabad, Pune', 'Sustainable manufacturing for a better tomorrow.', 'We manufacture eco-friendly packaging products and export globally.', 'Sustainability, Integrity, Quality', 'careers@greenleafindustries.com', '', 0, '2026-02-21 10:40:17', '2026-02-21 10:43:18'),
(27, 'SERP Hawk', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-25 09:20:38', '2026-02-25 09:20:38'),
(28, 'SANDS Lab', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-25 10:49:16', '2026-02-25 10:49:16'),
(29, 'xxx', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-26 05:49:32', '2026-02-26 05:49:32'),
(30, 'bbb', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, '2026-02-26 07:37:05', '2026-02-26 07:37:05');

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
(1, 283, 1, 'Understanding JavaScript Basics', '## Overview\nJavaScript is a versatile programming language primarily used for web development. It allows you to create interactive user interfaces and is essential for full-stack development. Learning JavaScript will help you bridge the gap between your PHP backend skills and front-end technologies.\n\n## Step-by-Step Guide\n### Step 1: Setting Up Your Environment\n1. **Install Node.js** – Download and install Node.js from [nodejs.org](https://nodejs.org/). This will allow you to run JavaScript on your machine.\n2. **Choose a Code Editor** – Use Visual Studio Code, which is popular among JavaScript developers. Download it from [code.visualstudio.com](https://code.visualstudio.com/).\n\n### Step 2: Write Your First JavaScript Code\n1. Open Visual Studio Code and create a new file named `app.js`.\n2. Add the following code:\n   javascript\n   console.log(\'Hello, World!\');\n   \n3. Save the file and run it in your terminal using the command:\n   bash\n   node app.js\n   \n4. You should see `Hello, World!` printed in your terminal.\n\n## Real-World Application\nJavaScript is used for everything from simple website enhancements to complex web applications. For example, if you’re building a dynamic web dashboard, JavaScript can handle user interactions and update the UI without needing to refresh the page.\n\n## Common Challenges\n- **Scope and Hoisting**: Understanding how variable scope works in JavaScript can be tricky. Remember that variables declared with `var` are function-scoped, while `let` and `const` are block-scoped.\n- **Asynchronous Programming**: JavaScript is non-blocking by nature. Familiarize yourself with callbacks, promises, and async/await to handle asynchronous operations effectively.\n\n## Key Takeaways\n- JavaScript is essential for web development.\n- Setting up your environment is the first step to start coding.\n- Writing and executing simple scripts lays the foundation for more complex applications.', '[\"https:\\/\\/developer.mozilla.org\\/en-US\\/docs\\/Web\\/JavaScript\\/Guide\",\"https:\\/\\/javascript.info\\/\"]', '[\"Create a simple JavaScript program that takes user input and displays it in the console.\",\"Experiment with different JavaScript data types and console log their types.\"]', 0),
(2, 283, 2, 'Working with Functions and DOM Manipulation', '## Overview\nFunctions are the building blocks of JavaScript applications. Understanding how to manipulate the Document Object Model (DOM) will enable you to create interactive web applications.\n\n## Step-by-Step Guide\n### Step 1: Creating Functions\n1. Open your existing `app.js` file.\n2. Add a function that takes a name as input and logs a greeting:\n   javascript\n   function greet(name) {\n       console.log(\'Hello, \' + name + \'!\');\n   }\n   greet(\'Alice\'); // Should print \'Hello, Alice!\'\n   \n3. Save and run your script using `node app.js`.\n\n### Step 2: Manipulating the DOM\n1. Create an HTML file named `index.html` and add the following code:\n   html\n   <!DOCTYPE html>\n   <html lang=\"en\">\n   <head>\n       <meta charset=\"UTF-8\">\n       <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n       <title>DOM Manipulation Example</title>\n   </head>\n   <body>\n       <h1 id=\"greeting\"></h1>\n       <script src=\"app.js\"></script>\n   </body>\n   </html>\n   \n2. In your `app.js`, add code to change the content of the heading:\n   javascript\n   document.getElementById(\'greeting\').innerText = \'Welcome to JavaScript!\';\n   \n3. Open `index.html` in your browser to see the result.\n\n## Real-World Application\nFunctions allow you to modularize your code, making it reusable and easier to maintain. DOM manipulation is key for creating responsive applications that react to user inputs, such as forms and buttons.\n\n## Common Challenges\n- **Understanding Scope**: Functions can have access to variables defined within their scope. Ensure you understand how variable scope works in functions.\n- **Event Listeners**: Adding interactivity requires understanding how to attach event listeners to DOM elements. Start with simple events like click events.\n\n## Key Takeaways\n- Functions encapsulate logic that can be reused.\n- Manipulating the DOM is essential for creating interactive web applications.\n- Always test your code in the browser to see real-time results.', '[\"https:\\/\\/developer.mozilla.org\\/en-US\\/docs\\/Web\\/JavaScript\\/Guide\\/Functions\",\"https:\\/\\/www.w3schools.com\\/js\\/js_htmldom.asp\"]', '[\"Create a function that takes two numbers and returns their sum. Use it in your HTML to display the result.\",\"Add a button in your HTML that changes the background color of the page when clicked.\"]', 0),
(3, 284, 1, 'Setting Up a Node.js Server', '## Overview\nNode.js is a runtime environment that allows you to run JavaScript on the server side. Learning Node.js will empower you to create backend applications and APIs.\n\n## Step-by-Step Guide\n### Step 1: Create a Simple Server\n1. In your project folder, create a file named `server.js`.\n2. Add the following code:\n   javascript\n   const http = require(\'http\');\n\n   const server = http.createServer((req, res) => {\n       res.statusCode = 200;\n       res.setHeader(\'Content-Type\', \'text/plain\');\n       res.end(\'Hello, Node.js!\n\');\n   });\n\n   server.listen(3000, () => {\n       console.log(\'Server running at http://localhost:3000/\');\n   });\n   \n3. Run your server using the command:\n   bash\n   node server.js\n   \n4. Visit `http://localhost:3000/` in your browser to see the message.\n\n### Step 2: Understanding Routing\n1. Modify your server code to handle different routes:\n   javascript\n   const server = http.createServer((req, res) => {\n       if (req.url === \'/\') {\n           res.end(\'Home Page\');\n       } else if (req.url === \'/about\') {\n           res.end(\'About Page\');\n       } else {\n           res.statusCode = 404;\n           res.end(\'Page Not Found\');\n       }\n   });\n   \n2. Test the different routes by visiting `/` and `/about` in your browser.\n\n## Real-World Application\nNode.js is widely used for building scalable web applications and APIs. For example, you can create a REST API that your React front-end communicates with to fetch or send data.\n\n## Common Challenges\n- **Asynchronous Code**: Node.js is non-blocking, which means you need to understand callbacks and promises to manage asynchronous operations.\n- **Error Handling**: Ensure you handle errors gracefully in your application to enhance the user experience.\n\n## Key Takeaways\n- Node.js allows you to run JavaScript on the server side.\n- Setting up a basic server is the first step in backend development.\n- Routing is essential for handling different requests in your application.', '[\"https:\\/\\/nodejs.dev\\/learn\",\"https:\\/\\/www.freecodecamp.org\\/news\\/nodejs-tutorial-for-beginners\\/\"]', '[\"Create a new route that returns a JSON object when accessed.\",\"Implement error handling in your server for undefined routes.\"]', 0),
(4, 284, 2, 'Building RESTful APIs with Express', '## Overview\nExpress.js is a web application framework for Node.js that simplifies the process of building RESTful APIs. Learning Express will enable you to create robust and maintainable server-side applications.\n\n## Step-by-Step Guide\n### Step 1: Installing Express\n1. In your project folder, run:\n   bash\n   npm init -y\n   npm install express\n   \n2. Create a new file named `app.js`.\n\n### Step 2: Building a Basic API\n1. In `app.js`, add the following code to set up a basic Express server:\n   javascript\n   const express = require(\'express\');\n   const app = express();\n\n   app.get(\'/\', (req, res) => {\n       res.send(\'Welcome to the Express API!\');\n   });\n\n   const PORT = 3000;\n   app.listen(PORT, () => {\n       console.log(`Server running on http://localhost:${PORT}`);\n   });\n   \n2. Run your server using:\n   bash\n   node app.js\n   \n3. Visit `http://localhost:3000/` in your browser to see the API response.\n\n### Step 3: Creating API Endpoints\n1. Add a new endpoint to return a list of users:\n   javascript\n   const users = [{ id: 1, name: \'Alice\' }, { id: 2, name: \'Bob\' }];\n\n   app.get(\'/users\', (req, res) => {\n       res.json(users);\n   });\n   \n2. Test the `/users` endpoint in your browser or using Postman.\n\n## Real-World Application\nRESTful APIs are crucial for web applications, allowing different services to communicate. Your React front end will make HTTP requests to this API to retrieve and update data.\n\n## Common Challenges\n- **Middleware Understanding**: Middleware functions are used in Express for processing requests. Familiarize yourself with how middleware works.\n- **Versioning APIs**: As your application grows, you may need to version your APIs. Plan how you’ll manage different versions of your API.\n\n## Key Takeaways\n- Express.js simplifies building RESTful APIs.\n- Setting up routes and handling requests is crucial for API development.\n- Understanding middleware is key to managing request processing.', '[\"https:\\/\\/expressjs.com\\/en\\/starter\\/installing.html\",\"https:\\/\\/www.taniarascia.com\\/node-express-server-rest-api\\/\"]', '[\"Create an endpoint to add a new user to the users array using POST requests.\",\"Implement middleware that logs the request method and URL for each request.\"]', 0),
(5, 285, 1, 'Getting Started with React', '## Overview\nReact is a powerful library for building dynamic user interfaces. It allows developers to create reusable UI components, making development more efficient.\n\n## Step-by-Step Guide\n### Step 1: Setting Up a React App\n1. Make sure you have Node.js and npm installed. Create a new React app using Create React App:\n   bash\n   npx create-react-app my-app\n   cd my-app\n   npm start\n   \n2. Open your browser and navigate to `http://localhost:3000/` to see your new React app.\n\n### Step 2: Understanding Components\n1. Open `src/App.js` and modify it to create a simple functional component:\n   javascript\n   function Welcome() {\n       return <h2>Welcome to React!</h2>;\n   }\n\n   function App() {\n       return (\n           <div>\n               <Welcome />\n           </div>\n       );\n   }\n\n   export default App;\n   \n2. Save the changes and see the output update in the browser.\n\n## Real-World Application\nReact is commonly used for front-end development in modern web applications. It allows you to create single-page applications (SPAs) that load quickly and provide a smooth user experience.\n\n## Common Challenges\n- **JSX Syntax**: React uses JSX, which may be unfamiliar. Practice translating HTML into JSX syntax.\n- **State Management**: As applications grow, managing component state can become complex. Consider using React\'s `useState` and `useEffect` hooks for state management.\n\n## Key Takeaways\n- React allows for building reusable UI components.\n- Understanding how to create and use components is fundamental to working with React.\n- The Create React App tool sets up a React environment quickly.', '[\"https:\\/\\/reactjs.org\\/docs\\/getting-started.html\",\"https:\\/\\/reactjs.org\\/tutorial\\/tutorial.html\"]', '[\"Create additional components to display a list of items.\",\"Experiment with props by passing data from a parent component to a child component.\"]', 0),
(6, 285, 2, 'State Management and Effects in React', '## Overview\nManaging state and handling side effects are crucial for creating interactive applications in React. This lesson will cover how to use state and side effects effectively.\n\n## Step-by-Step Guide\n### Step 1: Using State with Hooks\n1. Modify your `App.js` to include state using the `useState` hook:\n   javascript\n   import React, { useState } from \'react\';\n\n   function App() {\n       const [count, setCount] = useState(0);\n\n       return (\n           <div>\n               <h1>Count: {count}</h1>\n               <button onClick={() => setCount(count + 1)}>Increment</button>\n           </div>\n       );\n   }\n\n   export default App;\n   \n2. This creates a simple counter application. Test it by clicking the button to increment the count.\n\n### Step 2: Handling Side Effects with useEffect\n1. Add an effect to log the count each time it changes:\n   javascript\n   import React, { useState, useEffect } from \'react\';\n\n   function App() {\n       const [count, setCount] = useState(0);\n\n       useEffect(() => {\n           console.log(`Count updated: ${count}`);\n       }, [count]);\n\n       return (\n           <div>\n               <h1>Count: {count}</h1>\n               <button onClick={() => setCount(count + 1)}>Increment</button>\n           </div>\n       );\n   }\n\n   export default App;\n   \n2. Each time the count is updated, you should see a log in the console.\n\n## Real-World Application\nManaging state and side effects effectively is crucial in applications where user interactions are frequent. For instance, fetching data from an API and storing it in the component state to render it.\n\n## Common Challenges\n- **State Updates**: Remember that updates to state are asynchronous. Make sure you understand how to manage state updates properly.\n- **Dependency Arrays**: When using `useEffect`, be cautious about what you include in the dependency array to avoid unnecessary re-renders.\n\n## Key Takeaways\n- React\'s `useState` and `useEffect` hooks are essential for managing state and side effects.\n- Understanding these concepts will help you build dynamic applications.', '[\"https:\\/\\/reactjs.org\\/docs\\/hooks-state.html\",\"https:\\/\\/reactjs.org\\/docs\\/hooks-effect.html\"]', '[\"Create a component that fetches data from an API and displays it.\",\"Implement a feature that resets the count to zero when a button is clicked.\"]', 0),
(7, 286, 1, 'Understanding Core Concepts', 'Begin your journey by understanding the fundamental principles that define Python Developer. This role requires a solid grasp of Understanding Python syntax and semantics, Familiarity with Python libraries (e.g., NumPy, Pandas), Experience with Python web frameworks (e.g., Django, Flask).\n\nKey Concepts:\n\nFirst, research industry standards and best practices. Understanding the landscape is crucial - study what makes professionals successful in this role. Read official documentation, follow industry leaders on social media, and join relevant online communities.\n\nSecond, understand how these skills interconnect. No skill exists in isolation - they work together to solve real-world problems. For example, if you\'re learning a new programming language, understand how it integrates with databases, APIs, and frontend frameworks.\n\nThird, build a strong theoretical foundation before diving into practical applications. While hands-on practice is important, understanding the \'why\' behind concepts will make you a better problem-solver. Study design patterns, architectural principles, and the reasoning behind best practices.\n\nPractical Approach:\n\nCreate a personal knowledge base documenting key concepts, terminologies, and best practices. Use tools like Notion, Obsidian, or even a simple markdown file in GitHub. This becomes your reference throughout the learning journey.\n\nStudy real-world use cases and analyze how professionals approach problem-solving. Look at open-source projects, read technical blogs, and watch conference talks. Pay attention to how experienced developers structure their code and make decisions.\n\nFinally, practice explaining concepts in simple terms. If you can teach something, you truly understand it. Write blog posts, create tutorials, or explain concepts to friends. This reinforces your learning and builds your personal brand.', '[\"https:\\/\\/www.coursera.org\\/courses?query=Python+Developer\",\"https:\\/\\/www.udemy.com\\/courses\\/search\\/?q=Python+Developer\",\"https:\\/\\/roadmap.sh\\/\"]', '[\"Create a comprehensive mind map of key concepts in Python Developer\",\"Write a 500-word summary explaining the role requirements to a beginner\",\"List 10 companies hiring for this role and analyze their common requirements\"]', 0),
(8, 286, 2, 'Hands-on Practice', 'Theory alone is insufficient - practical application is crucial for mastering Python Developer.\n\nSetting Up Your Environment:\n\nStart by setting up a proper development environment. Install necessary tools, configure your IDE, and familiarize yourself with the ecosystem. Don\'t skip this step - a well-configured environment saves hours of frustration later.\n\nUse version control (Git) from day one. Even for small projects, commit regularly with meaningful messages. This builds good habits and creates a portfolio of your progress. Push your code to GitHub to make it accessible and shareable.\n\nBuilding Projects:\n\nBegin with tutorials but don\'t just copy-paste code. Type everything manually and experiment with modifications. Ask yourself: \'What happens if I change this?\' Breaking things and fixing them is how you truly learn.\n\nGradually increase project complexity. Start with a simple \'Hello World\', then build a calculator, then a todo app, then something more complex. Each project should challenge you slightly beyond your current comfort zone.\n\nFocus on writing clean, maintainable code following industry best practices. Use meaningful variable names, write comments for complex logic, and structure your code logically. Bad habits formed early are hard to break.\n\nLearning from Others:\n\nJoin online communities like Stack Overflow, Reddit, or Discord servers related to your target role. Don\'t just ask questions - answer them too. Teaching others reinforces your own understanding.\n\nParticipate in code reviews. Share your projects and ask for feedback. Be open to criticism - every critique is an opportunity to improve. Similarly, review others\' code to learn different approaches.\n\nBuild at least 3-5 small projects that demonstrate your understanding. Document each project thoroughly with README files explaining your approach, challenges faced, and solutions implemented. This becomes your portfolio.', '[\"https:\\/\\/github.com\\/topics\\/python+developer\",\"https:\\/\\/stackoverflow.com\\/\",\"https:\\/\\/www.freecodecamp.org\\/\"]', '[\"Complete 10 beginner-level coding challenges on LeetCode or HackerRank\",\"Build a simple project using your new skills and deploy it online\",\"Contribute to an open-source project on GitHub (even fixing typos counts!)\"]', 0),
(9, 287, 1, 'Advanced Technical Skills', 'Now that you have a foundation, it\'s time to dive deeper into advanced topics that separate beginners from professionals in Python Developer.\n\nDesign Patterns and Architecture:\n\nStudy common design patterns like Singleton, Factory, Observer, and Strategy. These aren\'t just academic concepts - they\'re proven solutions to recurring problems. Understanding when and how to apply them is crucial.\n\nLearn about architectural principles like SOLID, DRY (Don\'t Repeat Yourself), and KISS (Keep It Simple, Stupid). These principles guide you in writing maintainable, scalable code that other developers can understand and extend.\n\nUnderstand different architectural styles: MVC, microservices, serverless, event-driven architecture. Each has its use cases, advantages, and trade-offs. Know when to use which approach.\n\nPerformance and Optimization:\n\nLearn about performance optimization techniques. Understand time and space complexity (Big O notation). Profile your applications to identify bottlenecks. Remember: premature optimization is the root of all evil, but knowing how to optimize when needed is essential.\n\nStudy caching strategies, database indexing, and query optimization. Many performance issues stem from inefficient database operations. Learn to write efficient queries and use appropriate indexes.\n\nTesting and Quality:\n\nMaster testing methodologies: unit tests, integration tests, end-to-end tests. Write tests before or alongside your code (TDD/BDD). Tests are documentation that never goes out of date and give you confidence to refactor.\n\nUnderstand debugging techniques. Learn to use debuggers effectively, read stack traces, and systematically isolate issues. Good debugging skills save countless hours.\n\nContinuous Learning:\n\nRead source code of popular libraries and frameworks. This exposes you to professional coding standards and advanced techniques. Don\'t just use libraries - understand how they work internally.\n\nAttend webinars, watch conference talks, and follow industry experts. Technology evolves rapidly - staying current is part of the job. Subscribe to newsletters, podcasts, and blogs in your field.', '[\"https:\\/\\/refactoring.guru\\/design-patterns\",\"https:\\/\\/www.patterns.dev\\/\",\"https:\\/\\/martinfowler.com\\/\"]', '[\"Refactor an existing project using at least 3 design patterns\",\"Write comprehensive unit tests achieving 80%+ code coverage\",\"Optimize a slow application and document the improvements with benchmarks\"]', 0),
(10, 287, 2, 'Production-Ready Applications', 'Professional developers build applications that are maintainable, scalable, and production-ready. This lesson covers what it takes to deploy and maintain real-world applications.\n\nCI/CD and DevOps:\n\nLearn about Continuous Integration and Continuous Deployment. Set up automated pipelines that run tests, check code quality, and deploy automatically. Tools like GitHub Actions, Jenkins, or GitLab CI make this accessible.\n\nUnderstand containerization with Docker. Containers ensure your application runs consistently across different environments. Learn to write Dockerfiles and use docker-compose for multi-container applications.\n\nExplore orchestration with Kubernetes if working with microservices. While complex, Kubernetes is industry-standard for managing containerized applications at scale.\n\nCloud Platforms:\n\nStudy major cloud platforms: AWS, Azure, or Google Cloud. You don\'t need to master all services, but understand core offerings: compute (EC2, Lambda), storage (S3), databases (RDS), and networking (VPC).\n\nLearn Infrastructure as Code (IaC) using tools like Terraform or CloudFormation. Managing infrastructure through code makes it reproducible, version-controlled, and easier to maintain.\n\nMonitoring and Observability:\n\nImplement logging using structured logging libraries. Good logs are invaluable for debugging production issues. Log meaningful information but avoid logging sensitive data.\n\nSet up error tracking with tools like Sentry or Rollbar. Know when things break in production before users complain. Configure alerts for critical errors.\n\nImplement performance monitoring and APM (Application Performance Monitoring). Tools like New Relic or DataDog help identify performance bottlenecks in production.\n\nSecurity Best Practices:\n\nUnderstand common security vulnerabilities (OWASP Top 10): SQL injection, XSS, CSRF, etc. Learn how to prevent them. Security isn\'t optional - it\'s fundamental.\n\nImplement proper authentication and authorization. Use established libraries and frameworks rather than rolling your own. Understand OAuth, JWT, and session management.\n\nPractice defense in depth: validate all inputs, sanitize outputs, use HTTPS, keep dependencies updated, and follow the principle of least privilege.', '[\"https:\\/\\/12factor.net\\/\",\"https:\\/\\/aws.amazon.com\\/getting-started\\/\",\"https:\\/\\/owasp.org\\/www-project-top-ten\\/\"]', '[\"Deploy an application to a cloud platform with proper CI\\/CD pipeline\",\"Set up monitoring, logging, and alerting for a production application\",\"Implement authentication and authorization with proper security measures\"]', 0),
(11, 288, 1, 'Building Your Portfolio', 'Your portfolio is your professional showcase - it\'s often more important than your resume for technical roles.\n\nCreating Your Portfolio Website:\n\nBuild a personal website that highlights your projects, skills, and achievements. Keep it simple, fast, and mobile-responsive. Your portfolio itself demonstrates your technical skills.\n\nInclude an \'About Me\' section that tells your story. Why are you transitioning to Python Developer? What drives you? Make it personal and authentic.\n\nShowcase 3-5 of your best projects. Quality over quantity - it\'s better to have three polished projects than ten half-finished ones.\n\nProject Case Studies:\n\nFor each project, write a detailed case study explaining:\n- The problem you were solving\n- Your approach and technical decisions\n- Challenges faced and how you overcame them\n- The impact or results\n- Technologies used and why\n\nInclude screenshots, diagrams, and code snippets. Make it easy for recruiters to understand your work even if they\'re not technical.\n\nProvide links to live demos and GitHub repositories. Ensure your code is clean, well-documented, and includes a comprehensive README.\n\nGitHub Profile Optimization:\n\nYour GitHub profile is your technical resume. Ensure it\'s polished:\n- Complete profile with photo and bio\n- Pinned repositories showcasing your best work\n- Consistent commit history (shows you code regularly)\n- Well-documented repositories with clear README files\n- Meaningful commit messages\n\nContribute to open-source projects. Even small contributions (documentation, bug fixes) demonstrate collaboration skills and initiative.\n\nContent Creation:\n\nWrite technical blog posts about your learning journey. Share insights, tutorials, or solutions to problems you\'ve solved. This demonstrates communication skills and helps others.\n\nCreate video demos of your projects. A 2-3 minute walkthrough showing functionality and explaining technical decisions is powerful.\n\nBe active on LinkedIn. Share your projects, write posts about what you\'re learning, and engage with the community. Networking is crucial for career transitions.', '[\"https:\\/\\/github.com\\/topics\\/portfolio-website\",\"https:\\/\\/dev.to\\/\",\"https:\\/\\/www.linkedin.com\\/\"]', '[\"Create a professional portfolio website and deploy it\",\"Write 3 technical blog posts about your learning journey\",\"Record a 5-minute video demo of your best project\"]', 0),
(12, 288, 2, 'Interview Preparation', 'Preparing systematically for technical interviews is crucial for successfully transitioning to Python Developer.\n\nTechnical Interview Preparation:\n\nPractice coding challenges daily on platforms like LeetCode, HackerRank, or CodeSignal. Start with easy problems and gradually increase difficulty. Aim to solve at least 100-150 problems.\n\nFocus on data structures and algorithms: arrays, linked lists, trees, graphs, sorting, searching, dynamic programming. These form the foundation of technical interviews.\n\nUnderstand time and space complexity (Big O notation). You\'ll be asked to analyze the efficiency of your solutions. Practice explaining your thought process clearly.\n\nSystem Design Interviews:\n\nFor senior roles, study system design. Learn to design scalable systems: load balancers, caching, databases, microservices, message queues.\n\nPractice explaining trade-offs. There\'s rarely one \'correct\' answer in system design - it\'s about understanding pros and cons of different approaches.\n\nStudy real-world architectures: how does Twitter handle millions of tweets? How does Netflix stream video globally? Learn from these examples.\n\nBehavioral Interviews:\n\nPrepare stories using the STAR method (Situation, Task, Action, Result). Have examples ready for:\n- Challenging projects you\'ve worked on\n- Times you\'ve failed and what you learned\n- Conflicts with team members and how you resolved them\n- Leadership and initiative\n\nBe honest about your career transition. Frame it positively - you\'re not running from something, you\'re running toward something. Explain what excites you about the new role.\n\nJob Search Strategy:\n\nResearch companies thoroughly before applying. Tailor your resume and cover letter for each position. Generic applications rarely succeed.\n\nNetwork actively. Many jobs are filled through referrals before they\'re even posted. Attend meetups, conferences, and online events. Connect with people in your target role.\n\nPrepare thoughtful questions to ask interviewers. This shows genuine interest and helps you evaluate if the company is right for you.\n\nMock Interviews:\n\nPractice mock interviews with peers or use platforms like Pramp. Getting comfortable with the interview format is crucial.\n\nRecord yourself explaining technical concepts. Watch the recordings to improve your communication.\n\nStay Positive:\n\nRejections are part of the process. Each interview is practice for the next one. Learn from feedback and keep improving.\n\nKeep track of applications in a spreadsheet. Follow up professionally after interviews. Persistence pays off.', '[\"https:\\/\\/leetcode.com\\/\",\"https:\\/\\/www.pramp.com\\/\",\"https:\\/\\/www.glassdoor.com\\/Interview\\/\"]', '[\"Solve 50 coding problems on LeetCode (mix of easy, medium, hard)\",\"Complete 5 mock interviews with peers or online platforms\",\"Apply to 20 relevant job positions with tailored resumes\"]', 0),
(13, 289, 1, 'Understanding Core Concepts', 'Begin your journey by understanding the fundamental principles that define Data Scientist. This role requires a solid grasp of statistical analysis, machine learning algorithms, data visualization.\n\nKey Concepts:\n\nFirst, research industry standards and best practices. Understanding the landscape is crucial - study what makes professionals successful in this role. Read official documentation, follow industry leaders on social media, and join relevant online communities.\n\nSecond, understand how these skills interconnect. No skill exists in isolation - they work together to solve real-world problems. For example, if you\'re learning a new programming language, understand how it integrates with databases, APIs, and frontend frameworks.\n\nThird, build a strong theoretical foundation before diving into practical applications. While hands-on practice is important, understanding the \'why\' behind concepts will make you a better problem-solver. Study design patterns, architectural principles, and the reasoning behind best practices.\n\nPractical Approach:\n\nCreate a personal knowledge base documenting key concepts, terminologies, and best practices. Use tools like Notion, Obsidian, or even a simple markdown file in GitHub. This becomes your reference throughout the learning journey.\n\nStudy real-world use cases and analyze how professionals approach problem-solving. Look at open-source projects, read technical blogs, and watch conference talks. Pay attention to how experienced developers structure their code and make decisions.\n\nFinally, practice explaining concepts in simple terms. If you can teach something, you truly understand it. Write blog posts, create tutorials, or explain concepts to friends. This reinforces your learning and builds your personal brand.', '[\"https:\\/\\/www.coursera.org\\/courses?query=Data+Scientist\",\"https:\\/\\/www.udemy.com\\/courses\\/search\\/?q=Data+Scientist\",\"https:\\/\\/roadmap.sh\\/\"]', '[\"Create a comprehensive mind map of key concepts in Data Scientist\",\"Write a 500-word summary explaining the role requirements to a beginner\",\"List 10 companies hiring for this role and analyze their common requirements\"]', 0),
(14, 289, 2, 'Hands-on Practice', 'Theory alone is insufficient - practical application is crucial for mastering Data Scientist.\n\nSetting Up Your Environment:\n\nStart by setting up a proper development environment. Install necessary tools, configure your IDE, and familiarize yourself with the ecosystem. Don\'t skip this step - a well-configured environment saves hours of frustration later.\n\nUse version control (Git) from day one. Even for small projects, commit regularly with meaningful messages. This builds good habits and creates a portfolio of your progress. Push your code to GitHub to make it accessible and shareable.\n\nBuilding Projects:\n\nBegin with tutorials but don\'t just copy-paste code. Type everything manually and experiment with modifications. Ask yourself: \'What happens if I change this?\' Breaking things and fixing them is how you truly learn.\n\nGradually increase project complexity. Start with a simple \'Hello World\', then build a calculator, then a todo app, then something more complex. Each project should challenge you slightly beyond your current comfort zone.\n\nFocus on writing clean, maintainable code following industry best practices. Use meaningful variable names, write comments for complex logic, and structure your code logically. Bad habits formed early are hard to break.\n\nLearning from Others:\n\nJoin online communities like Stack Overflow, Reddit, or Discord servers related to your target role. Don\'t just ask questions - answer them too. Teaching others reinforces your own understanding.\n\nParticipate in code reviews. Share your projects and ask for feedback. Be open to criticism - every critique is an opportunity to improve. Similarly, review others\' code to learn different approaches.\n\nBuild at least 3-5 small projects that demonstrate your understanding. Document each project thoroughly with README files explaining your approach, challenges faced, and solutions implemented. This becomes your portfolio.', '[\"https:\\/\\/github.com\\/topics\\/data+scientist\",\"https:\\/\\/stackoverflow.com\\/\",\"https:\\/\\/www.freecodecamp.org\\/\"]', '[\"Complete 10 beginner-level coding challenges on LeetCode or HackerRank\",\"Build a simple project using your new skills and deploy it online\",\"Contribute to an open-source project on GitHub (even fixing typos counts!)\"]', 0),
(15, 290, 1, 'Advanced Technical Skills', 'Now that you have a foundation, it\'s time to dive deeper into advanced topics that separate beginners from professionals in Data Scientist.\n\nDesign Patterns and Architecture:\n\nStudy common design patterns like Singleton, Factory, Observer, and Strategy. These aren\'t just academic concepts - they\'re proven solutions to recurring problems. Understanding when and how to apply them is crucial.\n\nLearn about architectural principles like SOLID, DRY (Don\'t Repeat Yourself), and KISS (Keep It Simple, Stupid). These principles guide you in writing maintainable, scalable code that other developers can understand and extend.\n\nUnderstand different architectural styles: MVC, microservices, serverless, event-driven architecture. Each has its use cases, advantages, and trade-offs. Know when to use which approach.\n\nPerformance and Optimization:\n\nLearn about performance optimization techniques. Understand time and space complexity (Big O notation). Profile your applications to identify bottlenecks. Remember: premature optimization is the root of all evil, but knowing how to optimize when needed is essential.\n\nStudy caching strategies, database indexing, and query optimization. Many performance issues stem from inefficient database operations. Learn to write efficient queries and use appropriate indexes.\n\nTesting and Quality:\n\nMaster testing methodologies: unit tests, integration tests, end-to-end tests. Write tests before or alongside your code (TDD/BDD). Tests are documentation that never goes out of date and give you confidence to refactor.\n\nUnderstand debugging techniques. Learn to use debuggers effectively, read stack traces, and systematically isolate issues. Good debugging skills save countless hours.\n\nContinuous Learning:\n\nRead source code of popular libraries and frameworks. This exposes you to professional coding standards and advanced techniques. Don\'t just use libraries - understand how they work internally.\n\nAttend webinars, watch conference talks, and follow industry experts. Technology evolves rapidly - staying current is part of the job. Subscribe to newsletters, podcasts, and blogs in your field.', '[\"https:\\/\\/refactoring.guru\\/design-patterns\",\"https:\\/\\/www.patterns.dev\\/\",\"https:\\/\\/martinfowler.com\\/\"]', '[\"Refactor an existing project using at least 3 design patterns\",\"Write comprehensive unit tests achieving 80%+ code coverage\",\"Optimize a slow application and document the improvements with benchmarks\"]', 0),
(16, 290, 2, 'Production-Ready Applications', 'Professional developers build applications that are maintainable, scalable, and production-ready. This lesson covers what it takes to deploy and maintain real-world applications.\n\nCI/CD and DevOps:\n\nLearn about Continuous Integration and Continuous Deployment. Set up automated pipelines that run tests, check code quality, and deploy automatically. Tools like GitHub Actions, Jenkins, or GitLab CI make this accessible.\n\nUnderstand containerization with Docker. Containers ensure your application runs consistently across different environments. Learn to write Dockerfiles and use docker-compose for multi-container applications.\n\nExplore orchestration with Kubernetes if working with microservices. While complex, Kubernetes is industry-standard for managing containerized applications at scale.\n\nCloud Platforms:\n\nStudy major cloud platforms: AWS, Azure, or Google Cloud. You don\'t need to master all services, but understand core offerings: compute (EC2, Lambda), storage (S3), databases (RDS), and networking (VPC).\n\nLearn Infrastructure as Code (IaC) using tools like Terraform or CloudFormation. Managing infrastructure through code makes it reproducible, version-controlled, and easier to maintain.\n\nMonitoring and Observability:\n\nImplement logging using structured logging libraries. Good logs are invaluable for debugging production issues. Log meaningful information but avoid logging sensitive data.\n\nSet up error tracking with tools like Sentry or Rollbar. Know when things break in production before users complain. Configure alerts for critical errors.\n\nImplement performance monitoring and APM (Application Performance Monitoring). Tools like New Relic or DataDog help identify performance bottlenecks in production.\n\nSecurity Best Practices:\n\nUnderstand common security vulnerabilities (OWASP Top 10): SQL injection, XSS, CSRF, etc. Learn how to prevent them. Security isn\'t optional - it\'s fundamental.\n\nImplement proper authentication and authorization. Use established libraries and frameworks rather than rolling your own. Understand OAuth, JWT, and session management.\n\nPractice defense in depth: validate all inputs, sanitize outputs, use HTTPS, keep dependencies updated, and follow the principle of least privilege.', '[\"https:\\/\\/12factor.net\\/\",\"https:\\/\\/aws.amazon.com\\/getting-started\\/\",\"https:\\/\\/owasp.org\\/www-project-top-ten\\/\"]', '[\"Deploy an application to a cloud platform with proper CI\\/CD pipeline\",\"Set up monitoring, logging, and alerting for a production application\",\"Implement authentication and authorization with proper security measures\"]', 0),
(17, 291, 1, 'Building Your Portfolio', 'Your portfolio is your professional showcase - it\'s often more important than your resume for technical roles.\n\nCreating Your Portfolio Website:\n\nBuild a personal website that highlights your projects, skills, and achievements. Keep it simple, fast, and mobile-responsive. Your portfolio itself demonstrates your technical skills.\n\nInclude an \'About Me\' section that tells your story. Why are you transitioning to Data Scientist? What drives you? Make it personal and authentic.\n\nShowcase 3-5 of your best projects. Quality over quantity - it\'s better to have three polished projects than ten half-finished ones.\n\nProject Case Studies:\n\nFor each project, write a detailed case study explaining:\n- The problem you were solving\n- Your approach and technical decisions\n- Challenges faced and how you overcame them\n- The impact or results\n- Technologies used and why\n\nInclude screenshots, diagrams, and code snippets. Make it easy for recruiters to understand your work even if they\'re not technical.\n\nProvide links to live demos and GitHub repositories. Ensure your code is clean, well-documented, and includes a comprehensive README.\n\nGitHub Profile Optimization:\n\nYour GitHub profile is your technical resume. Ensure it\'s polished:\n- Complete profile with photo and bio\n- Pinned repositories showcasing your best work\n- Consistent commit history (shows you code regularly)\n- Well-documented repositories with clear README files\n- Meaningful commit messages\n\nContribute to open-source projects. Even small contributions (documentation, bug fixes) demonstrate collaboration skills and initiative.\n\nContent Creation:\n\nWrite technical blog posts about your learning journey. Share insights, tutorials, or solutions to problems you\'ve solved. This demonstrates communication skills and helps others.\n\nCreate video demos of your projects. A 2-3 minute walkthrough showing functionality and explaining technical decisions is powerful.\n\nBe active on LinkedIn. Share your projects, write posts about what you\'re learning, and engage with the community. Networking is crucial for career transitions.', '[\"https:\\/\\/github.com\\/topics\\/portfolio-website\",\"https:\\/\\/dev.to\\/\",\"https:\\/\\/www.linkedin.com\\/\"]', '[\"Create a professional portfolio website and deploy it\",\"Write 3 technical blog posts about your learning journey\",\"Record a 5-minute video demo of your best project\"]', 0),
(18, 291, 2, 'Interview Preparation', 'Preparing systematically for technical interviews is crucial for successfully transitioning to Data Scientist.\n\nTechnical Interview Preparation:\n\nPractice coding challenges daily on platforms like LeetCode, HackerRank, or CodeSignal. Start with easy problems and gradually increase difficulty. Aim to solve at least 100-150 problems.\n\nFocus on data structures and algorithms: arrays, linked lists, trees, graphs, sorting, searching, dynamic programming. These form the foundation of technical interviews.\n\nUnderstand time and space complexity (Big O notation). You\'ll be asked to analyze the efficiency of your solutions. Practice explaining your thought process clearly.\n\nSystem Design Interviews:\n\nFor senior roles, study system design. Learn to design scalable systems: load balancers, caching, databases, microservices, message queues.\n\nPractice explaining trade-offs. There\'s rarely one \'correct\' answer in system design - it\'s about understanding pros and cons of different approaches.\n\nStudy real-world architectures: how does Twitter handle millions of tweets? How does Netflix stream video globally? Learn from these examples.\n\nBehavioral Interviews:\n\nPrepare stories using the STAR method (Situation, Task, Action, Result). Have examples ready for:\n- Challenging projects you\'ve worked on\n- Times you\'ve failed and what you learned\n- Conflicts with team members and how you resolved them\n- Leadership and initiative\n\nBe honest about your career transition. Frame it positively - you\'re not running from something, you\'re running toward something. Explain what excites you about the new role.\n\nJob Search Strategy:\n\nResearch companies thoroughly before applying. Tailor your resume and cover letter for each position. Generic applications rarely succeed.\n\nNetwork actively. Many jobs are filled through referrals before they\'re even posted. Attend meetups, conferences, and online events. Connect with people in your target role.\n\nPrepare thoughtful questions to ask interviewers. This shows genuine interest and helps you evaluate if the company is right for you.\n\nMock Interviews:\n\nPractice mock interviews with peers or use platforms like Pramp. Getting comfortable with the interview format is crucial.\n\nRecord yourself explaining technical concepts. Watch the recordings to improve your communication.\n\nStay Positive:\n\nRejections are part of the process. Each interview is practice for the next one. Learn from feedback and keep improving.\n\nKeep track of applications in a spreadsheet. Follow up professionally after interviews. Persistence pays off.', '[\"https:\\/\\/leetcode.com\\/\",\"https:\\/\\/www.pramp.com\\/\",\"https:\\/\\/www.glassdoor.com\\/Interview\\/\"]', '[\"Solve 50 coding problems on LeetCode (mix of easy, medium, hard)\",\"Complete 5 mock interviews with peers or online platforms\",\"Apply to 20 relevant job positions with tailored resumes\"]', 0);

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
(283, 171, 1, 'JavaScript Fundamentals for Full Stack Development', 'In this module, you\'ll learn the essentials of JavaScript, which is crucial for working with frameworks like React and Node.js.', 2, NULL),
(284, 171, 2, 'Node.js and Backend Development', 'In this module, you will learn how to set up a basic server using Node.js and work with APIs.', 2, NULL),
(285, 171, 3, 'Building Frontend Applications with React', 'In this module, you will learn the fundamentals of React, a popular JavaScript library for building user interfaces.', 2, NULL),
(286, 172, 1, 'Foundation Skills for Python Developer', 'Master the fundamental concepts required for transitioning to Python Developer', 4, NULL),
(287, 172, 2, 'Advanced Techniques', 'Deepen your expertise with advanced concepts', 4, NULL),
(288, 172, 3, 'Career Preparation', 'Prepare for job interviews and build portfolio', 4, NULL),
(289, 173, 1, 'Foundation Skills for Data Scientist', 'Master the fundamental concepts required for transitioning to Data Scientist', 4, NULL),
(290, 173, 2, 'Advanced Techniques', 'Deepen your expertise with advanced concepts', 4, NULL),
(291, 173, 3, 'Career Preparation', 'Prepare for job interviews and build portfolio', 4, NULL);

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
(1, 171, 'Module 1 - Lesson 1', 'Complete lesson', 10, 1, 1, 1, 1, '2026-02-26 10:37:45'),
(2, 171, 'Module 1 - Lesson 2', 'Complete lesson', 10, 2, 1, 2, 0, NULL),
(3, 171, 'Module 2 - Lesson 1', 'Complete lesson', 10, 3, 2, 1, 0, NULL),
(4, 171, 'Module 2 - Lesson 2', 'Complete lesson', 10, 4, 2, 2, 0, NULL),
(5, 171, 'Module 3 - Lesson 1', 'Complete lesson', 10, 5, 3, 1, 0, NULL),
(6, 171, 'Module 3 - Lesson 2', 'Complete lesson', 10, 6, 3, 2, 0, NULL),
(7, 172, 'Module 1 - Understanding Core Concepts', 'Complete lesson: Understanding Core Concepts', 10, 1, 1, 1, 0, NULL),
(8, 172, 'Module 1 - Hands-on Practice', 'Complete lesson: Hands-on Practice', 10, 2, 1, 2, 0, NULL),
(9, 172, 'Module 2 - Advanced Technical Skills', 'Complete lesson: Advanced Technical Skills', 10, 3, 2, 1, 0, NULL),
(10, 172, 'Module 2 - Production-Ready Applications', 'Complete lesson: Production-Ready Applications', 10, 4, 2, 2, 0, NULL),
(11, 172, 'Module 3 - Building Your Portfolio', 'Complete lesson: Building Your Portfolio', 10, 5, 3, 1, 0, NULL),
(12, 172, 'Module 3 - Interview Preparation', 'Complete lesson: Interview Preparation', 10, 6, 3, 2, 0, NULL),
(13, 173, 'Module 1 - Understanding Core Concepts', 'Complete lesson: Understanding Core Concepts', 10, 1, 1, 1, 0, NULL),
(14, 173, 'Module 1 - Hands-on Practice', 'Complete lesson: Hands-on Practice', 10, 2, 1, 2, 0, NULL),
(15, 173, 'Module 2 - Advanced Technical Skills', 'Complete lesson: Advanced Technical Skills', 10, 3, 2, 1, 0, NULL),
(16, 173, 'Module 2 - Production-Ready Applications', 'Complete lesson: Production-Ready Applications', 10, 4, 2, 2, 0, NULL),
(17, 173, 'Module 3 - Building Your Portfolio', 'Complete lesson: Building Your Portfolio', 10, 5, 3, 1, 0, NULL),
(18, 173, 'Module 3 - Interview Preparation', 'Complete lesson: Interview Preparation', 10, 6, 3, 2, 0, NULL);

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
(1, 47, 'B.Sc.', 'Computer Science', 'Prajyoti Niketan College, Pudukad, Thrissur', '2012', '2015', '', '2026-02-23 01:24:40', '2026-02-23 01:24:40'),
(2, 68, 'B.Sc.', 'Computer Science', 'Prajyoti Niketan College, Pudukad, Thrissur', '2012', '2015', '', '2026-02-28 01:25:44', '2026-02-28 01:25:44'),
(3, 68, 'MBA', 'Marketing', 'Adi Shankara Institute of Engineering and Technology, Kalady', '2015', '2017', '', '2026-02-28 01:26:38', '2026-02-28 01:26:38');

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
(56, 46, 1, 'PHP Developer', NULL, 'TechNova Solutions', 'Bangalore', 'We are looking for a PHP Developer to build and maintain web applications.', 'Core PHP, MySQL, HTML, CSS, JavaScript', '2-3 years', 3, 85, 'OPTIONAL', 'open', '2026-02-21 15:35:28', 'Full-time'),
(57, 46, 1, 'Mechanical Engineer', 'Engineering', 'TechNova Solutions', 'Coimbatore, India', 'Responsible for maintaining and improving manufacturing machinery', 'AutoCAD, SolidWorks, Machine Maintenance', '1-3 Years', 2, 80, 'REQUIRED_HARD', 'open', '2026-02-21 16:07:59', 'Full-time'),
(58, 48, 26, 'Quality Inspector', 'Manufacturing', 'GreenLeaf Industries', 'Pune, India', 'Ensure product quality and compliance with standards.', 'Quality Control, Inspection, ISO Standards', '2-5 Years', 1, 70, 'REQUIRED_HARD', 'open', '2026-02-21 16:15:54', 'Full-time'),
(59, 46, 1, 'job2', 'Manufacturing', 'TechNova Solutions', 'Thrissur, Kerala', 'aaaaa', 'tool n die', '2-3 years', 3, 45, 'REQUIRED_HARD', 'open', '2026-02-24 14:07:09', 'Full-time'),
(60, 46, 1, 'job3', 'information tachnology', 'TechNova Solutions', 'Bangalore, Karnataka', 'aaaaa', '', '', 3, 80, 'REQUIRED_HARD', 'open', '2026-02-24 14:14:47', 'Full-time'),
(61, 46, 1, 'job 4', 'IT', 'TechNova Solutions', 'BANGALORE', 'aaa   .......', '', '', 2, 0, 'OFF', 'closed', '2026-02-26 12:17:54', 'Full-time'),
(62, 48, 26, 'PHP Developer', 'Software Developemnet', 'GreenLeaf Industries', 'Bangalore', 'aaaaaaaa', 'Core Php', '', 4, 0, 'OFF', 'open', '2026-02-27 12:25:16', 'Full-time');

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

--
-- Dumping data for table `job_alerts`
--

INSERT INTO `job_alerts` (`id`, `candidate_id`, `role_keywords`, `location_keywords`, `skills_keywords`, `salary_min`, `salary_max`, `notify_email`, `notify_in_app`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 47, 'PHP developer', 'Bangalore', 'Core PHP', NULL, NULL, 1, 1, 1, '2026-02-27 06:46:41', '2026-02-27 06:46:41'),
(2, 67, 'PHP developer', 'Bangalore', 'Core PHP', NULL, NULL, 1, 1, 1, '2026-02-27 06:51:49', '2026-02-27 06:51:49');

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

--
-- Dumping data for table `job_alert_deliveries`
--

INSERT INTO `job_alert_deliveries` (`id`, `job_alert_id`, `job_id`, `candidate_id`, `email_sent_at`, `in_app_sent_at`, `created_at`) VALUES
(1, 1, 62, 47, '2026-02-27 06:55:22', '2026-02-27 06:55:16', '2026-02-27 06:55:16'),
(2, 2, 62, 67, '2026-02-27 06:55:28', '2026-02-27 06:55:22', '2026-02-27 06:55:22');

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
(14, '2026-02-23-150000', 'App\\Database\\Migrations\\AddGoogleIdToUsers', 'default', 'App', 1771844544, 11),
(15, '2026-02-25-100000', 'App\\Database\\Migrations\\DropUnusedPhoneOtpColumnsFromUsers', 'default', 'App', 1772016233, 12),
(16, '2026-02-25-110000', 'App\\Database\\Migrations\\CreateRecruiterCandidateNotesTable', 'default', 'App', 1772022056, 13),
(17, '2026-02-27-120000', 'App\\Database\\Migrations\\CreateJobAlertsTables', 'default', 'App', 1772174422, 14),
(18, '2026-02-28-120000', 'App\\Database\\Migrations\\CreateCandidateResumeVersionsTable', 'default', 'App', 1772258325, 15),
(19, '2026-02-28-140000', 'App\\Database\\Migrations\\CreateCandidateProjectsTable', 'default', 'App', 1772263750, 16);

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
(12, 47, 75, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-24 08:28:08', NULL),
(13, 49, 76, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-25 11:44:47', NULL),
(14, 47, 74, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-26 04:40:32', NULL),
(15, 66, NULL, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-27 06:17:47', NULL),
(16, 47, NULL, '', 'Job Alert Match', 'New matching job: PHP Developer - Bangalore', 'http://localhost/ai-job-portal/public/job/62', 0, '2026-02-27 06:55:16', NULL),
(17, 67, NULL, '', 'Job Alert Match', 'New matching job: PHP Developer - Bangalore', 'http://localhost/ai-job-portal/public/job/62', 1, '2026-02-27 06:55:22', '2026-02-27 06:56:06'),
(18, 47, 75, '', 'Profile Viewed', 'Rohith Kumar viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-28 06:05:20', NULL),
(19, 47, 75, '', 'Resume Downloaded', 'Rohith Kumar downloaded your resume.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-28 06:05:58', NULL),
(20, 47, 79, '', 'Profile Viewed', 'Asha Govind viewed your profile.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-28 06:21:52', NULL),
(21, 47, 79, '', 'Resume Downloaded', 'Asha Govind downloaded your resume.', 'http://localhost/ai-job-portal/public/candidate/applications', 0, '2026-02-28 06:21:58', NULL);

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
(18, 47, 46, 75, 57, 'profile_viewed', '2026-02-24 08:28:08'),
(19, 49, 46, 76, 56, 'profile_viewed', '2026-02-25 11:44:47'),
(20, 47, 46, 74, 56, 'profile_viewed', '2026-02-26 04:40:32'),
(21, 66, 46, NULL, NULL, 'profile_viewed', '2026-02-27 06:17:46'),
(22, 47, 46, 75, 57, 'profile_viewed', '2026-02-28 06:05:20'),
(23, 47, 46, 75, 57, 'resume_downloaded', '2026-02-28 06:05:58'),
(24, 47, 48, 79, 62, 'profile_viewed', '2026-02-28 06:21:52'),
(25, 47, 48, 79, 62, 'resume_downloaded', '2026-02-28 06:21:58');

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
(1, 49, 46, 'Strong communication', 'can be interviewed', '2026-02-25 12:22:26', '2026-02-25 12:22:26'),
(2, 47, 48, 'Immediate joiner', '', '2026-02-28 06:40:41', '2026-02-28 06:40:41');

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
(5, 75, 'Shortlisted (Recruiter Override)', '2026-02-23 12:21:42', '2026-02-26 09:20:02'),
(6, 75, 'Rejected (Recruiter Override)', '2026-02-26 09:20:02', NULL),
(7, 77, 'Applied', '2026-02-26 09:30:54', NULL),
(8, 78, 'Applied', '2026-02-27 07:43:26', '2026-02-27 07:43:26'),
(9, 78, 'Shortlisted (AI Policy OFF)', '2026-02-27 07:43:26', NULL),
(10, 79, 'Applied', '2026-02-28 06:18:56', '2026-02-28 06:18:56'),
(11, 79, 'Shortlisted (AI Policy OFF)', '2026-02-28 06:18:56', NULL);

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

INSERT INTO `users` (`id`, `name`, `email`, `google_id`, `preferred_language`, `phone`, `role`, `company_name`, `company_id`, `email_verification_token`, `email_verified_at`, `phone_verified_at`, `password`, `created_at`, `resume_path`, `profile_photo`, `location`, `bio`) VALUES
(46, 'Rohith Kumar', 'rohith@technova.com', NULL, 'en', '+919544104305', 'recruiter', 'TechNova Solutions', 1, NULL, NULL, '2026-02-21 09:55:45', '$2y$10$tE9gbIZsmrhYj3JT0GUUr.Ra9rusYsib3.sglY1aTUA/80Qe4ENwi', '2026-02-21 15:25:05', NULL, NULL, NULL, NULL),
(47, 'Manju Aravind', 'manju@gmail.com', NULL, 'en', '1234567890', 'candidate', NULL, NULL, NULL, NULL, NULL, '$2y$10$NyfTmre9jA2XQ7YQyaA5dOFUht/7z2AYgaVvOQn0O/JnFz.oULaTe', '2026-02-21 15:39:46', 'uploads/resumes/Rinu_George_Resume_14.pdf', 'uploads/profiles/47_1771671062.jpg', 'BANGALORE', ''),
(48, 'Asha Govind', 'asha@greenleaf.com', NULL, 'en', '+919544104305', 'recruiter', 'GreenLeaf Industries', 26, NULL, NULL, '2026-02-21 10:40:38', '$2y$10$vnhHNHOsFGO3BoRU8ORdkO7scVwwPBJZWKAXD7907xXQsYdujrAfe', '2026-02-21 16:10:17', NULL, NULL, NULL, NULL),
(49, 'rinu george', 'rinugeorge@gmail.com', '110489513847967949727', 'en', '09747751235', 'candidate', NULL, NULL, NULL, NULL, NULL, '$2y$10$aX/2kGvTsL77jRiGRl9c9uF3csUzIulWshhaTrNuGDzASo4EJtY1q', '2026-02-23 16:58:13', 'uploads/resumes/Rinu_George_Resume_15.pdf', '', 'BANGALORE', ''),
(60, 'rinu george', 'rinu@sandslab.com', NULL, 'en', '+919544104305', 'recruiter', 'SANDS Lab', 28, NULL, NULL, '2026-02-25 11:01:11', '$2y$10$AYu/RrcrjVoevRK19XM/PuCnSi2qjah40.HOoHEqkE08n5ULW4bza', '2026-02-25 16:30:57', NULL, NULL, NULL, NULL),
(62, 'rinu george', 'rinu@ser.com', NULL, 'en', '+919544104305', 'recruiter', 'SERP Hawk', 27, NULL, NULL, '2026-02-25 11:43:19', '$2y$10$NQlgJesfIGrMFNJO1FPm5.Bi7Fr4Rn7od7csn/jZyGPecBxED1wla', '2026-02-25 17:13:10', NULL, NULL, NULL, NULL),
(64, 'John', 'john@xxx.com', NULL, 'en', '+919544104305', 'recruiter', 'xxx', 29, NULL, NULL, '2026-02-26 05:51:30', '$2y$10$jjsjSTk6Td8eBNZpW/HzwO7eDIjwTyFNLoDnlj.vDjZJC7jMN9ImO', '2026-02-26 11:21:16', NULL, NULL, NULL, NULL),
(65, 'kiran', 'kiran@bbb.com', NULL, 'en', '+919544104305', 'recruiter', 'bbb', 30, NULL, NULL, '2026-02-26 07:37:39', '$2y$10$IgxgjmtIcQzz/vj7u2c2Buet0SLEdbquuEP1huQaSVNUn6n4IWRoa', '2026-02-26 13:07:06', NULL, NULL, NULL, NULL),
(66, 'Rajeev', 'rajeev@gmail.com', NULL, 'en', '1472586900', 'candidate', NULL, NULL, NULL, NULL, NULL, '$2y$10$oWudCosjq5bAfx3P7kipMuJ.xcaiojdg42fL0801NEjGs2ZKAIBjC', '2026-02-26 14:55:26', 'uploads/resumes/Rinu-George-Resume.pdf', NULL, NULL, NULL),
(67, 'rinu george', 'rinugeorgep@gmail.com', NULL, 'en', '09747751235', 'candidate', NULL, NULL, NULL, NULL, NULL, '$2y$10$J5gbpKMdHbai.aUCaGh0VeJd5jt6GRkEQmL8bhEUQ06pAH4xWSGYC', '2026-02-27 12:19:18', 'uploads/resumes/Rinu-George-Resume_1.pdf', '', '', ''),
(68, 'Praveen ', 'praveen@gmail.com', NULL, 'en', '3692581470', 'candidate', NULL, NULL, NULL, NULL, NULL, '$2y$10$ZmcxQa6iLxLXydNTcRYXJu7ryIIN3VU0nFmAZ/5oYfsfNmkd6aiN6', '2026-02-28 12:21:19', NULL, NULL, NULL, NULL),
(72, 'rinu george', 'rinu@serphawk.com', NULL, 'en', '+919544104305', 'recruiter', 'SERP Hawk', 27, 'e6ac510f3b782c5c797ff2b711a71b9a1737e47e92a8a3eff538349d83a119cc', NULL, '2026-02-28 10:36:20', '$2y$10$DcTHfzm4RHDARH6vWe/aZeFzT9eC9G52zYhNGZ7IqVhxy4Sm8u6GW', '2026-02-28 16:06:06', NULL, NULL, NULL, NULL);

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
(2, 49, 'Web Developer', 'KJP Digital Solutions Pvt Ltd', 'Full-time', 'Thrissur, Kerala', '2023-06-26', '2024-06-28', 0, '', '2026-02-24 05:09:45', '2026-02-24 05:09:45'),
(3, 47, 'Web Developer', 'ABC company', 'Full-time', '', '2025-12-08', NULL, 1, '', '2026-02-26 00:48:23', '2026-02-26 00:48:23'),
(4, 68, 'Software Developer', 'SANDS Lab', 'Full-time', '', '2026-02-02', '2021-12-26', 0, '', '2026-02-28 01:23:19', '2026-02-28 01:23:19'),
(5, 68, 'Web Developer', 'KJP Digital Solutions Pvt Ltd', 'Full-time', '', '2023-06-26', NULL, 1, '', '2026-02-28 01:25:11', '2026-02-28 01:25:11');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=80;

--
-- AUTO_INCREMENT for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `candidate_interests`
--
ALTER TABLE `candidate_interests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `candidate_projects`
--
ALTER TABLE `candidate_projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `candidate_resume_versions`
--
ALTER TABLE `candidate_resume_versions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `career_transitions`
--
ALTER TABLE `career_transitions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `certifications`
--
ALTER TABLE `certifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `companies`
--
ALTER TABLE `companies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `course_lessons`
--
ALTER TABLE `course_lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `course_modules`
--
ALTER TABLE `course_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=292;

--
-- AUTO_INCREMENT for table `daily_tasks`
--
ALTER TABLE `daily_tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `education`
--
ALTER TABLE `education`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `job_alerts`
--
ALTER TABLE `job_alerts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_alert_deliveries`
--
ALTER TABLE `job_alert_deliveries`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `job_suggestions`
--
ALTER TABLE `job_suggestions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `recruiter_candidate_actions`
--
ALTER TABLE `recruiter_candidate_actions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `recruiter_candidate_messages`
--
ALTER TABLE `recruiter_candidate_messages`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `recruiter_candidate_notes`
--
ALTER TABLE `recruiter_candidate_notes`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `work_experiences`
--
ALTER TABLE `work_experiences`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
