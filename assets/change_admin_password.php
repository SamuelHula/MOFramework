<?php
// assets/change_admin_password.php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $currentPassword = $_POST['current_password'];
   $newPassword = $_POST['new_password'];
   $confirmPassword = $_POST['confirm_password'];
   
   $errors = [];
   
   // Validate required fields
   if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
      $errors[] = 'All fields are required';
   }
   
   // Check if new passwords match
   if ($newPassword !== $confirmPassword) {
      $errors[] = 'New passwords do not match';
   }
   
   // Validate new password strength
   if (strlen($newPassword) < 8) {
      $errors[] = 'New password must be at least 8 characters long';
   }
   
   if (empty($errors)) {
      try {
         // Get current admin data
         $stmt = $pdo->prepare("SELECT password FROM admins WHERE id = ?");
         $stmt->execute([$_SESSION['admin_id']]);
         $admin = $stmt->fetch(PDO::FETCH_ASSOC);
         
         if ($admin && password_verify($currentPassword, $admin['password'])) {
               // Update password
               $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
               $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
               $stmt->execute([$hashedPassword, $_SESSION['admin_id']]);
               
               // Log the activity
               logAdminActivity($_SESSION['admin_id'], 'change_password', 'Admin changed password');
               
               header("Location: ../admin_dashboard.php?success=Password+changed+successfully");
               exit;
         } else {
               $errors[] = 'Current password is incorrect';
         }
         
      } catch (PDOException $e) {
         $errors[] = 'Database error: ' . $e->getMessage();
      }
   }
   
   // If there are errors, redirect back with error messages
   if (!empty($errors)) {
      $errorString = implode('|', $errors);
      header("Location: ../admin_dashboard.php?error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../admin_dashboard.php");
   exit;
}
?>