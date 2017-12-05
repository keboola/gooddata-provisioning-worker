<?php

defined('TEST_API_BASEURI')
|| define('TEST_API_BASEURI', getenv('TEST_API_BASEURI') ? getenv('TEST_API_BASEURI') : null);

defined('TEST_STORAGE_TOKEN')
|| define('TEST_STORAGE_TOKEN', getenv('TEST_STORAGE_TOKEN') ? getenv('TEST_STORAGE_TOKEN') : null);

defined('TEST_MANAGE_TOKEN')
|| define('TEST_MANAGE_TOKEN', getenv('TEST_MANAGE_TOKEN') ? getenv('TEST_MANAGE_TOKEN') : null);

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
