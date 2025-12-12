<?php
// assets/process_edit_user.php
session_start();
require_once 'config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: ../admin_signin.php");
   exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $userId = intval($_POST['user_id']);
   $firstName = trim($_POST['first_name']);
   $lastName = trim($_POST['last_name']);
   $email = trim($_POST['email']);
   $password = $_POST['password'];
   $confirmPassword = $_POST['confirm_password'];
   $role = $_POST['role'] ?? 'user';
   
   $errors = [];
   
   // Validate required fields
   if (empty($firstName) || empty($lastName) || empty($email)) {
      $errors[] = 'All required fields must be filled';
   }
   
   // Validate email
   if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $errors[] = 'Invalid email format';
   }
   
   // Check if email already exists (excluding current user)
   try {
      $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
      $stmt->execute([$email, $userId]);
      if ($stmt->fetch()) {
         $errors[] = 'Email already registered by another user';
      }
   } catch (PDOException $e) {
      $errors[] = 'Database error: ' . $e->getMessage();
   }
   
   // Validate password if provided
   if (!empty($password)) {
      if (strlen($password) < 8) {
         $errors[] = 'Password must be at least 8 characters long';
      }
      
      if ($password !== $confirmPassword) {
         $errors[] = 'Passwords do not match';
      }
   }
   
   // Validate role
   $allowedRoles = ['user', 'premium'];
   if (!in_array($role, $allowedRoles)) {
      $errors[] = 'Invalid role selected';
   }
   
   // If no errors, update user account
   if (empty($errors)) {
      try {
         // Prepare base query
         $query = "UPDATE users SET 
                     first_name = ?, 
                     last_name = ?, 
                     email = ?, 
                     role = ?,
                     updated_at = NOW()";
         
         $params = [
               $firstName, 
               $lastName, 
               $email, 
               $role
         ];
         
         // Add password update if provided
         if (!empty($password)) {
               $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
               $query .= ", password = ?";
               $params[] = $hashedPassword;
         }
         
         $query .= " WHERE id = ?";
         $params[] = $userId;
         
         $stmt = $pdo->prepare($query);
         $stmt->execute($params);
         
         // Log the activity
         logAdminActivity($_SESSION['admin_id'], 'update_user', "Updated user ID: $userId ($firstName $lastName)");
         
         header("Location: ../edit_user.php?id=$userId&success=User+updated+successfully");
         exit;
         
      } catch (PDOException $e) {
         $errors[] = 'Database error: ' . $e->getMessage();
      }
   }
   
   // If there are errors, redirect back with error messages
   if (!empty($errors)) {
      $errorString = implode('|', $errors);
      header("Location: ../edit_user.php?id=$userId&error=" . urlencode($errorString));
      exit;
   }
} else {
   header("Location: ../manage_users.php");
   exit;
}