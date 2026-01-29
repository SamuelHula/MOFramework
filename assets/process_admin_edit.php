<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once __DIR__ . '/config.php';

if (session_status() === PHP_SESSION_NONE) {
   die("Session not started");
}

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   die("Not logged in as admin");
}

if (!isset($_SESSION['admin_role']) || $_SESSION['admin_role'] !== 'super_admin') {
   die("Not authorized - need super_admin role");
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   die("Invalid request method");
}

$admin_id = filter_input(INPUT_POST, 'admin_id', FILTER_VALIDATE_INT);
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');
$email = trim($_POST['email'] ?? '');
$role = $_POST['role'] ?? 'admin';

$errors = [];
if (!$admin_id || $admin_id <= 0) {
   $errors[] = "Invalid admin ID";
}
if (empty($first_name)) {
   $errors[] = "First name is required";
}
if (empty($last_name)) {
   $errors[] = "Last name is required";
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
   $errors[] = "Valid email is required";
}
if (!in_array($role, ['super_admin', 'admin', 'moderator'])) {
   $errors[] = "Invalid role";
}

if (empty($errors)) {
   try {
      $stmt = $pdo->prepare("SELECT id FROM admins WHERE email = ? AND id != ?");
      $stmt->execute([$email, $admin_id]);
      if ($stmt->fetch()) {
         $errors[] = "Email already exists for another admin";
      }
   } catch (PDOException $e) {
      die("Database error checking email: " . $e->getMessage());
   }
}

if (!empty($errors)) {
   $error_string = urlencode(implode("|", $errors));
   header("Location: ../manage_admin.php?action=edit&id=$admin_id&error=$error_string");
   exit;
}

try {
   $sql = "UPDATE admins SET 
         first_name = :first_name, 
         last_name = :last_name, 
         email = :email, 
         role = :role 
         WHERE id = :id";
   
   $stmt = $pdo->prepare($sql);
   $result = $stmt->execute([
      ':first_name' => $first_name,
      ':last_name' => $last_name,
      ':email' => $email,
      ':role' => $role,
      ':id' => $admin_id
   ]);
   
   if ($result) {
      if (function_exists('logAdminActivity') && isset($_SESSION['admin_id'])) {
         logAdminActivity($_SESSION['admin_id'], 'update_admin', "Updated admin ID: $admin_id");
      }
      
      header("Location: ../manage_admin.php?success=Admin+updated+successfully");
      exit;
   } else {
      header("Location: ../manage_admin.php?action=edit&id=$admin_id&error=Update+failed");
      exit;
   }
   
} catch (PDOException $e) {
   error_log("Database error updating admin: " . $e->getMessage());
   $error_msg = urlencode("Database error: " . $e->getMessage());
   header("Location: ../manage_admin.php?action=edit&id=$admin_id&error=$error_msg");
   exit;
}
?>