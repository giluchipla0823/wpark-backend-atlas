INSERT INTO `notifications` (`id`, `sender_id`, `recipient_id`, `type`, `resourceable_type`, `resourceable_id`, `reference_code`, `data`, `reat_at`, `created_at`, `updated_at`, `deleted_at`)
VALUES
(1, 2, NULL, 'App\\Events\\CompletedRowNotification', 'App\\Models\\Row', 1, 'ROW_COMPLETED', '{"title":"Fila completada","message":"Se ha completado la fila ZP1.001","item":{"id":1,"name":"ZP1.001"}}', NULL, '2022-06-10 10:35:32', '2022-06-10 10:35:32', NULL),
(2, 1, NULL, 'App\\Events\\CompletedRowNotification', 'App\\Models\\Row', 6, 'ROW_COMPLETED', '{"title":"Fila completada","message":"Se ha completado la fila PU1.001","item":{"id":6,"name":"PU1.001"}}', NULL, '2022-06-21 08:42:19', '2022-06-21 08:42:19', NULL);
