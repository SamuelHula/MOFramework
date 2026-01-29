<?php
session_start();
require_once 'config.php';

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
      $placeholders = implode(',', array_fill(0, count($userIds), '?'));
      
      $stmt = $pdo->prepare("SELECT id, first_name, last_name, email FROM users WHERE id IN ($placeholders)");
      $stmt->execute($userIds);
      $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
      
      $stmt = $pdo->prepare("DELETE FROM users WHERE id IN ($placeholders)");
      $stmt->execute($userIds);
      
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