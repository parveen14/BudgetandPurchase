<?php 
defined('APPLICATION_ROOT')
|| define('APPLICATION_ROOT', realpath(__DIR__ . '/../'));

defined('BASE_URL')
|| define('BASE_URL', 'http://budgetandpurchase.local.com/');

defined('PUBLIC_PATH')
|| define('PUBLIC_PATH', null);

defined('PROJECT_NAME')
|| define('PROJECT_NAME', 'http://budgetandpurchase.local.com/');
ini_set('display_errors', true);

/**
 * This makes our life easier when dealing with paths. Everything is relative
 * to the application root now.
 */
chdir(dirname(__DIR__));

// Decline static file requests back to the PHP built-in webserver
if (php_sapi_name() === 'cli-server' && is_file(__DIR__ . parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH))) {
    return false;
}

// Setup autoloading
require 'init_autoloader.php';

if (!defined('APPLICATION_PATH')) {
    define('APPLICATION_PATH', realpath(__DIR__ . '/../'));
}
$appConfig = include APPLICATION_PATH . '/config/application.config.php';
if (file_exists(APPLICATION_PATH . '/config/development.config.php')) {
    $appConfig = Zend\Stdlib\ArrayUtils::merge($appConfig, include APPLICATION_PATH . '/config/development.config.php');
}
// Run the application!
Zend\Mvc\Application::init($appConfig)->run();
