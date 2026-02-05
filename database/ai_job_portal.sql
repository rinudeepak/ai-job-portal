-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 05, 2026 at 09:50 AM
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
(27, 20, 13, 'applied', NULL, NULL, NULL, '2026-02-03 11:36:32'),
(28, 20, 11, 'interview_slot_booked', '2026-02-12 15:00:00', 1, 2, '2026-02-03 11:37:02'),
(29, 20, 15, 'applied', NULL, NULL, NULL, '2026-02-05 08:49:21');

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
(3, 20, 'younisyousaf', 31, 278, 'JavaScript,EJS,CSS,HTML,Vue,PHP,Blade,TypeScript,C#,Dart,C++,CMake,Swift,C,Dockerfile,Shell,Kotlin,Objective-C,Python,SCSS', 10, '2026-02-05 08:47:47');

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
(5, 20, 'React, Python, Selenium, JavaScript, HTML, CSS, MySQL, WordPress', '2026-02-05 08:46:28');

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

--
-- Dumping data for table `interview_bookings`
--

INSERT INTO `interview_bookings` (`id`, `application_id`, `user_id`, `job_id`, `slot_id`, `slot_datetime`, `booking_status`, `reschedule_count`, `max_reschedules`, `can_reschedule`, `booked_at`, `last_rescheduled_at`) VALUES
(2, 28, 20, 11, 2, '2026-02-12 15:00:00', 'rescheduled', 1, 2, 1, '2026-02-03 18:27:03', '2026-02-03 18:30:21');

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

--
-- Dumping data for table `interview_sessions`
--

