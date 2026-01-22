-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 22, 2026 at 02:20 PM
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
  `questions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
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
(2, 7, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":2,\"topic\":\"EJS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with EJS and provide a practical example.\",\"expected_keywords\":[\"EJS\"],\"points\":10},{\"id\":3,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":4,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10},{\"id\":5,\"topic\":\"Vue\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with Vue and provide a practical example.\",\"expected_keywords\":[\"Vue\"],\"points\":10}]', '[]', NULL, NULL, NULL, 'pending', NULL, 'in_progress', '2026-01-20 12:27:31', NULL, '2026-01-20 12:27:31', '2026-01-20 12:27:31'),
(3, 8, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"PHP\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with PHP and provide a practical example.\",\"expected_keywords\":[\"PHP\"],\"points\":10},{\"id\":2,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":3,\"topic\":\"SCSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with SCSS and provide a practical example.\",\"expected_keywords\":[\"SCSS\"],\"points\":10},{\"id\":4,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":5,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10}]', '[]', NULL, NULL, NULL, 'pending', NULL, 'in_progress', '2026-01-20 14:05:48', NULL, '2026-01-20 14:05:48', '2026-01-20 14:05:48'),
(4, 8, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"PHP\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with PHP and provide a practical example.\",\"expected_keywords\":[\"PHP\"],\"points\":10},{\"id\":2,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":3,\"topic\":\"SCSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with SCSS and provide a practical example.\",\"expected_keywords\":[\"SCSS\"],\"points\":10},{\"id\":4,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":5,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10}]', '[]', NULL, NULL, NULL, 'pending', NULL, 'in_progress', '2026-01-21 07:19:19', NULL, '2026-01-21 07:19:19', '2026-01-21 07:19:19'),
(5, 8, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"PHP\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with PHP and provide a practical example.\",\"expected_keywords\":[\"PHP\"],\"points\":10},{\"id\":2,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":3,\"topic\":\"SCSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with SCSS and provide a practical example.\",\"expected_keywords\":[\"SCSS\"],\"points\":10},{\"id\":4,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":5,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10}]', '[\"\",\"\",\"\",\"\",\"\"]', NULL, NULL, NULL, 'pending', NULL, 'completed', '2026-01-21 09:45:46', '2026-01-21 10:12:08', '2026-01-21 09:45:46', '2026-01-21 10:12:08'),
(6, 8, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"PHP\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with PHP and provide a practical example.\",\"expected_keywords\":[\"PHP\"],\"points\":10},{\"id\":2,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":3,\"topic\":\"SCSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with SCSS and provide a practical example.\",\"expected_keywords\":[\"SCSS\"],\"points\":10},{\"id\":4,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":5,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10}]', '[\"Yes i have experience in PHP\",\"No I don\'t have experience in javascript\",\"I dont have experince\",\"\",\"\",\"I have hands-on experience working with HTML for building structured and responsive web pages. I understand semantic HTML elements such as header, nav, section, article, and footer, which help improve accessibility and SEO.\\r\\n\\r\\nIn my projects, I have used HTML to create forms, tables, and reusable page layouts, and I often integrate it with CSS and JavaScript for styling and interactivity. I also follow best practices like proper form validation, clean markup, and accessibility-friendly labels.\\r\\n\\r\\nPractical example:\\r\\nIn one project, I converted static HTML designs into dynamic WordPress templates. I used HTML forms to collect user data such as name, email, and preferences, validated the inputs, and passed the data to the backend for processing. I also used semantic tags to structure the page, which improved readability and performance.\\r\\n\\r\\nOverall, I am comfortable translating UI designs into clean, maintainable HTML code and integrating it with backend systems.\",\"I have hands-on experience working with HTML for building structured and responsive web pages. I understand semantic HTML elements such as header, nav, section, article, and footer, which help improve accessibility and SEO.\\r\\n\\r\\nIn my projects, I have used HTML to create forms, tables, and reusable page layouts, and I often integrate it with CSS and JavaScript for styling and interactivity. I also follow best practices like proper form validation, clean markup, and accessibility-friendly labels.\\r\\n\\r\\nPractical example:\\r\\nIn one project, I converted static HTML designs into dynamic WordPress templates. I used HTML forms to collect user data such as name, email, and preferences, validated the inputs, and passed the data to the backend for processing. I also used semantic tags to structure the page, which improved readability and performance.\\r\\n\\r\\nOverall, I am comfortable translating UI designs into clean, maintainable HTML code and integrating it with backend systems.\"]', NULL, NULL, NULL, 'pending', NULL, 'completed', '2026-01-21 10:12:47', '2026-01-21 10:51:14', '2026-01-21 10:12:47', '2026-01-21 10:51:14'),
(7, 7, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":2,\"topic\":\"EJS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with EJS and provide a practical example.\",\"expected_keywords\":[\"EJS\"],\"points\":10},{\"id\":3,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":4,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10},{\"id\":5,\"topic\":\"Vue\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with Vue and provide a practical example.\",\"expected_keywords\":[\"Vue\"],\"points\":10}]', '[\"I have working knowledge of JavaScript and have used it mainly to add interactivity and improve user experience in web applications.\\r\\n\\r\\nIn my projects, I have used JavaScript for form validation, handling button clicks, dynamically showing or hiding elements, and making AJAX requests using fetch() to communicate with the backend.\\r\\n\\r\\nFor example, in an AI interview module I worked on, I used JavaScript to implement a countdown timer for each question. The timer updates every second, tracks the time taken by the candidate, and automatically submits the form when the time expires. I also used JavaScript to save draft answers asynchronously without refreshing the page.\\r\\n\\r\\nWhile I am not a JavaScript expert, I am comfortable understanding existing code, debugging issues, and quickly learning new concepts as needed.\",\"I have used EJS (Embedded JavaScript Templates) to create dynamic server-side rendered pages in Node.js applications, mainly with Express.js.\\r\\n\\r\\nMy experience includes:\\r\\n\\r\\nPassing data from controllers to views\\r\\n\\r\\nLooping through arrays and displaying lists\\r\\n\\r\\nConditional rendering (if\\/else)\\r\\n\\r\\nCreating reusable partials like headers and footers\\r\\n\\r\\nEJS helped me separate business logic from presentation and made pages dynamic without writing complex frontend frameworks.\",\"\",\"\",\"\"]', NULL, NULL, NULL, 'pending', NULL, 'completed', '2026-01-21 10:53:18', '2026-01-21 11:07:01', '2026-01-21 10:53:18', '2026-01-21 11:07:01'),
(8, 7, NULL, NULL, NULL, '[{\"id\":1,\"topic\":\"JavaScript\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with JavaScript and provide a practical example.\",\"expected_keywords\":[\"JavaScript\"],\"points\":10},{\"id\":2,\"topic\":\"EJS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with EJS and provide a practical example.\",\"expected_keywords\":[\"EJS\"],\"points\":10},{\"id\":3,\"topic\":\"CSS\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with CSS and provide a practical example.\",\"expected_keywords\":[\"CSS\"],\"points\":10},{\"id\":4,\"topic\":\"HTML\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with HTML and provide a practical example.\",\"expected_keywords\":[\"HTML\"],\"points\":10},{\"id\":5,\"topic\":\"Vue\",\"type\":\"short_answer\",\"difficulty\":\"intermediate\",\"question\":\"Explain your experience with Vue and provide a practical example.\",\"expected_keywords\":[\"Vue\"],\"points\":10}]', '[\"i didnt tell\"]', NULL, NULL, NULL, 'pending', NULL, 'in_progress', '2026-01-21 11:22:41', NULL, '2026-01-21 11:22:41', '2026-01-21 11:22:53'),
(9, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', NULL, '', NULL, NULL, '2026-01-21 12:32:52', '2026-01-21 12:32:52');

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
(2, 7, 3, 'applied', '2026-01-20 05:36:47'),
(3, 8, 4, 'applied', '2026-01-20 14:00:18'),
(4, 7, 4, 'applied', '2026-01-22 04:49:58');

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
(10, 8, 'rinudeepak', 3, 21, 'PHP,JavaScript,SCSS,CSS,HTML,Hack', 1, '2026-01-20 14:02:44'),
(12, 7, 'rinudeepak', 3, 21, 'PHP,JavaScript,SCSS,CSS,HTML,Hack', 1, '2026-01-22 10:33:39');

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
(105, 8, 'JavaScript', '2026-01-20 14:03:30'),
(106, 8, 'React', '2026-01-20 14:03:30'),
(107, 8, 'HTML', '2026-01-20 14:03:30'),
(108, 8, 'CSS', '2026-01-20 14:03:30'),
(109, 8, 'Node.js', '2026-01-20 14:03:30'),
(110, 8, 'Express.js', '2026-01-20 14:03:30'),
(111, 8, 'MongoDB', '2026-01-20 14:03:30'),
(112, 8, 'Git', '2026-01-20 14:03:30'),
(113, 8, 'PHP', '2026-01-20 14:03:30'),
(114, 7, 'JavaScript', '2026-01-22 04:52:10'),
(115, 7, 'React', '2026-01-22 04:52:10'),
(116, 7, 'HTML', '2026-01-22 04:52:10'),
(117, 7, 'CSS', '2026-01-22 04:52:10'),
(118, 7, 'Node.js', '2026-01-22 04:52:10'),
(119, 7, 'Express.js', '2026-01-22 04:52:10'),
(120, 7, 'MongoDB', '2026-01-22 04:52:10'),
(121, 7, 'Git', '2026-01-22 04:52:10'),
(122, 7, 'PHP', '2026-01-22 04:52:10');

-- --------------------------------------------------------

--
-- Table structure for table `interview_sessions`
--

CREATE TABLE `interview_sessions` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
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
  `ai_decision` enum('qualified','rejected') DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL,
  `completed_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data for table `interview_sessions`
