<?php
// assets/process_delete_user.php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if (isset($_GET['id'])) {
   $userId = intval($_GET['id']);
   
   try {
      // Get user info for logging
      $stmt = $pdo->prepare("SELECT first_name, last_name, email FROM users WHERE id = ?");
      $stmt->execute([$userId]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if (!$user) {
         header("Location: ../manage_users.php?error=User+not+found");
         exit;
      }
      
      // Hard delete (since we don't have soft delete columns)
      $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
      $stmt->execute([$userId]);
      
      // Log the activity
      logAdminActivity($_SESSION['admin_id'], 'delete_user', 
         "Deleted user ID: $userId (" . $user['first_name'] . ' ' . $user['last_name'] . ' - ' . $user['email'] . ")");
      
      header("Location: ../manage_users.php?success=User+deleted+successfully");
      exit;
      
   } catch (PDOException $e) {
      error_log("Failed to delete user: " . $e->getMessage());
      header("Location: ../manage_users.php?error=Database+error");
      exit;
   }
} else {
   header("Location: ../manage_users.php");
   exit;
}