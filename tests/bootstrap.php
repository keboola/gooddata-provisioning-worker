<?php

defined('TEST_DB_HOST')
|| define('TEST_DB_HOST', getenv('TEST_DB_HOST') ? getenv('TEST_DB_HOST') : null);

defined('TEST_DB_NAME')
|| define('TEST_DB_NAME', getenv('TEST_DB_NAME') ? getenv('TEST_DB_NAME') : null);

defined('TEST_DB_USER')
|| define('TEST_DB_USER', getenv('TEST_DB_USER') ? getenv('TEST_DB_USER') : null);

defined('TEST_DB_PASSWORD')
|| define('TEST_DB_PASSWORD', getenv('TEST_DB_PASSWORD') ? getenv('TEST_DB_PASSWORD') : null);

defined('TEST_DB_PORT')
|| define('TEST_DB_PORT', getenv('TEST_DB_PORT') ? getenv('TEST_DB_PORT') : null);

defined('TEST_GD_LOGIN')
|| define('TEST_GD_LOGIN', getenv('TEST_GD_LOGIN') ? getenv('TEST_GD_LOGIN') : null);

defined('TEST_GD_PASSWORD')
|| define('TEST_GD_PASSWORD', getenv('TEST_GD_PASSWORD') ? getenv('TEST_GD_PASSWORD') : null);

defined('TEST_GD_BACKEND')
|| define('TEST_GD_BACKEND', getenv('TEST_GD_BACKEND') ? getenv('TEST_GD_BACKEND') : null);

defined('TEST_GD_DOMAIN')
|| define('TEST_GD_DOMAIN', getenv('TEST_GD_DOMAIN') ? getenv('TEST_GD_DOMAIN') : null);

defined('TEST_GD_SSO_PROVIDER')
|| define('TEST_GD_SSO_PROVIDER', getenv('TEST_GD_SSO_PROVIDER') ? getenv('TEST_GD_SSO_PROVIDER') : null);

defined('TEST_GD_AUTH_TOKEN')
|| define('TEST_GD_AUTH_TOKEN', getenv('TEST_GD_AUTH_TOKEN') ? getenv('TEST_GD_AUTH_TOKEN') : null);

require_once __DIR__ . '/../vendor/autoload.php';