INSERT INTO `interview_sessions` (`id`, `user_id`, `application_id`, `session_id`, `position`, `conversation_history`, `turn`, `max_turns`, `status`, `evaluation_data`, `technical_score`, `communication_score`, `problem_solving_score`, `adaptability_score`, `enthusiasm_score`, `overall_rating`, `ai_decision`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 20, 28, 'interview_69823add189972.87698220', 'PHP Developer', '[{\"role\":\"system\",\"content\":\"You are Sarah, an expert technical interviewer for a growing tech company.\\r\\nYou are conducting a first-round screening interview for the position: **PHP Developer**.\\r\\n\\r\\n**CANDIDATE\'S BACKGROUND:**\\r\\n- Resume Skills: \\r\\n- GitHub Languages: \\r\\n\\r\\n**YOUR INTERVIEW APPROACH:**\\r\\n\\r\\n1. **Introduction (Turn 1):**\\r\\n   - Introduce yourself warmly: \\\"Hi! I\'m Sarah, and I\'ll be conducting your technical interview today.\\\"\\r\\n   - Ask them to briefly introduce themselves and what excites them about this role.\\r\\n\\r\\n2. **Resume Deep-Dive (Turns 2-4):**\\r\\n   - Pick specific skills from their resume and ask about real experience\\r\\n   - If they mention a project, dig deeper: \\\"Tell me more about that project...\\\"\\r\\n   - Ask about challenges they faced and how they solved them\\r\\n   - Example: \\\"I see you have React on your resume. Can you walk me through a complex component you built?\\\"\\r\\n\\r\\n3. **GitHub Analysis (Turns 3-5):**\\r\\n   - Reference their GitHub languages naturally\\r\\n   - Example: \\\"I noticed you\'ve been working with Python. What\'s the most interesting thing you\'ve built with it?\\\"\\r\\n   - Ask about coding patterns, testing, or architecture decisions\\r\\n\\r\\n4. **Technical Probing (Turns 4-7):**\\r\\n   - Ask follow-up technical questions based on their answers\\r\\n   - If they give a shallow answer, probe deeper: \\\"That\'s interesting. Can you elaborate on how you handled [specific aspect]?\\\"\\r\\n   - If they seem uncertain, give hints: \\\"Let me rephrase - have you worked with [related concept]?\\\"\\r\\n   - If they answer well, ask a slightly harder question\\r\\n   - If they struggle, ask an easier question to build confidence\\r\\n\\r\\n5. **Behavioral & Communication (Throughout):**\\r\\n   - Assess clarity, confidence, and professionalism\\r\\n   - Notice if they explain things well\\r\\n   - Check if they admit when they don\'t know something (positive trait!)\\r\\n\\r\\n6. **Adaptive Questioning:**\\r\\n   - **If answer is WRONG\\/WEAK:** Don\'t immediately move on. Probe gently:\\r\\n     * \\\"Interesting perspective. Let me ask this differently...\\\"\\r\\n     * \\\"That\'s one approach. Have you considered [alternative]?\\\"\\r\\n     * \\\"I think there might be some confusion. Let me clarify...\\\"\\r\\n   - **If answer is STRONG:** Follow up with harder question:\\r\\n     * \\\"Great! Now, how would you handle [edge case]?\\\"\\r\\n     * \\\"Excellent. What if we had [constraint]?\\\"\\r\\n   - **If answer shows confusion:** Help them:\\r\\n     * \\\"No worries! Let me give you a hint...\\\"\\r\\n     * \\\"Think about it from [angle]...\\\"\\r\\n\\r\\n7. **Natural Conversation Flow:**\\r\\n   - Use transitions: \\\"That makes sense. Building on that...\\\"\\r\\n   - Show engagement: \\\"Interesting!\\\", \\\"I see what you mean.\\\"\\r\\n   - Be encouraging: \\\"Good thinking!\\\", \\\"That\'s a solid approach.\\\"\\r\\n   - Be empathetic: \\\"I know this can be challenging...\\\"\\r\\n\\r\\n8. **Closing (Turn 8-10):**\\r\\n   - Thank them for their time\\r\\n   - End with: \\\"That concludes our interview. Thank you for sharing your experience with me today. INTERVIEW_COMPLETE\\\"\\r\\n\\r\\n**IMPORTANT RULES:**\\r\\n- Ask exactly ONE clear question per turn. Never combine multiple questions in a single response.\\r\\n- Keep responses concise (2-3 sentences max)\\r\\n- Act like a human interviewer, not a robot\\r\\n- Don\'t list multiple questions in one turn\\r\\n- Adapt based on their answers - this is a conversation, not a quiz\\r\\n- If they struggle, help them; if they excel, challenge them\\r\\n- Maximum 10 turns total\\r\\n\\r\\n**EVALUATION CRITERIA (Track Mentally):**\\r\\n- Technical Knowledge (40%)\\r\\n- Problem-Solving Ability (30%)\\r\\n- Communication Skills (20%)\\r\\n- Cultural Fit & Enthusiasm (10%)\\r\\n\\r\\nBegin the interview now with your introduction.\"},{\"role\":\"assistant\",\"content\":\"Hi! I\'m Sarah, and I\'ll be conducting your technical interview today for the PHP Developer position. To start, could you briefly introduce yourself and tell me what excites you about this role?\"},{\"role\":\"user\",\"content\":\"My name is Rinu. I have experience in Core PHP and WordPress\"},{\"role\":\"assistant\",\"content\":\"Thanks for sharing that, Rinu! It\'s great to meet you.\\n\\nI see you have experience with Core PHP and WordPress. Could you walk me through a specific project where you used Core PHP to build something from scratch? What was the most challenging part?\"},{\"role\":\"user\",\"content\":\"i built using core php a e- commerce website for restaurant\"},{\"role\":\"assistant\",\"content\":\"That sounds like a great project! Could you tell me more about the architecture you used for this e-commerce website? For example, how did you structure your database and handle user sessions?\"},{\"role\":\"user\",\"content\":\"i used user sessions and cookies to use cart restaurant products\"},{\"role\":\"assistant\",\"content\":\"Got it! Using sessions and cookies for the cart is a solid approach. How did you handle security concerns, like preventing session hijacking or ensuring the integrity of the cart data?\"},{\"role\":\"user\",\"content\":\"no\"},{\"role\":\"assistant\",\"content\":\"No worries\\u2014security is a big topic! Let me ask this differently: Have you ever worked with PHP\\u2019s built-in functions like `session_regenerate_id()` or `hash_hmac()` to protect sessions or data? If not, how did you approach basic security for user logins?\"},{\"role\":\"user\",\"content\":\"no idea\"},{\"role\":\"assistant\",\"content\":\"That\'s okay\\u2014security can be tricky! Let me simplify it: When users logged into your restaurant e-commerce site, how did you store their passwords in the database? For example, did you use plain text, or did you hash them?\"},{\"role\":\"user\",\"content\":\"hash them\"},{\"role\":\"assistant\",\"content\":\"Good! That\\u2019s a secure practice. Which hashing algorithm did you use\\u2014was it something like `password_hash()` in PHP, or did you use a different method?\"},{\"role\":\"user\",\"content\":\"no\"},{\"role\":\"assistant\",\"content\":\"No problem! Let me ask a more foundational question to wrap up our discussion on security: Did you ever use any form of input validation (like checking if a user\'s email is properly formatted) before storing data in your database?\"},{\"role\":\"user\",\"content\":\"yes i used\"},{\"role\":\"assistant\",\"content\":\"Great! That\\u2019s an important step. Could you share an example of how you implemented input validation\\u2014for instance, did you use PHP\\u2019s `filter_var()` or custom regex patterns for validation?\"},{\"role\":\"user\",\"content\":\"regex pattern\"},{\"role\":\"assistant\",\"content\":\"Perfect\\u2014regex is a powerful tool for validation! That concludes our interview. Thank you for sharing your experience with me today.\\n\\n**INTERVIEW_COMPLETE**\"}]', 10, 10, 'evaluated', '{\"technical_score\":0,\"communication_score\":0,\"problem_solving_score\":0,\"adaptability_score\":0,\"enthusiasm_score\":0,\"overall_rating\":0,\"ai_decision\":\"rejected\",\"recommendation\":\"NO HIRE\",\"total_responses\":9,\"technical_feedback\":\"\",\"communication_feedback\":\"\",\"key_highlights\":[],\"concerns\":[],\"recommendations\":[],\"decision_reasoning\":\"\",\"next_steps\":\"\",\"notable_moments\":[],\"detailed_breakdown\":{\"technical\":[],\"problem_solving\":[],\"communication\":[],\"adaptability\":[],\"enthusiasm\":[]}}', 0.00, 0.00, 0.00, 0.00, 0.00, 89.00, 'shortlisted', '2026-02-03 18:13:49', '2026-02-03 18:18:23', '2026-02-03 18:18:52');

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
(2, 11, '2026-02-12', '15:00:00', '2026-02-12 15:00:00', 1, 1, 0, 21, '2026-02-03 18:30:03');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
  `recruiter_id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `company` varchar(250) NOT NULL,
  `location` varchar(250) NOT NULL,
  `description` text DEFAULT NULL,
  `required_skills` text DEFAULT NULL,
  `experience_level` enum('fresher','junior','mid','senior') DEFAULT NULL,
  `openings` int(11) DEFAULT 1,
  `min_ai_cutoff_score` int(11) DEFAULT 0,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp(),
  `employment_type` varchar(50) DEFAULT 'Full-time'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_id`, `title`, `company`, `location`, `description`, `required_skills`, `experience_level`, `openings`, `min_ai_cutoff_score`, `status`, `created_at`, `employment_type`) VALUES
