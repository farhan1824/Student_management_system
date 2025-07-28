-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 26, 2025 at 10:39 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `student_management_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `correction_requests`
--

CREATE TABLE `correction_requests` (
  `id` int(11) NOT NULL,
  `requested_by` enum('student','teacher') NOT NULL,
  `submitted_by_id` varchar(50) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `attendance_date` date DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `reason_type` enum('attendance','result') DEFAULT NULL,
  `status` enum('pending','approved','rejected') DEFAULT 'pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `correction_requests`
--

INSERT INTO `correction_requests` (`id`, `requested_by`, `submitted_by_id`, `student_id`, `subject_id`, `attendance_date`, `reason`, `reason_type`, `status`, `requested_at`) VALUES
(1, 'teacher', 'CSE147852', 1, 1, '2025-07-23', 'ami bhul e absent diye felsi', NULL, 'pending', '2025-07-25 17:39:13'),
(2, 'student', 'cse-12', 1, 1, NULL, 'test', 'result', 'pending', '2025-07-26 07:17:24'),
(3, 'student', 'cse-12', 1, 1, '2025-07-25', 'i think i was present that day', 'attendance', 'pending', '2025-07-26 07:18:35'),
(4, 'student', 'cse-12', 1, 1, '2025-07-08', 'this is', 'attendance', 'pending', '2025-07-26 07:47:34');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `roll` varchar(20) DEFAULT NULL,
  `name` varchar(15) DEFAULT NULL,
  `profile_pic` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `roll`, `name`, `profile_pic`) VALUES
(1, 'cse-12', 'Md.Tawrat Ahmed', NULL),
(2, 'cse-13', 'Md.Chengis khan', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `student_attendance`
--

CREATE TABLE `student_attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL DEFAULT curdate(),
  `status` enum('Present','Absent') DEFAULT 'Present'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_attendance`
--

INSERT INTO `student_attendance` (`id`, `student_id`, `subject_id`, `attendance_date`, `status`) VALUES
(1, 1, 1, '2025-07-24', 'Present'),
(2, 2, 1, '2025-07-24', 'Present'),
(3, 1, 1, '2025-07-16', 'Present'),
(4, 2, 1, '2025-07-16', 'Present'),
(5, 1, 1, '2025-07-25', 'Present'),
(6, 2, 1, '2025-07-25', 'Present');

-- --------------------------------------------------------

--
-- Table structure for table `student_subjects`
--

CREATE TABLE `student_subjects` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `enrolled_date` date DEFAULT curdate()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_subjects`
--

INSERT INTO `student_subjects` (`id`, `student_id`, `subject_id`, `enrolled_date`) VALUES
(1, 1, 1, '0000-00-00'),
(2, 2, 1, '0000-00-00');

-- --------------------------------------------------------

--
-- Table structure for table `subjects`
--

CREATE TABLE `subjects` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `class_count` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `subjects`
--

INSERT INTO `subjects` (`id`, `name`, `class_count`) VALUES
(1, 'physics', 20),
(2, 'math', 15);

-- --------------------------------------------------------

--
-- Table structure for table `teacher_subjects`
--

CREATE TABLE `teacher_subjects` (
  `id` int(11) NOT NULL,
  `teacher_num` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher_subjects`
--

INSERT INTO `teacher_subjects` (`id`, `teacher_num`, `subject_id`) VALUES
(1, 'CSE147852', 2),
(2, 'CSE147852', 1);

-- --------------------------------------------------------

--
-- Table structure for table `teach_attendance`
--

CREATE TABLE `teach_attendance` (
  `id` int(11) NOT NULL,
  `teacher_num` varchar(50) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `attendance_date` date NOT NULL DEFAULT curdate(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teach_attendance`
--

INSERT INTO `teach_attendance` (`id`, `teacher_num`, `subject_id`, `attendance_date`, `created_at`) VALUES
(3, 'CSE147852', 1, '2025-07-23', '2025-07-23 17:19:41'),
(4, 'CSE147852', 2, '2025-07-23', '2025-07-23 17:59:17'),
(5, 'CSE147852', 1, '2025-07-24', '2025-07-24 15:01:58'),
(6, 'CSE147852', 2, '2025-07-24', '2025-07-24 15:02:04'),
(7, 'CSE147852', 1, '2025-07-25', '2025-07-25 10:48:35'),
(8, 'CSE147852', 2, '2025-07-25', '2025-07-25 10:56:42'),
(19, 'CSE147852', 2, '2025-07-26', '2025-07-26 08:18:45'),
(20, 'CSE147852', 1, '2025-07-26', '2025-07-26 08:22:01');

-- --------------------------------------------------------

--
-- Table structure for table `teach_details`
--

CREATE TABLE `teach_details` (
  `id` int(11) NOT NULL,
  `teach_num` varchar(20) NOT NULL,
  `Name` varchar(20) NOT NULL,
  `pwd` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teach_details`
--

INSERT INTO `teach_details` (`id`, `teach_num`, `Name`, `pwd`) VALUES
(1, 'CSE123456', 'shirina akter', '147'),
(2, 'CSE147852', 'Farhan Ahmed', '123');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `correction_requests`
--
ALTER TABLE `correction_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_attendance` (`student_id`,`subject_id`,`attendance_date`),
  ADD KEY `fk_student_attendance_subject` (`subject_id`);

--
-- Indexes for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_student` (`student_id`),
  ADD KEY `fk_subject` (`subject_id`);

--
-- Indexes for table `subjects`
--
ALTER TABLE `subjects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_num` (`teacher_num`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `teach_attendance`
--
ALTER TABLE `teach_attendance`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teacher_num` (`teacher_num`,`subject_id`,`attendance_date`),
  ADD KEY `subject_id` (`subject_id`);

--
-- Indexes for table `teach_details`
--
ALTER TABLE `teach_details`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `teach_num` (`teach_num`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `correction_requests`
--
ALTER TABLE `correction_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `student_attendance`
--
ALTER TABLE `student_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `student_subjects`
--
ALTER TABLE `student_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `subjects`
--
ALTER TABLE `subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `teach_attendance`
--
ALTER TABLE `teach_attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `teach_details`
--
ALTER TABLE `teach_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student_attendance`
--
ALTER TABLE `student_attendance`
  ADD CONSTRAINT `fk_student_attendance_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_student_attendance_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `student_subjects`
--
ALTER TABLE `student_subjects`
  ADD CONSTRAINT `fk_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_subject` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `teacher_subjects`
--
ALTER TABLE `teacher_subjects`
  ADD CONSTRAINT `teacher_subjects_ibfk_1` FOREIGN KEY (`teacher_num`) REFERENCES `teach_details` (`teach_num`) ON DELETE CASCADE,
  ADD CONSTRAINT `teacher_subjects_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teach_attendance`
--
ALTER TABLE `teach_attendance`
  ADD CONSTRAINT `teach_attendance_ibfk_1` FOREIGN KEY (`teacher_num`) REFERENCES `teach_details` (`teach_num`) ON DELETE CASCADE,
  ADD CONSTRAINT `teach_attendance_ibfk_2` FOREIGN KEY (`subject_id`) REFERENCES `subjects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
