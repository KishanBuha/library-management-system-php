-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 24, 2025 at 02:16 PM
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
-- Database: `library_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`) VALUES
(1, 'admin', '$2y$10$TEnzuctNFzZZMKgBDoqAaONC6QqHXo50Jbmq4X3U60263SyzQHYM6', '2025-11-04 04:34:29');

-- --------------------------------------------------------

--
-- Table structure for table `books`
--

CREATE TABLE `books` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `author` varchar(100) NOT NULL,
  `isbn` varchar(30) NOT NULL,
  `category` varchar(100) DEFAULT NULL,
  `copies` int(11) DEFAULT 1 CHECK (`copies` >= 0),
  `added_on` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `books`
--

INSERT INTO `books` (`id`, `title`, `author`, `isbn`, `category`, `copies`, `added_on`) VALUES
(21, 'Let Us C', 'Yashavant Kanetkar', '9788183331630', 'Programming', 15, '2025-11-04 04:45:17'),
(22, 'Data Structures Using C', 'Reema Thareja', '9780198065449', 'Data Structures', 14, '2025-11-04 04:45:17'),
(23, 'Database System Concepts', 'Silberschatz and Korth', '9789332901384', 'Database', 13, '2025-11-04 04:45:17'),
(24, 'Operating System Concepts', 'Galvin and Gagne', '9788126508856', 'Operating Systems', 24, '2025-11-04 04:45:17'),
(25, 'Computer Networks', 'Andrew S. Tanenbaum', '9789332576223', 'Networking', 13, '2025-11-04 04:45:17'),
(26, 'Object-Oriented Programming with C++', 'E. Balagurusamy', '9789351341816', 'Programming', 15, '2025-11-04 04:45:17'),
(27, 'Core Java: An Integrated Approach', 'R. Nageswara Rao', '9789351199252', 'Programming', 14, '2025-11-04 04:45:17'),
(28, 'Software Engineering: A Practitioner\'s Approach', 'Roger Pressman', '9789353165710', 'Software Engineering', 13, '2025-11-04 04:45:17'),
(29, 'Digital Logic and Computer Design', 'M. Morris Mano', '9789332543530', 'Computer Architecture', 23, '2025-11-04 04:45:17'),
(30, 'Introduction to Algorithms', 'Cormen, Leiserson, Rivest', '9780262033848', 'Data Structures', 22, '2025-11-04 04:45:17'),
(31, 'Discrete Mathematics and Its Applications', 'Kenneth H. Rosen', '9780073383095', 'Mathematics', 14, '2025-11-04 04:45:17'),
(32, 'Programming with Python', 'John Zelle', '9781590282435', 'Programming', 23, '2025-11-04 04:45:17'),
(33, 'PHP and MySQL Web Development', 'Luke Welling', '9780321833891', 'Web Development', 12, '2025-11-04 04:45:17'),
(34, 'Learning Web Design', 'Jennifer Robbins', '9781491960202', 'Web Development', 22, '2025-11-04 04:45:17'),
(35, 'UNIX and Linux System Administration Handbook', 'Evi Nemeth', '9780134277554', 'Operating Systems', 23, '2025-11-04 04:45:17'),
(36, 'Cloud Computing: Principles and Paradigms', 'Rajkumar Buyya', '9780470940101', 'Cloud Computing', 25, '2025-11-04 04:45:17'),
(37, 'Artificial Intelligence: A Modern Approach', 'Russell and Norvig', '9789353947814', 'Artificial Intelligence', 21, '2025-11-04 04:45:17'),
(38, 'Foundations of Software Testing', 'Aditya P. Mathur', '9788131716601', 'Software Engineering', 13, '2025-11-04 04:45:17'),
(39, 'Data Mining: Concepts and Techniques', 'Jiawei Han', '9789380931913', 'Data Mining', 12, '2025-11-04 04:45:17'),
(40, 'Fundamentals of Data Structures in C++', 'Sahni and Horowitz', '9788173715228', 'Data Structures', 14, '2025-11-04 04:45:17');

-- --------------------------------------------------------

--
-- Table structure for table `contacts`
--

CREATE TABLE `contacts` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `issued_books`
--

CREATE TABLE `issued_books` (
  `id` int(11) NOT NULL,
  `book_id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `issue_date` date NOT NULL,
  `due_date` date NOT NULL,
  `return_date` date DEFAULT NULL,
  `status` enum('Issued','Returned') DEFAULT 'Issued'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `issued_books`
--

INSERT INTO `issued_books` (`id`, `book_id`, `student_id`, `issue_date`, `due_date`, `return_date`, `status`) VALUES
(1, 33, 1, '2025-11-04', '0000-00-00', NULL, 'Issued'),
(2, 37, 1, '2025-11-04', '0000-00-00', '2025-11-04', 'Issued');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `student_id` varchar(50) NOT NULL,
  `roll_no` varchar(50) NOT NULL,
  `department` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `registered_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `student_id`, `roll_no`, `department`, `email`, `password`, `registered_at`) VALUES
(1, 'user', '001', '101', 'BCA', 'user@gmail.com', '$2y$10$SrsKbvUz0ftTR8643f.rAuEXAX3gma5xObdAfAsQrDjAI6hBBL7oC', '2025-11-04 04:37:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `books`
--
ALTER TABLE `books`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `isbn` (`isbn`),
  ADD KEY `idx_books_isbn` (`isbn`);

--
-- Indexes for table `contacts`
--
ALTER TABLE `contacts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD PRIMARY KEY (`id`),
  ADD KEY `book_id` (`book_id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_students_email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `books`
--
ALTER TABLE `books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `contacts`
--
ALTER TABLE `contacts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `issued_books`
--
ALTER TABLE `issued_books`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `issued_books`
--
ALTER TABLE `issued_books`
  ADD CONSTRAINT `issued_books_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `issued_books_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
