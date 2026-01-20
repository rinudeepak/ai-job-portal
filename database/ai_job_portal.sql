-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 01:38 PM
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
-- Table structure for table `ai_interviews`
--

CREATE TABLE `ai_interviews` (
  `id` int(10) UNSIGNED NOT NULL,
  `candidate_id` int(10) UNSIGNED NOT NULL,
  `job_id` int(10) UNSIGNED DEFAULT NULL,
  `skills_tested` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `github_languages` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`github_languages`)),
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`questions`)),
  `answers` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`answers`)),
  `technical_score` decimal(5,2) DEFAULT NULL,
  `communication_score` decimal(5,2) DEFAULT NULL,
  `overall_rating` decimal(5,2) DEFAULT NULL,
  `ai_decision` enum('pending','qualified','rejected') NOT NULL DEFAULT 'pending',
  `ai_feedback` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`ai_feedback`)),
  `status` enum('created','in_progress','completed') NOT NULL DEFAULT 'created',
  `started_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `ai_interviews`
--

INSERT INTO `ai_interviews` (`id`, `candidate_id`, `job_id`, `skills_tested`, `github_languages`, `questions`, `answers`, `technical_score`, `communication_score`, `overall_rating`, `ai_decision`, `ai_feedback`, `status`, `started_at`, `completed_at`, `created_at`, `updated_at`) VALUES
(1, 7, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":2,\"topic\":\"EJS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with EJS and provide a practical example.\",\"expected_keywords\":[\"EJS\"],\"points\":10},{\"id\":3,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":4,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10},{\"id\":5,\"topic\":\"Vue\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with Vue and provide a practical example.\",\"expected_keywords\":[\"Vue\"],\"points\":10}]', '[]', NULL, NULL, NULL, 'pending', NULL, 'in_progress', '2026-01-20 12:26:30', NULL, '2026-01-20 12:26:30', '2026-01-20 12:26:30'),
(2, 7, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":2,\"topic\":\"EJS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with EJS and provide a practical example.\",\"expected_keywords\":[\"EJS\"],\"points\":10},{\"id\":3,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":4,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10},{\"id\":5,\"topic\":\"Vue\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with Vue and provide a practical example.\",\"expected_keywords\":[\"Vue\"],\"points\":10}]', '[]', NULL, NULL, NULL, 'pending', NULL, 'in_progress', '2026-01-20 12:27:31', NULL, '2026-01-20 12:27:31', '2026-01-20 12:27:31');

-- --------------------------------------------------------

--
-- Table structure for table `applications`
--

CREATE TABLE `applications` (
  `id` int(11) NOT NULL,
  `candidate_id` int(11) DEFAULT NULL,
  `job_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `applications`
--

