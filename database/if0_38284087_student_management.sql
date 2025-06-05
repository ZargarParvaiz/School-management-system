-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql112.infinityfree.com
-- Generation Time: Feb 16, 2025 at 12:14 PM
-- Server version: 10.6.19-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_38284087_student_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `date` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id`, `student_id`, `class_id`, `status`, `date`, `created_at`) VALUES
(1, 1, 1, 'present', '2025-02-10', '2025-02-10 13:05:26'),
(2, 2, 1, 'present', '2025-02-10', '2025-02-10 13:05:26'),
(3, 3, 2, 'absent', '2025-02-10', '2025-02-10 13:05:26'),
(4, 4, 2, 'present', '2025-02-10', '2025-02-10 13:05:26'),
(5, 1, 1, 'present', '2025-02-01', '2025-02-10 19:40:04'),
(6, 2, 1, 'present', '2025-02-01', '2025-02-10 19:40:04'),
(7, 5, 1, 'present', '2025-02-01', '2025-02-10 19:40:04'),
(8, 7, 5, 'present', '2025-02-11', '2025-02-11 07:16:37'),
(9, 6, 6, 'present', '2025-02-13', '2025-02-13 11:50:16'),
(10, 8, 8, 'present', '2025-02-13', '2025-02-13 11:50:32'),
(11, 6, 6, 'present', '2025-02-14', '2025-02-14 05:20:59');

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `section` varchar(10) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `classes`
--

INSERT INTO `classes` (`id`, `name`, `section`, `teacher_id`, `created_at`) VALUES
(1, 'Class 10', 'A', 2, '2025-02-10 13:05:26'),
(2, 'Class 10', 'B', 3, '2025-02-10 13:05:26'),
(3, 'Class 9', 'A', 4, '2025-02-10 13:05:26'),
(4, 'Class 9', 'B', 5, '2025-02-10 13:05:26'),
(5, 'Class 1st', 'A', 2, '2025-02-10 16:37:20'),
(6, 'Class 2nd', 'A', 7, '2025-02-10 19:26:48'),
(8, 'class 3rd', 'blue', 4, '2025-02-13 10:51:51');

-- --------------------------------------------------------

--
-- Table structure for table `results`
--

CREATE TABLE `results` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `subject_id` int(11) DEFAULT NULL,
  `marks` decimal(5,2) NOT NULL,
  `grade` varchar(2) DEFAULT NULL,
  `exam_type` varchar(50) NOT NULL,
  `exam_date` date DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `results`
--

INSERT INTO `results` (`id`, `student_id`, `subject_id`, `marks`, `grade`, `exam_type`, `exam_date`, `created_at`) VALUES
(1, 1, 1, '85.50', 'A', 'Midterm', '2024-01-10', '2025-02-10 13:05:26'),
(2, 1, 2, '78.75', 'B+', 'Midterm', '2024-01-10', '2025-02-10 13:05:26'),
(3, 2, 1, '92.00', 'A+', 'Midterm', '2024-01-10', '2025-02-10 13:05:26'),
(4, 2, 2, '88.25', 'A', 'Midterm', '2024-01-10', '2025-02-10 13:05:26'),
(5, 5, 1, '80.00', 'A', 'Midterm', '2025-02-10', '2025-02-10 16:48:58'),
(6, 8, 11, '60.00', 'B', 'Final', '2025-02-13', '2025-02-13 10:54:33'),
(7, 8, 14, '90.00', 'A+', 'Final', '2025-02-13', '2025-02-13 10:55:14'),
(8, 8, 14, '90.00', 'A+', 'Final', '2025-02-13', '2025-02-13 11:04:58'),
(9, 8, 12, '60.00', 'B', 'Final', '2025-02-13', '2025-02-13 11:05:22');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `registration_number` varchar(20) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `block_results` tinyint(1) DEFAULT 0,
  `block_message` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `registration_number`, `first_name`, `last_name`, `email`, `phone`, `gender`, `date_of_birth`, `address`, `class_id`, `created_at`, `block_results`, `block_message`) VALUES
(1, '2024001', 'Alice', 'Brown', 'alice.brown@student.com', '5678901234', 'female', '2008-05-15', '123 Student St', 1, '2025-02-10 13:05:26', 0, 'Please clear your dues 1000'),
(2, '2024002', 'Bob', 'Taylor', 'bob.taylor@student.com', '6789012345', 'male', '2008-07-22', '456 School Ave', 1, '2025-02-10 13:05:26', 0, 'Please clear your dues 10002'),
(3, '2024003', 'Charlie', 'Davis', 'charlie.davis@student.com', '7890123456', 'male', '2008-03-10', '789 Education Rd', 2, '2025-02-10 13:05:26', 1, 'Please clear your dues two months'),
(4, '2024004', 'Diana', 'Miller', 'diana.miller@student.com', '8901234567', 'female', '2008-11-30', '321 Learning Ln', 2, '2025-02-10 13:05:26', 1, 'Please clear your dues one month'),
(5, '20001', 'Par', 'Zargar', 'gh@n.com', '0000000000', NULL, NULL, NULL, 1, '2025-02-10 16:39:08', 0, 'Please clear your dues 100'),
(6, '1', 'Zahid ahmad', 'Zargar', 'zahid@gmail.com', '8491892003', NULL, NULL, NULL, 6, '2025-02-10 19:28:22', 0, NULL),
(8, '1000', 'zahider', 'xargar', 'ayan@gmail.com', '9999888888', NULL, NULL, NULL, 8, '2025-02-13 10:54:02', 0, 'Please clear your dues');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `class_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `code`, `class_id`, `created_at`) VALUES
(10, 'hindi', 'hindi8', 1, '2025-02-13 10:51:22'),
(3, 'English', 'ENG101', 1, '2025-02-10 13:05:26'),
(4, 'History', 'HIS101', 1, '2025-02-10 13:05:26'),
(9, 'hindi', 'hindi123', 5, '2025-02-13 10:47:36'),
(6, 'Science', 'SCI102', 2, '2025-02-10 13:05:26'),
(7, 'English', 'ENG102', 2, '2025-02-10 13:05:26'),
(8, 'History', 'HIS102', 2, '2025-02-10 13:05:26'),
(11, 'hindi', 'hindi3', 8, '2025-02-13 10:52:15'),
(12, 'science', 'sc3', 8, '2025-02-13 10:52:39'),
(13, 'Math', 'math3', 8, '2025-02-13 10:53:04'),
(14, 'urdu', 'urdu3', 8, '2025-02-13 10:53:23');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','teacher') NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `specialization` varchar(100) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `email`, `phone`, `specialization`, `created_at`) VALUES
(8, 'Masroor', '$2y$10$I3yY9IEFYY7vO1pDECFNRODbKL1ezFSPuyKAU7QqvC0lu6lxXRGRq', 'teacher', 'admin@example.com', '9999888888', 'Principal', '2025-02-14 11:00:58'),
(7, 'parvaiz', '$2y$10$ryDDmW5gB7jD93oDJS2h1.SlYO7cuDVYgea7lJ3aEYK1eSPVPnxAW', 'admin', 'a@b.com', '0000000000', '', '2025-02-10 16:56:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `results`
--
ALTER TABLE `results`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `registration_number` (`registration_number`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `results`
--
ALTER TABLE `results`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
