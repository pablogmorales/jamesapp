<?php
require_once dirname(dirname(__DIR__)) . '/config/bootstrap.php';

// Retain prod DB credentials
putenv('DATABASE_PROD_USER=' . getenv('DATABASE_USER'));
putenv('DATABASE_PROD_PASS=' . getenv('DATABASE_PASS'));
putenv('DATABASE_PROD_HOST=' . getenv('DATABASE_HOST'));
putenv('DATABASE_PROD_NAME=' . getenv('DATABASE_NAME'));


// Replace DB credentials with test DB
putenv('DATABASE_USER=' . getenv('DATABASE_TEST_USER'));
putenv('DATABASE_PASS=' . getenv('DATABASE_TEST_PASS'));
putenv('DATABASE_HOST=' . getenv('DATABASE_TEST_HOST'));
putenv('DATABASE_NAME=' . getenv('DATABASE_TEST_NAME'));
