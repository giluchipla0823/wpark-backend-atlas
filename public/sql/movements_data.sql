/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

/*!40000 ALTER TABLE `movements` DISABLE KEYS */;
INSERT INTO `movements` (`id`, `vehicle_id`, `user_id`, `device_id`, `origin_position_type`, `origin_position_id`, `destination_position_type`, `destination_position_id`, `category`, `confirmed`, `canceled`, `manual`, `dt_start`, `dt_end`, `comments`, `created_at`, `updated_at`, `deleted_at`) VALUES
	(1, 1, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:52', '2022-06-05 09:09:52', NULL, '2022-06-05 09:09:52', '2022-06-05 09:09:52', NULL),
	(2, 2, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:53', '2022-06-05 09:09:53', NULL, '2022-06-05 09:09:53', '2022-06-05 09:09:53', NULL),
	(3, 3, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:53', '2022-06-05 09:09:53', NULL, '2022-06-05 09:09:53', '2022-06-05 09:09:53', NULL),
	(4, 4, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:54', '2022-06-05 09:09:54', NULL, '2022-06-05 09:09:54', '2022-06-05 09:09:54', NULL),
	(5, 5, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:55', '2022-06-05 09:09:55', NULL, '2022-06-05 09:09:55', '2022-06-05 09:09:55', NULL),
	(6, 6, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:56', '2022-06-05 09:09:56', NULL, '2022-06-05 09:09:56', '2022-06-05 09:09:56', NULL),
	(7, 7, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:57', '2022-06-05 09:09:57', NULL, '2022-06-05 09:09:57', '2022-06-05 09:09:57', NULL),
	(8, 8, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:58', '2022-06-05 09:09:58', NULL, '2022-06-05 09:09:58', '2022-06-05 09:09:58', NULL),
	(9, 9, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:58', '2022-06-05 09:09:58', NULL, '2022-06-05 09:09:58', '2022-06-05 09:09:58', NULL),
	(10, 10, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:09:59', '2022-06-05 09:09:59', NULL, '2022-06-05 09:09:59', '2022-06-05 09:09:59', NULL),
	(11, 11, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:00', '2022-06-05 09:10:00', NULL, '2022-06-05 09:10:00', '2022-06-05 09:10:00', NULL),
	(12, 12, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(14, 13, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(15, 14, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(16, 15, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(17, 16, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(18, 17, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(19, 18, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(20, 19, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(21, 20, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(22, 21, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(23, 22, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(24, 23, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(25, 24, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(26, 25, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(27, 26, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(28, 27, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL, '2022-06-05 09:10:01', '2022-06-05 09:10:01', NULL),
	(222, 28, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-14 10:30:05', '2022-06-14 10:30:05', NULL, '2022-06-14 10:30:05', '2022-06-14 10:30:05', NULL),
	(223, 29, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-14 10:39:43', '2022-06-14 10:39:43', NULL, '2022-06-14 10:39:43', '2022-06-14 10:39:43', NULL),
	(224, 30, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-14 11:27:46', '2022-06-14 11:27:46', NULL, '2022-06-14 11:27:46', '2022-06-14 11:27:46', NULL),
	(227, 31, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-14 18:59:58', '2022-06-14 18:59:58', NULL, '2022-06-14 18:59:58', '2022-06-14 18:59:58', NULL),
	(228, 32, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-14 19:00:47', '2022-06-14 19:00:47', NULL, '2022-06-14 19:00:47', '2022-06-14 19:00:47', NULL),
	(229, 33, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-14 19:01:27', '2022-06-14 19:01:27', NULL, '2022-06-14 19:01:27', '2022-06-14 19:01:27', NULL),
	(239, 34, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 10:04:18', '2022-06-15 10:04:18', NULL, '2022-06-15 10:04:18', '2022-06-15 10:04:18', NULL),
	(241, 35, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 10:08:07', '2022-06-15 10:08:07', NULL, '2022-06-15 10:08:07', '2022-06-15 10:08:07', NULL),
	(243, 36, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 10:19:11', '2022-06-15 10:19:11', NULL, '2022-06-15 10:19:11', '2022-06-15 10:19:11', NULL),
	(251, 37, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 11:40:31', '2022-06-15 11:40:31', NULL, '2022-06-15 11:40:31', '2022-06-15 11:40:31', NULL),
	(252, 38, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 11:40:54', '2022-06-15 11:40:54', NULL, '2022-06-15 11:40:54', '2022-06-15 11:40:54', NULL),
	(253, 39, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 11:41:12', '2022-06-15 11:41:12', NULL, '2022-06-15 11:41:12', '2022-06-15 11:41:12', NULL),
	(254, 40, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 11:41:26', '2022-06-15 11:41:26', NULL, '2022-06-15 11:41:26', '2022-06-15 11:41:26', NULL),
	(259, 41, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:19:27', '2022-06-15 13:19:27', NULL, '2022-06-15 13:19:27', '2022-06-15 13:19:27', NULL),
	(260, 42, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:19:59', '2022-06-15 13:19:59', NULL, '2022-06-15 13:19:59', '2022-06-15 13:19:59', NULL),
	(261, 43, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:20:33', '2022-06-15 13:20:33', NULL, '2022-06-15 13:20:33', '2022-06-15 13:20:33', NULL),
	(262, 44, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:21:14', '2022-06-15 13:21:14', NULL, '2022-06-15 13:21:14', '2022-06-15 13:21:14', NULL),
	(263, 45, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:22:11', '2022-06-15 13:22:11', NULL, '2022-06-15 13:22:11', '2022-06-15 13:22:11', NULL),
	(264, 46, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:22:29', '2022-06-15 13:22:29', NULL, '2022-06-15 13:22:29', '2022-06-15 13:22:29', NULL),
	(265, 47, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-15 13:25:27', '2022-06-15 13:25:27', NULL, '2022-06-15 13:25:27', '2022-06-15 13:25:27', NULL),
	(266, 48, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-15 13:25:41', '2022-06-15 13:25:41', NULL, '2022-06-15 13:25:41', '2022-06-15 13:25:41', NULL),
	(267, 49, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-15 13:25:56', '2022-06-15 13:25:56', NULL, '2022-06-15 13:25:56', '2022-06-15 13:25:56', NULL),
	(268, 50, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-15 13:26:09', '2022-06-15 13:26:09', NULL, '2022-06-15 13:26:09', '2022-06-15 13:26:09', NULL),
	(269, 51, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-15 13:26:21', '2022-06-15 13:26:21', NULL, '2022-06-15 13:26:21', '2022-06-15 13:26:21', NULL),
	(272, 52, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 14, NULL, 1, 0, 1, '2022-06-15 13:51:36', '2022-06-15 13:51:36', NULL, '2022-06-15 13:51:36', '2022-06-15 13:51:36', NULL),
	(292, 79, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-17 15:46:16', '2022-06-17 15:46:16', NULL, '2022-06-17 15:46:16', '2022-06-17 15:46:16', NULL),
	(293, 80, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-17 16:07:14', '2022-06-17 16:07:14', NULL, '2022-06-17 16:07:14', '2022-06-17 16:07:14', NULL),
	(294, 81, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-17 16:09:32', '2022-06-17 16:09:32', NULL, '2022-06-17 16:09:32', '2022-06-17 16:09:32', NULL),
	(295, 82, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-17 16:22:42', '2022-06-17 16:22:42', NULL, '2022-06-17 16:22:42', '2022-06-17 16:22:42', NULL),
	(301, 83, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-20 14:58:23', '2022-06-20 14:58:23', NULL, '2022-06-20 14:58:23', '2022-06-20 14:58:23', NULL),
	(303, 84, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-20 17:54:49', '2022-06-20 17:54:49', NULL, '2022-06-20 17:54:49', '2022-06-20 17:54:49', NULL),
	(311, 53, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-20 17:54:49', '2022-06-20 17:54:49', NULL, '2022-06-20 17:54:49', '2022-06-20 17:54:49', NULL),
	(312, 60, 1, NULL, 'App\\Models\\Parking', 0, 'App\\Models\\Parking', 1, NULL, 1, 0, 1, '2022-06-20 17:54:49', '2022-06-20 17:54:49', NULL, '2022-06-20 17:54:49', '2022-06-20 17:54:49', NULL);
/*!40000 ALTER TABLE `movements` ENABLE KEYS */;

/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
