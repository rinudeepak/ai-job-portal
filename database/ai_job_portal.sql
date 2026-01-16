-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 16, 2026 at 01:20 PM
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
  `status` varchar(50) DEFAULT NULL,
  `applied_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` int(11) NOT NULL,
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

INSERT INTO `jobs` (`id`, `title`, `company`, `location`, `description`, `required_skills`, `experience_level`, `min_ai_cutoff_score`, `status`, `created_at`) VALUES
(1, 'PHP Developer', 'TechNova Solutions', 'Kochi, Kerala', 'Develop and maintain backend web applications using Core PHP and MVC architecture.', 'Core PHP, MySQL, MVC, HTML, CSS, JavaScript', 'junior', 60, 'open', '2026-01-16 14:26:56'),
(2, 'WordPress Developer', 'Creative Web Labs', 'Remote', 'Customize WordPress themes and plugins based on client requirements.', 'WordPress, PHP, WooCommerce, HTML, CSS', 'fresher', 50, 'open', '2026-01-16 14:26:56'),
(3, 'Backend Developer', 'AI Tech Systems', 'Bangalore', 'Build scalable backend APIs and database-driven applications.', 'PHP, CodeIgniter, REST API, MySQL', 'mid', 70, 'open', '2026-01-16 14:26:56'),
(4, 'Full Stack Developer', 'InnovateSoft Pvt Ltd', 'Chennai', 'Develop complete web solutions including frontend and backend.', 'PHP, Laravel, JavaScript, Bootstrap, MySQL', 'senior', 80, 'open', '2026-01-16 14:26:56'),
(5, 'Junior Web Developer', 'Startup Hub', 'Trivandrum', 'Assist senior developers in building and testing web applications.', 'HTML, CSS, PHP Basics, MySQL', 'fresher', 40, 'closed', '2026-01-16 14:26:56');

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
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`) VALUES
(1, 'Athira', 'athira@gmail.com', '$2y$10$9sDk93NqixUOU8zcF8FhWe1EerdPh1Ed.l3ABWTep1ou.Q1QgcojO', '', '2026-01-14 15:41:23'),
(2, 'Rinu George', 'rinugeorgep@gmail.com', '$2y$10$L0eYVJokqrpjUe0KTOgO.e8dr/fWnsWA4SusuWLyN14SOG5ubk8mi', 'candidate', '2026-01-14 16:32:23'),
(3, 'Rohith Kumar', 'rohith@gmail.com', '$2y$10$F1qVw6VSBW719lA80dzJaOLA6v5SFxhDkOqez6Pwl2OQPelsMnJaW', 'admin', '2026-01-14 16:41:21'),
(4, 'Maya', 'maya@gmail.com', '$2y$10$QsJg7TbsNy0LxIxgbfp83eZ94I8g2rvsqP5WZ1MR7mSxqjONANSiW', 'admin', '2026-01-14 17:45:50'),
(5, 'Candidate1', 'candidate1@gmail.com', '$2y$10$qJgKEvhl7b4pIwBXi/wRbOos/4uXYKx.HeyoZ.OCjzrQL.dfkGW1m', 'candidate', '2026-01-16 11:07:09'),
(6, 'Rinu George', 'abc@gmail.com', '$2y$10$WbbioV.30t4atwrgbq7xweEOUdB2F1/sGIjX.XrBvakUqU0OubSWy', 'admin', '2026-01-16 11:18:30'),
(7, 'sarayu', 'sarayu@gmail.com', '$2y$10$maFz4ZMZbvYjvCI7n1Rq6.bx2TcVvWQ3VQY3HhqYTop2fqaPjWBlq', 'candidate', '2026-01-16 11:27:28'),
(8, 'Jyothi', 'jyothi@gmail.com', '$2y$10$Fy2SFIQi33Ui9MbR/AjnSuXUAuyngaH75dalNv1xjGRSvjjypT9Ue', 'candidate', '2026-01-16 11:29:38'),
(9, 'bbbb', 'bb@gmail.com', '$2y$10$/ezgjOz8BzqNDaBzWjF.3ezb7AsTZDrwlHvBfmtgt9WzvTooAdteO', 'admin', '2026-01-16 11:35:50');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applications`
--
ALTER TABLE `applications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`candidate_id`),
  ADD KEY `job_id` (`job_id`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
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
-- AUTO_INCREMENT for table `applications`
--
ALTER TABLE `applications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
