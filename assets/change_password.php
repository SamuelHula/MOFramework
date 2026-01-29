<?php
require_once 'config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: ../signin.php");
   exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $current_password = trim($_POST["current_password"]);
   $new_password = trim($_POST["new_password"]);
   $confirm_password = trim($_POST["confirm_password"]);

   $errors = [];

   if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
      $errors[] = "All fields are required";
   }

   if ($new_password !== $confirm_password) {
      $errors[] = "New passwords do not match";
   }

   if (strlen($new_password) < 8) {
      $errors[] = "New password must be at least 8 characters long";
   }

   if (empty($errors)) {
      try {
         $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
         $stmt->execute([$_SESSION['user_id']]);
         
         if ($stmt->rowCount() === 1) {
               $user = $stmt->fetch(PDO::FETCH_ASSOC);
               
               if (password_verify($current_password, $user['password'])) {
                  $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                  
                  $updateStmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                  $updateStmt->execute([$hashedPassword, $_SESSION['user_id']]);
                  
                  $log_entry = "=== PASSWORD CHANGED ===" . PHP_EOL;
                  $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
                  $log_entry .= "User ID: " . $_SESSION['user_id'] . PHP_EOL;
                  $log_entry .= "Email: " . $_SESSION['user_email'] . PHP_EOL;
                  $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
                  $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
                  
                  file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
                  
                  header("Location: ../account.php?success=Password+changed+successfully");
                  exit;
               } else {
                  $errors[] = "Current password is incorrect";
               }
         } else {
               $errors[] = "User not found";
         }
      } catch (PDOException $e) {
         error_log("Database error: " . $e->getMessage());
         $errors[] = "Password change failed due to database error";
      }
   }

   if (!empty($errors)) {
      $errorString = implode("|", $errors);
      header("Location: ../account.php?error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../error.php?code=403&message=Invalid+request+method");
   exit;
}
?>