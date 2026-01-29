<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if ($_SESSION['admin_role'] !== 'super_admin') {
   header("Location: ../admin_dashboard.php?error=unauthorized");
   exit;
}

if (isset($_GET['id'])) {
   $adminId = intval($_GET['id']);
   
   if ($adminId === $_SESSION['admin_id']) {
      header("Location: ../admin_dashboard.php?error=Cannot+delete+your+own+account");
      exit;
   }
   
   try {
      $stmt = $pdo->prepare("SELECT role FROM admins WHERE id = ?");
      $stmt->execute([$adminId]);
      $admin = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($admin && $admin['role'] === 'super_admin') {
         header("Location: ../admin_dashboard.php?error=Cannot+delete+super+admin");
         exit;
      }
      
      $stmt = $pdo->prepare("UPDATE admins SET is_active = 0 WHERE id = ?");
      $stmt->execute([$adminId]);
      
      logAdminActivity($_SESSION['admin_id'], 'delete_admin', "Deactivated admin account ID: $adminId");
      
      header("Location: ../admin_dashboard.php?success=Admin+deactivated+successfully");
      exit;
      
   } catch (PDOException $e) {
      header("Location: ../admin_dashboard.php?error=Database+error");
      exit;
   }
} else {
   header("Location: ../admin_dashboard.php");
   exit;
}
?>