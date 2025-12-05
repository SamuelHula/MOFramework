<?php
require_once 'config.php';

// Log the logout
if (isset($_SESSION['user_id'])) {
      $log_entry = "=== USER LOGOUT ===" . PHP_EOL;
      $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
      $log_entry .= "User ID: " . $_SESSION['user_id'] . PHP_EOL;
      $log_entry .= "Email: " . $_SESSION['user_email'] . PHP_EOL;
      $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
      $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
      
      file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
}

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Delete the remember me cookie
setcookie('remember_me', '', time() - 3600, '/');

// Redirect to home page
header("Location: ../index.php");
exit;
?>