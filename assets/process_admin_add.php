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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $firstName = trim($_POST['first_name']);
   $lastName = trim($_POST['last_name']);
   $email = trim($_POST['email']);
   $password = $_POST['password'];
   $role = trim($_POST['role']);
   
   $errors = [];
   
   if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($role)) {
      $errors[] = 'All fields are required';
   }
   
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email format';
   }
   
   try {
      $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
         $errors[] = 'Email already registered';
      }
   } catch (PDOException $e) {
      $errors[] = 'Database error: ' . $e->getMessage();
   }
   
   if (strlen($password) < 8) {
      $errors[] = 'Password must be at least 8 characters long';
   }
   
   if (!in_array($role, ['admin', 'moderator'])) {
      $errors[] = 'Invalid role selected';
   }
   
   if (empty($errors)) {
      try {
         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
         $createdBy = $_SESSION['admin_id'];
         
         $stmt = $pdo->prepare("INSERT INTO admins (first_name, last_name, email, password, role, created_by) VALUES (?, ?, ?, ?, ?, ?)");
         $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $role, $createdBy]);
         
         logAdminActivity($_SESSION['admin_id'], 'create_admin', "Created new admin: $firstName $lastName ($role)");
         
         header("Location: ../admin_dashboard.php?success=Admin+created+successfully");
         exit;
         
      } catch (PDOException $e) {
         $errors[] = 'Database error: ' . $e->getMessage();
      }
   }
   
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