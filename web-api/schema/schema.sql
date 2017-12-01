--
-- Table structure for table `api_usage`
--

CREATE TABLE IF NOT EXISTS `api_usage` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(15) CHARACTER SET latin1 NOT NULL,
  `module` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` date NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usage_lookup` (`ip`,`module`,`created`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_tokens`
--

CREATE TABLE IF NOT EXISTS `auth_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token` varchar(255) CHARACTER SET latin1 NOT NULL,
  `secret` varchar(40) COLLATE utf8_unicode_ci NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `description` text CHARACTER SET latin1 NOT NULL,
  `can_view_errors` tinyint(1) NOT NULL,
  `can_import_data` tinyint(1) NOT NULL,
  `rate_limited` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_token_usage`
--

CREATE TABLE IF NOT EXISTS `auth_token_usage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `token_id` int(10) unsigned NOT NULL,
  `module` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `other` longtext COLLATE utf8_unicode_ci NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `created` (`created`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `endpoint_failures`
--

CREATE TABLE IF NOT EXISTS `endpoint_failures` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `module_name` varchar(50) CHARACTER SET latin1 DEFAULT NULL,
  `last_failed` int(11) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parsed_data_keys`
--

CREATE TABLE IF NOT EXISTS `parsed_data_keys` (
  `parsed_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `module` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`parsed_id`),
  KEY `key` (`key`,`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `parsed_data_values`
--

CREATE TABLE IF NOT EXISTS `parsed_data_values` (
  `parsed_id` int(11) NOT NULL,
  `data` longblob NOT NULL,
  PRIMARY KEY (`parsed_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proxy_servers`
--

CREATE TABLE IF NOT EXISTS `proxy_servers` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) CHARACTER SET latin1 NOT NULL,
  `enabled` tinyint(1) NOT NULL DEFAULT '1',
  `socket_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `IP` varchar(255) CHARACTER SET latin1 NOT NULL,
  `port` int(11) NOT NULL,
  `username` varchar(255) CHARACTER SET latin1 NOT NULL,
  `password` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`),
  KEY `path` (`path`),
  KEY `enabled` (`enabled`),
  KEY `socket_enabled` (`socket_enabled`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `proxy_server_usage`
--

CREATE TABLE IF NOT EXISTS `proxy_server_usage` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `proxy_server_id` int(10) unsigned NOT NULL,
  `module_identifier` varchar(255) CHARACTER SET latin1 NOT NULL,
  `other_identifier` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status_code` int(11) DEFAULT NULL,
  `module_detected_error` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usage` (`proxy_server_id`,`module_identifier`,`created`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raw_data_keys`
--

CREATE TABLE IF NOT EXISTS `raw_data_keys` (
  `raw_id` int(11) NOT NULL AUTO_INCREMENT,
  `key` varchar(255) CHARACTER SET latin1 NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `module` varchar(255) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`raw_id`),
  KEY `key` (`key`,`created`),
  KEY `created` (`created`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `raw_data_values`
--

CREATE TABLE IF NOT EXISTS `raw_data_values` (
  `raw_id` int(11) NOT NULL,
  `request` blob,
  `data` longblob,
  `gzipped_data` longblob,
  PRIMARY KEY (`raw_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usage_log`
--

CREATE TABLE IF NOT EXISTS `usage_log` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `created` datetime NOT NULL,
  `var_name` varchar(255) CHARACTER SET latin1 NOT NULL,
  `contents` blob NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;