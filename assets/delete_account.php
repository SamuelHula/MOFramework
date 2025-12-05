<?php
require_once 'config.php';

// Check if user is logged in (session already started by config.php)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: ../signin.php");
   exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
   try {
      // Log the account deletion attempt
      $log_entry = "=== ACCOUNT DELETION ===" . PHP_EOL;
      $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
      $log_entry .= "User ID: " . $_SESSION['user_id'] . PHP_EOL;
      $log_entry .= "Email: " . $_SESSION['user_email'] . PHP_EOL;
      $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
      $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
      
      file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
      
      // Delete user from database
      $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
      $stmt->execute([$_SESSION['user_id']]);
      
      // Unset all session variables
      $_SESSION = array();

      // Destroy the session
      session_destroy();

      // Delete the remember me cookie
      setcookie('remember_me', '', time() - 3600, '/');

      // Redirect to home page with success message
      header("Location: ../index.php?success=Your+account+has+been+deleted");
      exit;
      
   } catch (PDOException $e) {
      error_log("Database error during account deletion: " . $e->getMessage());
      header("Location: ../account.php?error=Account+deletion+failed");
      exit;
   }
} else {
   header("Location: ../error.php?code=403&message=Invalid+request+method");
   exit;
}
?>