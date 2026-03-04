<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pengwin_admin');

// Application Configuration
define('APP_NAME', 'Pengwin Admin');
define('APP_URL', 'http://localhost/php-admin');

// Session Configuration
session_start();

// Database Connection
function getDBConnection()
{
    try {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        return $conn;
    } catch (Exception $e) {
        die("Database connection error: " . $e->getMessage());
    }
}

// Helper function to check if user is logged in
function isLoggedIn()
{
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Helper function to redirect if not logged in
function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: ');
        exit();
    }
}

// Helper function to get current page
function getCurrentPage()
{
    $page = basename($_SERVER['PHP_SELF'], '.php');
    return $page === 'index' ? 'dashboard' : $page;
}
