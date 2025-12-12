<?php
// assets/process_bulk_delete_users.php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if (isset($_GET['ids'])) {
   $userIds = explode(',', $_GET['ids']);
   $userIds = array_map('intval', $userIds);
   $userIds = array_filter($userIds);
   
   if (empty($userIds)) {
      header("Location: ../manage_users.php?error=No+users+selected");
      exit;
   }
   
   try {
      // Create placeholders for the query
      $placeholders = implode(',', array_fill(0, count($userIds), '?'));
      
      // Get user info for logging
      $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE id IN ($placeholders)");
      $stmt->execute($userIds);
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      // Hard delete all selected users
      $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");
      $stmt->execute($userIds);
      
      // Log the activity
      $userNames = array_map(function($user) {
         return $user['first_name'] . ' ' . $user['last_name'];
      }, $users);
      
      logAdminActivity($_SESSION['admin_id'], 'bulk_delete_users', 
         "Bulk deleted " . count($userIds) . " users: " . implode(', ', $userNames));
      
      header("Location: ../manage_users.php?success=" . count($userIds) . "+users+deleted+successfully");
      exit;
      
   } catch (PDOException $e) {
      error_log("Failed to bulk delete users: " . $e->getMessage());
      header("Location: ../manage_users.php?error=Database+error");
      exit;
   }
} else {
   header("Location: ../manage_users.php");
   exit;
}