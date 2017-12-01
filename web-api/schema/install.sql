-- Set the default test token
INSERT INTO `auth_tokens` (`id`, `token`, `secret`, `enabled`, `description`, `can_view_errors`, `can_import_data`, `rate_limited`) VALUES
(9, 'test12320090810', '6b53668dbf9e2b8548cf5a44aa19e8ab90a8bcc5', 1, 'Test Token', 1, 1, 1);