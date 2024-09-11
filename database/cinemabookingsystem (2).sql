-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Sep 11, 2024 at 03:59 PM
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
-- Database: `cinemabookingsystem`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `showtime_id` int(11) DEFAULT NULL,
  `seat_id` int(11) DEFAULT NULL,
  `booking_time` date DEFAULT current_timestamp(),
  `status` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `user_id`, `showtime_id`, `seat_id`, `booking_time`, `status`, `image`) VALUES
(216, 2, 55, 47, '2024-09-11', 'pending', 'uploads/stree 2.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `halls`
--

CREATE TABLE `halls` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `capacity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `halls`
--

INSERT INTO `halls` (`id`, `name`, `capacity`) VALUES
(10, 'cinema I', 60),
(11, 'cinema II', 60);

-- --------------------------------------------------------

--
-- Table structure for table `movies`
--

CREATE TABLE `movies` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `release_date` date DEFAULT NULL,
  `duration` int(11) DEFAULT NULL,
  `genre` varchar(50) DEFAULT NULL,
  `rating` decimal(3,1) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `movies`
--

INSERT INTO `movies` (`id`, `name`, `description`, `release_date`, `duration`, `genre`, `rating`, `image`) VALUES
(19, 'STREE 2', 'Rajkummar RaoShraddha KapoorPankaj Tripathi တိုကပဲပါဝင်သရုပ်ဆောင်ထားတဲ့ Stree 1 မှာ အပ်ချုပ်သမားလေး ဗစ်ကီဟာပွဲတော်ကိုသွားရင်းနဲ့ မိန်းမလှလေးတစ်ယောက်ကိုမြင်မြင်ချင်းပဲချစ်သွားခဲ့တယ်။ သူချစ်နေတဲ့သူဟာသရဲမလေးဖြစ်နေခဲ့တယ်။ Stree 2 ထဲမှာတော့ Chanderi မြိုကိုထက်ပြီးသရဲခြောက်ခြင်းခံရပြန်တယ်။ ဒီတစ်ကြိမ်မှာတော့ အမျိုးသမီးများသည် ကြောက်စရာကောင်းတဲခေါင်းပြတ်သရဲရဲ့ ဖမ်းဆီးခေါ်ဆောင်ခြင်းခံခဲ့ရတာပဲဖြစ်ပါတယ်။\r\n ဗစ်ကီတစ်ယောက်သူတိုရဲ့မြိုနှင့် သူချစ်ရသူတွေကိုဘယ်လိုတွေကယ်တင်သွားမှာလဲဆိုတာကို အချစ်၊ ဟာသ ရောပြီးရိုက်ကူးတင်ဆက်ထားတဲ့ သရဲကားလေးဖြစ်လို လာရောက်အားပေးဖိုဖိတ်ခေါ်လိုက်ရပါတယ်နော်။', '2024-09-01', 120, 'Horror', 8.0, 'uploads/stree 2.jpg'),
(20, 'GOAT', 'The Greatest of All Time (G.O.A.T.)\r\n==============\r\nComing Soon on Cinemas\r\nအိန္ဒိယတောင်ပိုင်း နာမည်ကြီးမင်းသားကြီးဗီဂျေးရဲ့ဇာတ်ကားသစ် ဖြစ်တဲ့ The Greatest of All Time (G.O.A.T.) ကိုမကြာမီ အဆင့်မြင့်ရုပ်ရှင်ရုံကြီးများတွင် ရုံတင်ပြသတော့မှာဘဲဖြစ်ပါတယ်။ Thalapathy Vijay ၏နောက်ဆုံးဇာတ်ကားကြီးဘဲဖြစ်လာမလားဆိုတာကို စောင့်ကြည့်ရမှာဘဲဖြစ်ပါတယ်။ ထိုဇာတ်ကားကြီးတွင် လူငယ်တစ်ယောက်အနေနဲကော လူလတ်ပိုင်းတစ်ယောက်အနေနဲ့ပါ စရိုက်နှစ်ခုနဲ့ သရုပ်ဆောင်ထားတာဖြစ်ပြီး မင်းသားကြီးဗီဂျေးရဲ့ Action ကိုတွေကြိုက်နှစ်သတ်တဲ့ပရိသတ်ကြီးတို့အတွက် စောင့်မျှော်ကြည့်ရှုသင့်တဲ့ဇာတ်ကားတစ်ကားဘဲဖြစ်ပါတယ်။\r\n', '2024-09-01', 120, 'ACTION', 9.0, 'uploads/goat.jpg'),
(21, 'Beetlejuice Beetlejuice', 'Lydia မှာ ဂျစ်ကန်ကန်ဆယ်ကျော်သက်သမီးလေးတစ်ယောက်ရှိတယ်၊ သူ့ရဲ့သမီးဟာ လျှိဝှက်ဆန်းကြယ်သောတစ်ခါးပေါက်တစ်ခုကိုရှာဖွေတွေရှိခဲ့ပြီးဖွင့်လိုက်တဲ့အခါမှာတော့ Lydia ရဲ့ဘဝဟာဇောက်ထိုးပြောင်းသွားခဲ့တယ်၊ Beetlejuice ဟု နာမည်ကိုသုံးခါခေါ်တဲ့အခါ ယုတ်ညံ့တဲ့နတ်ဆိုးတစ်ကောင်ဟာပေါ်လာခဲ့ပြီး သူရဲ့ကိုယ်ပိုင်အနိုင့်ကျင့်မှုအမှတ်တံဆိပ်နဲ့ Lydia ဘဝကို ဒုက္ခပေးပါတော့တယ်။ Lydia ဟာ နတ်ဆိုးခိုင်းသမျှကိုအကုန်လုပ်ပေးခဲ့ပြီး နတ်ဆိုးရဲ့လက်ထဲကနေလွတ်မြောက်နိုင့်ပါမလား၊ လွတ်မြောက်နိုင်အောင်ဘယ်လိုတွေလုပ်ဆောင်သွားမလဲဆိုတာ စိတ်ဝင်စားစွာကြည့်ရှုရမဲ့ဇာတ်ကားလေးတစ်ကားဖြစ်လို မင်္ဂလာအဆင်မြင့်ရုပ်ရှင်ရုံကြီးများမှာလာရောက်ကြည့်ရှုဖိုဖိတ်ခေါ်လိုက်ရပါတယ်နော်။', '2024-09-01', 120, 'horror', 7.0, 'uploads/beetlejuice.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `rowandprice`
--

CREATE TABLE `rowandprice` (
  `id` int(11) NOT NULL,
  `row_number` int(11) NOT NULL,
  `price` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rowandprice`
--

INSERT INTO `rowandprice` (`id`, `row_number`, `price`) VALUES
(7, 1, 4000.00),
(9, 2, 5000.00),
(11, 3, 16000.00);

-- --------------------------------------------------------

--
-- Table structure for table `seats`
--

CREATE TABLE `seats` (
  `id` int(11) NOT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `seat_number` varchar(10) NOT NULL,
  `row_number` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `seats`
--

INSERT INTO `seats` (`id`, `hall_id`, `seat_number`, `row_number`) VALUES
(47, 10, '1', 1),
(48, 10, '2', 1),
(50, 10, '3', 1),
(51, 10, '4', 1),
(52, 10, '5', 1),
(53, 10, '6', 1),
(54, 10, '7', 1),
(55, 10, '8', 1),
(56, 10, '9', 1),
(57, 10, '10', 1),
(58, 10, '1', 2),
(59, 10, '2', 2),
(60, 10, '3', 2),
(61, 10, '4', 2),
(62, 10, '5', 2),
(63, 10, '6', 2),
(64, 10, '7', 2),
(65, 10, '8', 2),
(66, 10, '9', 2),
(67, 10, '10', 2),
(68, 10, '1', 3),
(69, 10, '2', 3),
(70, 10, '3', 3),
(71, 10, '4', 3),
(72, 10, '5', 3),
(73, 10, '6', 3),
(74, 11, '1', 1),
(75, 11, '2', 1),
(76, 11, '3', 1),
(77, 11, '4', 1),
(78, 11, '5', 1),
(79, 11, '6', 1),
(80, 11, '7', 1),
(81, 11, '8', 1),
(82, 11, '9', 1),
(83, 11, '10', 1),
(84, 11, '1', 2),
(85, 11, '2', 2),
(86, 11, '3', 2),
(87, 11, '4', 2),
(88, 11, '5', 2),
(89, 11, '6', 2),
(90, 11, '7', 2),
(91, 11, '8', 2),
(92, 11, '9', 2),
(93, 11, '10', 2),
(94, 11, '1', 3),
(95, 11, '2', 3),
(96, 11, '3', 3),
(97, 11, '4', 3),
(98, 11, '5', 3),
(99, 11, '6', 3),
(100, 11, '7', 3),
(101, 11, '8', 3),
(102, 11, '9', 3),
(103, 11, '10', 3),
(104, 10, '7', 3);

-- --------------------------------------------------------

--
-- Table structure for table `showtimes`
--

CREATE TABLE `showtimes` (
  `id` int(11) NOT NULL,
  `movie_id` int(11) DEFAULT NULL,
  `hall_id` int(11) DEFAULT NULL,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `showtimes`
--

INSERT INTO `showtimes` (`id`, `movie_id`, `hall_id`, `start_time`, `end_time`) VALUES
(55, 19, 10, '2024-09-12 09:00:00', '2024-09-12 11:00:00'),
(56, 20, 10, '2024-09-12 13:00:00', '2024-09-12 15:00:00'),
(57, 21, 10, '2024-09-12 16:00:00', '2024-09-12 18:00:00'),
(58, 19, 10, '2024-09-11 21:00:00', '2024-09-11 23:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `role` tinyint(4) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`) VALUES
(2, 'thihawin', 'admin@gmail.com', '$2y$10$YidQuNHV..3O84mnsbQpRupdRtiyITmLfSrMWCVeh9R1Sbgsy3A/C', '09690428503', '12', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `showtime_id` (`showtime_id`),
  ADD KEY `seat_id` (`seat_id`);

--
-- Indexes for table `halls`
--
ALTER TABLE `halls`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `movies`
--
ALTER TABLE `movies`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rowandprice`
--
ALTER TABLE `rowandprice`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `seats`
--
ALTER TABLE `seats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hall_id` (`hall_id`);

--
-- Indexes for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `movie_id` (`movie_id`),
  ADD KEY `hall_id` (`hall_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=217;

--
-- AUTO_INCREMENT for table `halls`
--
ALTER TABLE `halls`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `movies`
--
ALTER TABLE `movies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `rowandprice`
--
ALTER TABLE `rowandprice`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `seats`
--
ALTER TABLE `seats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=105;

--
-- AUTO_INCREMENT for table `showtimes`
--
ALTER TABLE `showtimes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`showtime_id`) REFERENCES `showtimes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`seat_id`) REFERENCES `seats` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `seats`
--
ALTER TABLE `seats`
  ADD CONSTRAINT `seats_ibfk_1` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `showtimes`
--
ALTER TABLE `showtimes`
  ADD CONSTRAINT `showtimes_ibfk_1` FOREIGN KEY (`movie_id`) REFERENCES `movies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `showtimes_ibfk_2` FOREIGN KEY (`hall_id`) REFERENCES `halls` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