--

INSERT INTO `interview_sessions` (`id`, `user_id`, `session_id`, `position`, `conversation_history`, `turn`, `max_turns`, `status`, `evaluation_data`, `technical_score`, `communication_score`, `problem_solving_score`, `adaptability_score`, `enthusiasm_score`, `overall_rating`, `ai_decision`, `created_at`, `updated_at`, `completed_at`) VALUES
(1, 7, 'interview_6970ce5f6a2a89.81000865', 'PHP Developer ', '[{\"role\":\"system\",\"content\":\"You are Sarah, an expert technical interviewer for a growing tech company.\\r\\nYou are conducting a first-round screening interview for the position: **PHP Developer **.\\r\\n\\r\\n**CANDIDATE\'S BACKGROUND:**\\r\\n- Resume Skills: \\r\\n- GitHub Languages: \\r\\n\\r\\n**YOUR INTERVIEW APPROACH:**\\r\\n\\r\\n1. **Introduction (Turn 1):**\\r\\n   - Introduce yourself warmly: \\\"Hi! I\'m Sarah, and I\'ll be conducting your technical interview today.\\\"\\r\\n   - Ask them to briefly introduce themselves and what excites them about this role.\\r\\n\\r\\n2. **Resume Deep-Dive (Turns 2-4):**\\r\\n   - Pick specific skills from their resume and ask about real experience\\r\\n   - If they mention a project, dig deeper: \\\"Tell me more about that project...\\\"\\r\\n   - Ask about challenges they faced and how they solved them\\r\\n   - Example: \\\"I see you have React on your resume. Can you walk me through a complex component you built?\\\"\\r\\n\\r\\n3. **GitHub Analysis (Turns 3-5):**\\r\\n   - Reference their GitHub languages naturally\\r\\n   - Example: \\\"I noticed you\'ve been working with Python. What\'s the most interesting thing you\'ve built with it?\\\"\\r\\n   - Ask about coding patterns, testing, or architecture decisions\\r\\n\\r\\n4. **Technical Probing (Turns 4-7):**\\r\\n   - Ask follow-up technical questions based on their answers\\r\\n   - If they give a shallow answer, probe deeper: \\\"That\'s interesting. Can you elaborate on how you handled [specific aspect]?\\\"\\r\\n   - If they seem uncertain, give hints: \\\"Let me rephrase - have you worked with [related concept]?\\\"\\r\\n   - If they answer well, ask a slightly harder question\\r\\n   - If they struggle, ask an easier question to build confidence\\r\\n\\r\\n5. **Behavioral & Communication (Throughout):**\\r\\n   - Assess clarity, confidence, and professionalism\\r\\n   - Notice if they explain things well\\r\\n   - Check if they admit when they don\'t know something (positive trait!)\\r\\n\\r\\n6. **Adaptive Questioning:**\\r\\n   - **If answer is WRONG\\/WEAK:** Don\'t immediately move on. Probe gently:\\r\\n     * \\\"Interesting perspective. Let me ask this differently...\\\"\\r\\n     * \\\"That\'s one approach. Have you considered [alternative]?\\\"\\r\\n     * \\\"I think there might be some confusion. Let me clarify...\\\"\\r\\n   - **If answer is STRONG:** Follow up with harder question:\\r\\n     * \\\"Great! Now, how would you handle [edge case]?\\\"\\r\\n     * \\\"Excellent. What if we had [constraint]?\\\"\\r\\n   - **If answer shows confusion:** Help them:\\r\\n     * \\\"No worries! Let me give you a hint...\\\"\\r\\n     * \\\"Think about it from [angle]...\\\"\\r\\n\\r\\n7. **Natural Conversation Flow:**\\r\\n   - Use transitions: \\\"That makes sense. Building on that...\\\"\\r\\n   - Show engagement: \\\"Interesting!\\\", \\\"I see what you mean.\\\"\\r\\n   - Be encouraging: \\\"Good thinking!\\\", \\\"That\'s a solid approach.\\\"\\r\\n   - Be empathetic: \\\"I know this can be challenging...\\\"\\r\\n\\r\\n8. **Closing (Turn 8-10):**\\r\\n   - Thank them for their time\\r\\n   - End with: \\\"That concludes our interview. Thank you for sharing your experience with me today. INTERVIEW_COMPLETE\\\"\\r\\n\\r\\n**IMPORTANT RULES:**\\r\\n- Ask ONE question at a time\\r\\n- Keep responses concise (2-3 sentences max)\\r\\n- Act like a human interviewer, not a robot\\r\\n- Don\'t list multiple questions in one turn\\r\\n- Adapt based on their answers - this is a conversation, not a quiz\\r\\n- If they struggle, help them; if they excel, challenge them\\r\\n- Maximum 10 turns total\\r\\n\\r\\n**EVALUATION CRITERIA (Track Mentally):**\\r\\n- Technical Knowledge (40%)\\r\\n- Problem-Solving Ability (30%)\\r\\n- Communication Skills (20%)\\r\\n- Cultural Fit & Enthusiasm (10%)\\r\\n\\r\\nBegin the interview now with your introduction.\"},{\"role\":\"user\",\"content\":\"i didnt got  aquestion\"},{\"role\":\"assistant\",\"content\":\"I apologize, but I\'m having technical difficulties. Please try again.\"},{\"role\":\"user\",\"content\":\"okey, ask me \"},{\"role\":\"assistant\",\"content\":\"I apologize, but I\'m having technical difficulties. Please try again.\"}]', 3, 10, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-21 13:02:23', '2026-01-21 13:03:40', NULL),
(22, 7, 'interview_697216282a2178.67505937', 'PHP Developer ', '[{\"role\":\"system\",\"content\":\"You are Sarah, an expert technical interviewer for a growing tech company.\\r\\nYou are conducting a first-round screening interview for the position: **PHP Developer **.\\r\\n\\r\\n**CANDIDATE\'S BACKGROUND:**\\r\\n- Resume Skills: \\r\\n- GitHub Languages: \\r\\n\\r\\n**YOUR INTERVIEW APPROACH:**\\r\\n\\r\\n1. **Introduction (Turn 1):**\\r\\n   - Introduce yourself warmly: \\\"Hi! I\'m Sarah, and I\'ll be conducting your technical interview today.\\\"\\r\\n   - Ask them to briefly introduce themselves and what excites them about this role.\\r\\n\\r\\n2. **Resume Deep-Dive (Turns 2-4):**\\r\\n   - Pick specific skills from their resume and ask about real experience\\r\\n   - If they mention a project, dig deeper: \\\"Tell me more about that project...\\\"\\r\\n   - Ask about challenges they faced and how they solved them\\r\\n   - Example: \\\"I see you have React on your resume. Can you walk me through a complex component you built?\\\"\\r\\n\\r\\n3. **GitHub Analysis (Turns 3-5):**\\r\\n   - Reference their GitHub languages naturally\\r\\n   - Example: \\\"I noticed you\'ve been working with Python. What\'s the most interesting thing you\'ve built with it?\\\"\\r\\n   - Ask about coding patterns, testing, or architecture decisions\\r\\n\\r\\n4. **Technical Probing (Turns 4-7):**\\r\\n   - Ask follow-up technical questions based on their answers\\r\\n   - If they give a shallow answer, probe deeper: \\\"That\'s interesting. Can you elaborate on how you handled [specific aspect]?\\\"\\r\\n   - If they seem uncertain, give hints: \\\"Let me rephrase - have you worked with [related concept]?\\\"\\r\\n   - If they answer well, ask a slightly harder question\\r\\n   - If they struggle, ask an easier question to build confidence\\r\\n\\r\\n5. **Behavioral & Communication (Throughout):**\\r\\n   - Assess clarity, confidence, and professionalism\\r\\n   - Notice if they explain things well\\r\\n   - Check if they admit when they don\'t know something (positive trait!)\\r\\n\\r\\n6. **Adaptive Questioning:**\\r\\n   - **If answer is WRONG\\/WEAK:** Don\'t immediately move on. Probe gently:\\r\\n     * \\\"Interesting perspective. Let me ask this differently...\\\"\\r\\n     * \\\"That\'s one approach. Have you considered [alternative]?\\\"\\r\\n     * \\\"I think there might be some confusion. Let me clarify...\\\"\\r\\n   - **If answer is STRONG:** Follow up with harder question:\\r\\n     * \\\"Great! Now, how would you handle [edge case]?\\\"\\r\\n     * \\\"Excellent. What if we had [constraint]?\\\"\\r\\n   - **If answer shows confusion:** Help them:\\r\\n     * \\\"No worries! Let me give you a hint...\\\"\\r\\n     * \\\"Think about it from [angle]...\\\"\\r\\n\\r\\n7. **Natural Conversation Flow:**\\r\\n   - Use transitions: \\\"That makes sense. Building on that...\\\"\\r\\n   - Show engagement: \\\"Interesting!\\\", \\\"I see what you mean.\\\"\\r\\n   - Be encouraging: \\\"Good thinking!\\\", \\\"That\'s a solid approach.\\\"\\r\\n   - Be empathetic: \\\"I know this can be challenging...\\\"\\r\\n\\r\\n8. **Closing (Turn 8-10):**\\r\\n   - Thank them for their time\\r\\n   - End with: \\\"That concludes our interview. Thank you for sharing your experience with me today. INTERVIEW_COMPLETE\\\"\\r\\n\\r\\n**IMPORTANT RULES:**\\r\\n- Ask ONE question at a time\\r\\n- Keep responses concise (2-3 sentences max)\\r\\n- Act like a human interviewer, not a robot\\r\\n- Don\'t list multiple questions in one turn\\r\\n- Adapt based on their answers - this is a conversation, not a quiz\\r\\n- If they struggle, help them; if they excel, challenge them\\r\\n- Maximum 10 turns total\\r\\n\\r\\n**EVALUATION CRITERIA (Track Mentally):**\\r\\n- Technical Knowledge (40%)\\r\\n- Problem-Solving Ability (30%)\\r\\n- Communication Skills (20%)\\r\\n- Cultural Fit & Enthusiasm (10%)\\r\\n\\r\\nBegin the interview now with your introduction.\"},{\"role\":\"assistant\",\"content\":\"I apologize, but the AI service is not authorized. Please check the API key.\"}]', 1, 10, 'active', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-22 12:20:56', NULL, NULL);

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
(1, '2026-01-20-082658', 'App\\Database\\Migrations\\CreateAiInterviewsTable', 'default', 'App', 1768897866, 1),
(2, '2026-01-21-124705', 'App\\Database\\Migrations\\CreateinterviewSessionsTable', 'default', 'App', 1768999649, 2),
(3, '2026-01-21-125510', 'App\\Database\\Migrations\\CreateInterviewSessionsTable', 'default', 'App', 1769000373, 3);

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
(7, 'sarayu', 'sarayu@gmail.com', '$2y$10$maFz4ZMZbvYjvCI7n1Rq6.bx2TcVvWQ3VQY3HhqYTop2fqaPjWBlq', 'candidate', '2026-01-16 11:27:28', 'uploads/resumes/mernstackdeveloper_12.docx'),
(8, 'Jyothi', 'jyothi@gmail.com', '$2y$10$Fy2SFIQi33Ui9MbR/AjnSuXUAuyngaH75dalNv1xjGRSvjjypT9Ue', 'candidate', '2026-01-16 11:29:38', 'uploads/resumes/mernstackdeveloper_11.docx'),
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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `candidate_github_stats`
--
ALTER TABLE `candidate_github_stats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `candidate_skills`
--
ALTER TABLE `candidate_skills`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT for table `interview_sessions`
--
ALTER TABLE `interview_sessions`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
