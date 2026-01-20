<?php
// assets/process_add_admin.php
session_start();
require_once 'config.php';

// Check if admin is logged in and is super_admin
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

// Only super_admin can create admins
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
   
   // Validate required fields
   if (empty($firstName) || empty($lastName) || empty($email) || empty($password) || empty($role)) {
      $errors[] = 'All fields are required';
   }
   
   // Validate email
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email format';
   }
   
   // Check if email already exists
   try {
      $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ?");
      $stmt->execute([$email]);
      if ($stmt->fetch()) {
         $errors[] = 'Email already registered';
      }
   } catch (PDOException $e) {
      $errors[] = 'Database error: ' . $e->getMessage();
   }
   
   // Validate password
   if (strlen($password) < 8) {
      $errors[] = 'Password must be at least 8 characters long';
   }
   
   // Validate role
   if (!in_array($role, ['admin', 'moderator'])) {
      $errors[] = 'Invalid role selected';
   }
   
   // If no errors, create admin account
   if (empty($errors)) {
      try {
         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
         $createdBy = $_SESSION['admin_id'];
         
         $stmt = $pdo->prepare("INSERT INTO admins (first_name, last_name, email, password, role, created_by) VALUES (?, ?, ?, ?, ?, ?)");
         $stmt->execute([$firstName, $lastName, $email, $hashedPassword, $role, $createdBy]);
         
         // Log the activity
         logAdminActivity($_SESSION['admin_id'], 'create_admin', "Created new admin: $firstName $lastName ($role)");
         
         header("Location: ../admin_dashboard.php?success=Admin+created+successfully");
         exit;
         
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