(11, 21, 'PHP Developer', 'PHP Developer', 'Kochi, Kerala', 'Develop and maintain web applications using PHP and MySQL', 'PHP, MySQL, HTML, CSS, JavaScript', 'junior', 2, 65, 'open', '2026-02-03 16:33:05', 'Full-time'),
(13, 22, 'Laravel Developer', 'CodeCraft Technologies', 'Thrissur, Kerala', 'Work on REST APIs and backend modules using Laravel framework', 'Laravel, PHP, REST API, MySQL', 'mid', 1, 75, 'open', '2026-02-03 16:38:16', 'Full-time'),
(15, 22, 'Front End Developer', 'PixelSoft Pvt Ltd', 'Bangalore, Karnataka', 'Build responsive UI components using modern frontend technologies.', 'HTML, CSS, Bootstrap, JavaScript, React', 'mid', 3, 70, 'open', '2026-02-02 00:00:00', 'Full-time'),
(16, 21, 'Software Tester', 'Innova Systems', 'Chennai, Tamil Nadu', 'Test web applications and report bugs and performance issues.', 'Manual Testing, Selenium, Test Cases', 'junior', 2, 60, 'open', '2026-01-30 00:00:00', 'Full-time'),
(17, 22, 'Full Stack Developer', 'NextGen IT Services', 'Hyderabad, Telangana', 'Develop complete web solutions from frontend to backend.', 'PHP, Laravel, JavaScript, MySQL, API Integration', 'senior', 1, 80, 'open', '2026-02-03 00:00:00', 'Full-time'),
(28, 21, 'Java Developer', 'BlueSky Technologies', 'Pune, Maharashtra', 'Develop backend services and APIs using Java and Spring Boot.', 'Java, Spring Boot, REST API, MySQL', 'mid', 2, 72, 'open', '2026-02-03 00:00:00', 'Full-time'),
(29, 21, 'Node.js Developer', 'CloudNova Pvt Ltd', 'Noida, Uttar Pradesh', 'Build scalable APIs and microservices using Node.js.', 'Node.js, Express, MongoDB, REST API', 'junior', 3, 68, 'open', '2026-02-02 00:00:00', 'Full-time'),
(30, 22, 'UI/UX Designer', 'DesignHub Studio', 'Mumbai, Maharashtra', 'Design user-friendly interfaces and improve user experience.', 'Figma, Adobe XD, UI Design, UX Research', 'mid', 1, 60, 'open', '2026-02-01 00:00:00', 'Full-time'),
(31, 21, 'DevOps Engineer', 'InfraTech Solutions', 'Bangalore, Karnataka', 'Manage CI/CD pipelines and cloud infrastructure.', 'AWS, Docker, Kubernetes, Linux, CI/CD', 'senior', 1, 85, 'open', '2026-02-03 00:00:00', 'Full-time'),
(32, 22, 'Data Analyst', 'Insight Analytics', 'Kolkata, West Bengal', 'Analyze business data and prepare reports and dashboards.', 'Python, SQL, Power BI, Excel', 'junior', 2, 65, 'open', '2026-01-29 00:00:00', 'Full-time'),
(33, 22, 'Python Developer', 'AlgoSoft Systems', 'Trivandrum, Kerala', 'Develop backend logic and data processing modules.', 'Python, Django, REST API, PostgreSQL', 'mid', 2, 70, 'open', '2026-02-01 00:00:00', 'Full-time'),
(34, 22, 'React Developer', 'WebSpark IT Services', 'Ahmedabad, Gujarat', 'Build SPA applications using React.js.', 'React.js, JavaScript, HTML, CSS', 'junior', 2, 67, 'open', '2026-02-02 00:00:00', 'Full-time'),
(35, 21, 'HR Recruiter', 'TalentBridge HR', 'Coimbatore, Tamil Nadu', 'Handle end-to-end recruitment and candidate screening.', 'Recruitment, Communication, ATS Tools', 'junior', 1, 55, 'open', '2026-02-03 00:00:00', 'Full-time'),
(36, 21, 'QA Automation Engineer', 'TestPro Labs', 'Bangalore, Karnataka', 'Automate test cases and perform regression testing.', 'Selenium, Java, TestNG, Automation Testing', 'mid', 1, 75, 'open', '2026-02-01 00:00:00', 'Full-time'),
(37, 22, 'Business Analyst', 'MarketScope Consulting', 'Delhi, India', 'Gather requirements and coordinate between business and tech teams.', 'Requirement Analysis, SQL, Documentation, Communication', 'senior', 1, 78, 'open', '2026-02-02 00:00:00', 'Full-time');

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
(3, '2026-01-21-125510', 'App\\Database\\Migrations\\CreateInterviewSessionsTable', 'default', 'App', 1769000373, 3);

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
(1, 2, 1, 2, '2026-02-09 14:00:00', '2026-02-12 15:00:00', '', 'candidate', '2026-02-03 18:30:21');

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
(1, 27, 'Applied', '2026-02-03 11:36:32', NULL),
(2, 28, 'Applied', '2026-02-03 11:37:02', '2026-02-03 18:13:49'),
(3, 28, 'AI Interview Started', '2026-02-03 18:13:49', '2026-02-03 18:18:23'),
(4, 28, 'AI Interview Completed', '2026-02-03 18:18:23', '2026-02-03 18:18:52'),
(5, 28, 'AI Interview Evaluated', '2026-02-03 18:18:52', '2026-02-03 18:18:52'),
(6, 28, 'rejected', '2026-02-03 18:18:52', NULL),
(7, 29, 'Applied', '2026-02-05 08:49:21', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(15) DEFAULT NULL,
  `role` enum('candidate','recruiter','','') NOT NULL,
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

INSERT INTO `users` (`id`, `name`, `email`, `phone`, `role`, `password`, `created_at`, `resume_path`, `profile_photo`, `location`, `bio`) VALUES
(20, 'Rinu George', 'rinugeorgep@gmail.com', '09747751235', 'candidate', '$2y$10$Rr.HAa/OGmBZ8oEaZNbEN.pZFV1wvuIK2w7Rv9mkWA6goQ0yXwkem', '2026-02-03 16:20:20', 'uploads/resumes/Vinay_Resume_7.pdf', 'uploads/profiles/20_1770186261.jpg', 'Bangalore', 'Dedicated software Developer with over 3years of  experience in web development, specializing in PHP and  MySQL. Skilled in building dynamic, scalable applications  and solving complex technical problems. Currently seeking to restart my career with a focus on continuous learning, adapting to new technologies, and contributing to impactful projects'),
(21, 'recruiter1', 'recruiter1@gmail.com', NULL, 'recruiter', '$2y$10$yYQoY.Q5LO5Zt7Uppgole.0f8QRkECBzPxFCOwrSpHD9CLi20dxPC', '2026-02-03 16:23:07', NULL, NULL, '', NULL),
(22, 'recruiter2', 'recruiter2@gmail.com', NULL, 'recruiter', '$2y$10$e4aQ8RI/bTPancHiKcARG.9AWuCT6QoopPCn3YGIBhnb6tY2yUwAm', '2026-02-03 16:36:32', NULL, NULL, '', NULL);

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
-- Indexes for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`);

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
  ADD KEY `recruiter_id` (`recruiter_id`);

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
-- Indexes for table `reschedule_history`
--
ALTER TABLE `reschedule_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `interview_bookings`
--
ALTER TABLE `interview_bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `interview_slots`
--
ALTER TABLE `interview_slots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reschedule_history`
--
ALTER TABLE `reschedule_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `stage_history`
--
ALTER TABLE `stage_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

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
-- Constraints for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  ADD CONSTRAINT `candidate_skills_ibfk_1` FOREIGN KEY (`candidate_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `jobs`
--
ALTER TABLE `jobs`
  ADD CONSTRAINT `jobs_ibfk_1` FOREIGN KEY (`recruiter_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `stage_history`
--
ALTER TABLE `stage_history`
  ADD CONSTRAINT `stage_history_ibfk_1` FOREIGN KEY (`application_id`) REFERENCES `applications` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
