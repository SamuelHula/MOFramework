<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
   header("Location: ../admin_signin.php?error=" . urlencode("Invalid request method"));
   exit;
}

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
   header("Location: ../admin_signin.php?error=" . urlencode("Please fill in all fields"));
   exit;
}

if (!isset($_SESSION['login_attempts'])) {
   $_SESSION['login_attempts'] = 0;
   $_SESSION['last_attempt'] = time();
}

if ($_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_attempt']) < 900) {
   header("Location: ../admin_signin.php?error=" . urlencode("Too many login attempts. Please wait 15 minutes."));
   exit;
}

try {
   $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? AND is_active = 1");
   $stmt->execute([$email]);
   $admin = $stmt->fetch(PDO::FETCH_ASSOC);
   
   if ($admin && password_verify($password, $admin['password'])) {
      $_SESSION['login_attempts'] = 0;
      $_SESSION['last_attempt'] = time();
      
      $_SESSION['admin_loggedin'] = true;
      $_SESSION['admin_id'] = $admin['id'];
      $_SESSION['admin_email'] = $admin['email'];
      $_SESSION['admin_name'] = $admin['name'];
      $_SESSION['admin_role'] = $admin['role'];
      
      header("Location: ../admin_dashboard.php");
      exit;
   } else {
      $_SESSION['login_attempts']++;
      $_SESSION['last_attempt'] = time();
      
      header("Location: ../admin_signin.php?error=" . urlencode("Invalid email or password"));
      exit;
   }
} catch (PDOException $e) {
   error_log("Login error: " . $e->getMessage());
   header("Location: ../admin_signin.php?error=" . urlencode("Database error. Please try again later."));
   exit;
}
?>