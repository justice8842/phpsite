<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';
require_once 'lib/Database.php';
require_once 'Router.php';
require_once 'auth.php';
require_once 'helpers.php';

$router = new Router();

// Home redirect
$router->addRoute('', function() {
    if (isLoggedIn()) {
        header('Location: ' . base_url('dashboard'));
    } else {
        header('Location: ' . base_url('login'));
    }
    exit();
}, 'GET');

// Authentication routes
$router->addRoute('login', 'AuthController@showLogin', 'GET');
$router->addRoute('login', 'AuthController@login', 'POST');
$router->addRoute('logout', 'AuthController@logout', 'GET');
$router->addRoute('dashboard', 'AuthController@showDashboard', 'GET');
$router->addRoute('unauthorized', 'AuthController@unauthorized', 'GET');

// Role-specific routes
$router->addRoute('admin/dashboard', 'AdminController@dashboard', 'GET');
$router->addRoute('manager/dashboard', 'ManagerController@dashboard', 'GET');
$router->addRoute('supervisor/dashboard', 'SupervisorController@dashboard', 'GET');
$router->addRoute('employee/dashboard', 'EmployeeController@dashboard', 'GET');

// Get requested URL
$requestUrl = $_GET['url'] ?? '';

// Dispatch the request
$router->dispatch($requestUrl);