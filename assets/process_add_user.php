<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $firstName = trim($_POST['first_name']);
   $lastName = trim($_POST['last_name']);
   $email = trim($_POST['email']);
   $password = $_POST['password'];
   $confirmPassword = $_POST['confirm_password'];
   $role = $_POST['role'] ?? 'user';
   
   $errors = [];
   
   if (empty($firstName) || empty($lastName) || empty($email) || empty($password)) {
      $errors[] = 'All required fields must be filled';
   }
   
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email format';
   }
   
   try {
      $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
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
   
   if ($password !== $confirmPassword) {
      $errors[] = 'Passwords do not match';
   }
   
   $allowedRoles = ['user', 'premium'];
   if (!in_array($role, $allowedRoles)) {
      $errors[] = 'Invalid role selected';
   }
   
   if (empty($errors)) {
      try {
         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
         
         $stmt = $pdo->prepare("
               INSERT INTO users 
               (first_name, last_name, email, password, role, created_at) 
               VALUES (?, ?, ?, ?, ?, NOW())
         ");
         
         $stmt->execute([
               $firstName, 
               $lastName, 
               $email, 
               $hashedPassword, 
               $role
         ]);
         
         $userId = $pdo->lastInsertId();
         
         logAdminActivity($_SESSION['admin_id'], 'create_user', "Created new user: $firstName $lastName ($email)");
         
         header("Location: ../manage_users.php?success=User+created+successfully");
         exit;
         
      } catch (PDOException $e) {
         $errors[] = 'Database error: ' . $e->getMessage();
      }
   }
   
   if (!empty($errors)) {
      $errorString = implode('|', $errors);
      header("Location: ../add_user.php?error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../manage_users.php");
   exit;
}