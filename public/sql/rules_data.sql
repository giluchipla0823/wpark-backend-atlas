INSERT INTO `rules` (`id`, `name`, `countdown`, `priority`, `is_group`, `final_position`, `predefined_zone_id`, `carrier_id`, `active`, `created_at`, `updated_at`, `deleted_at`)
VALUES
(1, 'VEHÍCULOS_ROJOS_Y_AZULES', 0, 2, 0, 0, NULL, NULL, 1, '2022-06-05 09:09:33', '2022-06-05 09:09:33', NULL),
(2, 'DESTINO_TARRAGONA', 0, 1, 0, 1, NULL, NULL, 0, '2022-06-05 09:09:33', '2022-06-05 09:09:33', NULL),
(3, 'REGLA ESPIGA 001', 0, 1, 0, 1, NULL, NULL, 0, '2022-06-13 16:14:03', '2022-06-13 16:14:04', NULL),
(4, 'REGLA ESPIGA 002', 0, 1, 0, 1, NULL, NULL, 0, '2022-06-13 16:14:03', '2022-06-13 16:14:04', NULL),
(6, 'REGLA DEST. HAGEN', NULL, 1, 0, 1, NULL, 27, 1, '2022-06-14 19:05:04', '2022-06-14 19:05:04', NULL),
(7, 'REGLA DEST. COMP. IILLIGEN', NULL, 2, 0, 1, NULL, 27, 1, '2022-06-14 19:06:06', '2022-06-14 19:06:06', NULL),
(8, 'REGLA SETE COMPOUND', NULL, 3, 0, 1, NULL, 27, 1, '2022-06-14 19:06:49', '2022-06-14 19:06:49', NULL),
(9, 'REGLA DEST. CODE(28,29,30)', NULL, NULL, 1, 0, NULL, NULL, 1, '2022-06-14 19:08:13', '2022-06-14 19:08:13', NULL),
(11, 'dqqwd', NULL, 1, 0, 1, NULL, 27, 1, '2022-06-17 11:16:34', '2022-06-17 11:16:40', '2022-06-17 11:16:40');

INSERT INTO `rules_blocks` (`id`, `rule_id`, `block_id`, `created_at`, `updated_at`, `deleted_at`)
VALUES
(1, 1, 1, '2022-06-05 09:09:36', '2022-06-05 09:09:36', NULL),
(2, 2, 2, '2022-06-05 09:09:37', '2022-06-05 09:09:37', NULL),
(3, 3, 1, '2022-06-13 16:15:45', '2022-06-13 16:15:45', NULL),
(4, 4, 1, '2022-06-13 16:15:45', '2022-06-13 16:15:45', NULL),
(5, 4, 3, '2022-06-13 16:15:45', '2022-06-13 16:15:45', NULL),
(6, 6, 1, '2022-06-14 19:05:04', '2022-06-14 19:05:04', NULL),
(7, 6, 2, '2022-06-14 19:05:04', '2022-06-14 19:05:04', NULL),
(8, 7, 1, '2022-06-14 19:06:06', '2022-06-14 19:06:06', NULL),
(9, 7, 2, '2022-06-14 19:06:06', '2022-06-14 19:06:06', NULL),
(10, 8, 1, '2022-06-14 19:06:49', '2022-06-14 19:06:49', NULL),
(11, 8, 2, '2022-06-14 19:06:49', '2022-06-14 19:06:49', NULL),
(12, 9, 1, '2022-06-14 19:08:13', '2022-06-14 19:08:13', NULL),
(13, 3, 3, '2022-06-13 16:15:45', '2022-06-13 16:15:45', NULL),
(16, 11, 1, '2022-06-17 11:16:34', '2022-06-17 11:16:40', '2022-06-17 11:16:40'),
(17, 11, 2, '2022-06-17 11:16:34', '2022-06-17 11:16:40', '2022-06-17 11:16:40');

INSERT INTO `rules_conditions` (`id`, `rule_id`, `condition_id`, `conditionable_type`, `conditionable_id`, `created_at`, `updated_at`, `deleted_at`)
VALUES
(1, 1, 5, 'App\\Models\\DestinationCode', 1, '2022-06-05 09:09:35', '2022-06-05 09:09:35', NULL),
(2, 1, 4, 'App\\Models\\Color', 1, '2022-06-05 09:09:35', '2022-06-05 09:09:35', NULL),
(3, 1, 4, 'App\\Models\\Color', 107, '2022-06-05 09:09:35', '2022-06-05 09:09:35', NULL),
(4, 2, 5, 'App\\Models\\DestinationCode', 6, '2022-06-05 09:09:36', '2022-06-05 09:09:36', NULL),
(5, 2, 5, 'App\\Models\\DestinationCode', 11, '2022-06-05 09:09:36', '2022-06-05 09:09:36', NULL),
(6, 1, 4, 'App\\Models\\Color', 170, '2022-06-05 09:09:35', '2022-06-05 09:09:35', NULL),
(7, 2, 5, 'App\\Models\\DestinationCode', 5, '2022-06-05 09:09:36', '2022-06-05 09:09:36', NULL),
(8, 3, 5, 'App\\Models\\DestinationCode', 334, '2022-06-13 16:19:10', '2022-06-13 16:19:11', NULL),
(9, 6, 5, 'App\\Models\\DestinationCode', 28, '2022-06-14 19:05:04', '2022-06-14 19:05:04', NULL),
(10, 7, 5, 'App\\Models\\DestinationCode', 29, '2022-06-14 19:06:06', '2022-06-14 19:06:06', NULL),
(11, 8, 5, 'App\\Models\\DestinationCode', 30, '2022-06-14 19:06:49', '2022-06-14 19:06:49', NULL),
(13, 11, 1, 'App\\Models\\Vehicle', 1, '2022-06-17 11:16:34', '2022-06-17 11:16:40', '2022-06-17 11:16:40');

INSERT INTO `rules_groups` (`id`, `parent_rule_id`, `child_rule_id`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 9, 6, '2022-06-14 19:08:13', '2022-06-14 19:08:13', NULL),
(2, 9, 7, '2022-06-14 19:08:13', '2022-06-14 19:08:13', NULL),
(3, 9, 8, '2022-06-14 19:08:13', '2022-06-14 19:08:13', NULL);