INSERT INTO `applications` (`id`, `candidate_id`, `job_id`, `status`, `applied_at`) VALUES
(1, 2, 4, 'applied', '2026-01-17 06:00:27'),
(2, 7, 3, 'applied', '2026-01-20 05:36:47');

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
(1, 8, 'rinudeepak', 3, 0, 'PHP,JavaScript,SCSS,CSS,HTML,Hack', 0, '2026-01-19 13:06:11'),
(8, 7, 'younisyousaf', 31, 278, 'JavaScript,EJS,CSS,HTML,Vue,PHP,Blade,TypeScript,C#,Dart,C++,CMake,Swift,C,Dockerfile,Shell,Kotlin,Objective-C,Python,SCSS', 10, '2026-01-20 07:19:23');

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
(4, 2, 'PHP', '2026-01-19 09:20:47'),
(5, 2, 'MySQL', '2026-01-19 09:20:47'),
(6, 2, 'jQuery', '2026-01-19 09:20:47'),
(7, 2, 'WordPress', '2026-01-19 09:20:47'),
(8, 2, 'Building dynamic', '2026-01-19 09:20:47'),
(44, 8, 'JavaScript', '2026-01-19 10:26:05'),
(45, 8, 'HTML', '2026-01-19 10:26:05'),
(46, 8, 'CSS', '2026-01-19 10:26:05'),
(47, 8, 'MongoDB', '2026-01-19 10:26:05'),
(48, 8, 'React', '2026-01-19 10:26:05'),
(49, 8, 'Node.js', '2026-01-19 10:26:05'),
(50, 8, 'Express.js', '2026-01-19 10:26:05'),
(51, 8, 'PHP', '2026-01-19 10:26:50'),
(52, 8, 'MySQL', '2026-01-19 10:26:50'),
(53, 8, 'jQuery', '2026-01-19 10:26:50'),
(54, 8, 'WordPress', '2026-01-19 10:26:50'),
(55, 8, 'JavaScript', '2026-01-19 10:28:54'),
(56, 8, 'HTML', '2026-01-19 10:28:54'),
(57, 8, 'CSS', '2026-01-19 10:28:54'),
(58, 8, 'MongoDB', '2026-01-19 10:28:54'),
(59, 8, 'React', '2026-01-19 10:28:54'),
(60, 8, 'Node.js', '2026-01-19 10:28:54'),
(61, 8, 'Express.js', '2026-01-19 10:28:54'),
(62, 8, 'JavaScript', '2026-01-19 10:30:13'),
(63, 8, 'HTML', '2026-01-19 10:30:13'),
(64, 8, 'CSS', '2026-01-19 10:30:13'),
(65, 8, 'MongoDB', '2026-01-19 10:30:13'),
(66, 8, 'React', '2026-01-19 10:30:13'),
(67, 8, 'Node.js', '2026-01-19 10:30:13'),
(68, 8, 'Express.js', '2026-01-19 10:30:13'),
(69, 8, 'Git', '2026-01-19 10:30:13'),
(70, 8, 'JavaScript', '2026-01-19 10:32:05'),
(71, 8, 'HTML', '2026-01-19 10:32:05'),
(72, 8, 'CSS', '2026-01-19 10:32:05'),
(73, 8, 'MongoDB', '2026-01-19 10:32:05'),
(74, 8, 'React', '2026-01-19 10:32:05'),
(75, 8, 'Node.js', '2026-01-19 10:32:05'),
(76, 8, 'Express.js', '2026-01-19 10:32:05'),
(77, 8, 'Git', '2026-01-19 10:32:05'),
(78, 8, 'PHP', '2026-01-19 10:32:05'),
(79, 8, 'JavaScript', '2026-01-19 10:55:35'),
(80, 8, 'React', '2026-01-19 10:55:35'),
(81, 8, 'HTML', '2026-01-19 10:55:35'),
(82, 8, 'CSS', '2026-01-19 10:55:35'),
(83, 8, 'Node.js', '2026-01-19 10:55:35'),
(84, 8, 'Express.js', '2026-01-19 10:55:35'),
(85, 8, 'MongoDB', '2026-01-19 10:55:35'),
(86, 8, 'Git', '2026-01-19 10:55:35'),
(87, 8, 'PHP', '2026-01-19 10:55:35'),
(88, 8, 'JavaScript', '2026-01-19 10:58:49'),
(89, 8, 'React', '2026-01-19 10:58:49'),
(90, 8, 'HTML', '2026-01-19 10:58:49'),
(91, 8, 'CSS', '2026-01-19 10:58:49'),
(92, 8, 'Node.js', '2026-01-19 10:58:49'),
(93, 8, 'Express.js', '2026-01-19 10:58:49'),
(94, 8, 'MongoDB', '2026-01-19 10:58:49'),
(95, 8, 'PHP', '2026-01-19 10:58:49'),
(96, 7, 'JavaScript', '2026-01-20 04:56:12'),
(97, 7, 'React', '2026-01-20 04:56:12'),
(98, 7, 'HTML', '2026-01-20 04:56:12'),
(99, 7, 'CSS', '2026-01-20 04:56:12'),
(100, 7, 'Node.js', '2026-01-20 04:56:12'),
(101, 7, 'Express.js', '2026-01-20 04:56:12'),
(102, 7, 'MongoDB', '2026-01-20 04:56:12'),
(103, 7, 'Git', '2026-01-20 04:56:12'),
(104, 7, 'PHP', '2026-01-20 04:56:12');

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
  `min_ai_cutoff_score` int(11) DEFAULT 0,
  `status` enum('open','closed') DEFAULT 'open',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jobs`
--

INSERT INTO `jobs` (`id`, `recruiter_id`, `title`, `company`, `location`, `description`, `required_skills`, `experience_level`, `min_ai_cutoff_score`, `status`, `created_at`) VALUES
(2, 6, 'WordPress Developer', 'Creative Web Labs', 'Remote', 'Customize WordPress themes and plugins based on client requirements.', 'WordPress, PHP, WooCommerce, HTML, CSS', 'fresher', 50, 'open', '2026-01-16 14:26:56'),
(3, 6, 'Backend Developer', 'AI Tech Systems', 'Bangalore', 'Build scalable backend APIs and database-driven applications.', 'PHP, CodeIgniter, REST API, MySQL', 'mid', 70, 'open', '2026-01-16 14:26:56'),
(4, 6, 'Full Stack Developer', 'InnovateSoft Pvt Ltd', 'Chennai', 'Develop complete web solutions including frontend and backend.', 'PHP, Laravel, JavaScript, Bootstrap, MySQL', 'senior', 80, 'open', '2026-01-16 14:26:56'),
(5, 6, 'Junior Web Developer', 'Startup Hub', 'Trivandrum', 'Assist senior developers in building and testing web applications.', 'HTML, CSS, PHP Basics, MySQL', 'fresher', 40, 'closed', '2026-01-16 14:26:56'),
(8, 9, 'AI Interview Evaluator', 'BlueScape Labs', 'Hyderabad', 'Analyze candidate technical and communication performance.', 'AI, Python, NLP, Evaluation Logic', 'senior', NULL, 'open', '2026-01-17 15:01:18'),
(9, 6, 'MERN Stack Intern', 'CodeLabs', 'Bangalore', 'Assist in developing full-stack apps with MongoDB, Express, React, Node.js.', 'MongoDB, React, Node.js, REST APIs', 'fresher', 45, 'open', '2026-01-17 15:20:41');

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
(1, '2026-01-20-082658', 'App\\Database\\Migrations\\CreateAiInterviewsTable', 'default', 'App', 1768897866, 1);

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
(1, 'JavaScript', 'Programming', '[\"JS\", \"ES6\", \"ES2015\"]'),
(2, 'Python', 'Programming', '[\"Python3\", \"Py\"]'),
(3, 'React', 'Framework', '[\"ReactJS\", \"React.js\"]'),
(4, 'AWS', 'Cloud', '[\"Amazon Web Services\", \"EC2\", \"S3\"]'),
(5, 'PHP', 'Backend', '[\"Core PHP\"]'),
(6, 'MySQL', 'Database', '[]'),
(7, 'JavaScript', 'Frontend', '[\"JS\",\"ECMAScript\"]'),
(8, 'HTML', 'Frontend', '[]'),
(9, 'CSS', 'Frontend', '[]'),
(10, 'Bootstrap', 'Frontend', '[]'),
(11, 'jQuery', 'Frontend', '[]'),
(12, 'React', 'Frontend', '[\"ReactJS\"]'),
(13, 'Node.js', 'Backend', '[\"Node\"]'),
(14, 'Express.js', 'Backend', '[\"Express\"]'),
(15, 'MongoDB', 'Database', '[]'),
(16, 'REST API', 'Backend', '[\"API\",\"RESTful\"]'),
(17, 'WordPress', 'CMS', '[]'),
(18, 'CodeIgniter', 'Framework', '[\"CI\"]'),
(19, 'Laravel', 'Framework', '[]'),
(20, 'Git', 'Tool', '[\"Version Control\"]'),
(21, 'AWS', 'Cloud', '[\"Amazon Web Services\"]'),
(22, 'Docker', 'Tool', '[]'),
(23, 'Python', 'Backend', '[]'),
(24, 'Java', 'Backend', '[]'),
(25, 'Git', 'Version Control', NULL),
(26, 'Git', 'Version Control', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `role` enum('candidate','admin') DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `resume_path` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `resume_path`) VALUES
(1, 'Athira', 'athira@gmail.com', '$2y$10$9sDk93NqixUOU8zcF8FhWe1EerdPh1Ed.l3ABWTep1ou.Q1QgcojO', '', '2026-01-14 15:41:23', NULL),
(2, 'Rinu George', 'rinugeorgep@gmail.com', '$2y$10$L0eYVJokqrpjUe0KTOgO.e8dr/fWnsWA4SusuWLyN14SOG5ubk8mi', 'candidate', '2026-01-14 16:32:23', NULL),
(3, 'Rohith Kumar', 'rohith@gmail.com', '$2y$10$F1qVw6VSBW719lA80dzJaOLA6v5SFxhDkOqez6Pwl2OQPelsMnJaW', 'admin', '2026-01-14 16:41:21', NULL),
(4, 'Maya', 'maya@gmail.com', '$2y$10$QsJg7TbsNy0LxIxgbfp83eZ94I8g2rvsqP5WZ1MR7mSxqjONANSiW', 'admin', '2026-01-14 17:45:50', NULL),
(5, 'Candidate1', 'candidate1@gmail.com', '$2y$10$qJgKEvhl7b4pIwBXi/wRbOos/4uXYKx.HeyoZ.OCjzrQL.dfkGW1m', 'candidate', '2026-01-16 11:07:09', NULL),
(6, 'Rinu George', 'abc@gmail.com', '$2y$10$WbbioV.30t4atwrgbq7xweEOUdB2F1/sGIjX.XrBvakUqU0OubSWy', 'admin', '2026-01-16 11:18:30', NULL),
(7, 'sarayu', 'sarayu@gmail.com', '$2y$10$maFz4ZMZbvYjvCI7n1Rq6.bx2TcVvWQ3VQY3HhqYTop2fqaPjWBlq', 'candidate', '2026-01-16 11:27:28', 'uploads/resumes/mernstackdeveloper_10.docx'),
(8, 'Jyothi', 'jyothi@gmail.com', '$2y$10$Fy2SFIQi33Ui9MbR/AjnSuXUAuyngaH75dalNv1xjGRSvjjypT9Ue', 'candidate', '2026-01-16 11:29:38', NULL),
(9, 'bbbb', 'bb@gmail.com', '$2y$10$/ezgjOz8BzqNDaBzWjF.3ezb7AsTZDrwlHvBfmtgt9WzvTooAdteO', 'admin', '2026-01-16 11:35:50', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `ai_interviews`
--
ALTER TABLE `ai_interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `candidate_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`);

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
-- Indexes for table `skills`
--
ALTER TABLE `skills`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `ai_interviews`
--
ALTER TABLE `ai_interviews`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `skills`
--
ALTER TABLE `skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
