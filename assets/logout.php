<?php
require_once 'config.php';

if (isset($_SESSION['user_id'])) {
      $log_entry = "=== USER LOGOUT ===" . PHP_EOL;
      $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
      $log_entry .= "User ID: " . $_SESSION['user_id'] . PHP_EOL;
      $log_entry .= "Email: " . $_SESSION['user_email'] . PHP_EOL;
      $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
      $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
      
      file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
}

$_SESSION = array();

session_destroy();

setcookie('remember_me', '', time() - 3600, '/');

header("Location: ../index.php");
exit;
?>