<?php
require_once 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   $email = filter_var(trim($_POST["email"]), FILTER_SANITIZE_EMAIL);

   // Validate input
   $errors = [];

   if (empty($email)) {
      $errors[] = "Email is required";
   }

   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = "Invalid email format";
   }

   if (empty($errors)) {
      try {
         $stmt = $pdo->prepare("SELECT id, first_name FROM users WHERE email = ?");
         $stmt->execute([$email]);
         
         if ($stmt->rowCount() === 1) {
               $user = $stmt->fetch(PDO::FETCH_ASSOC);
               
               // Generate reset token (in a real app, you'd send an email)
               $reset_token = bin2hex(random_bytes(32));
               $reset_expires = date('Y-m-d H:i:s', strtotime('+1 hour'));
               
               // Store token in database (in a real app)
               $updateStmt = $pdo->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
               $updateStmt->execute([$reset_token, $reset_expires, $user['id']]);
               
               // Log the reset request
               $log_entry = "=== PASSWORD RESET REQUEST ===" . PHP_EOL;
               $log_entry .= "Time: " . date('Y-m-d H:i:s') . PHP_EOL;
               $log_entry .= "User ID: " . $user['id'] . PHP_EOL;
               $log_entry .= "Email: " . $email . PHP_EOL;
               $log_entry .= "Reset Token: " . $reset_token . PHP_EOL;
               $log_entry .= "IP: " . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
               $log_entry .= "=================================" . PHP_EOL . PHP_EOL;
               
               file_put_contents('./assets/logs/auth.log', $log_entry, FILE_APPEND | LOCK_EX);
               
               // In a real application, you would send an email here
               // For this demo, we'll just show a success message
               header("Location: ../forgot_password.php?success=Password+reset+link+has+been+sent+to+your+email");
               exit;
         } else {
               $errors[] = "No account found with that email";
         }
      } catch (PDOException $e) {
         error_log("Database error: " . $e->getMessage());
         $errors[] = "Reset request failed due to database error";
      }
   }

   // If there are errors, redirect back with error messages
   if (!empty($errors)) {
      $errorString = implode("|", $errors);
      header("Location: ../forgot_password.php?error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../error.php?code=403&message=Invalid+request+method");
   exit;
}
?>