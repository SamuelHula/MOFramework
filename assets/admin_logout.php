<?php
// assets/admin_logout.php
session_start();
require_once 'config.php';

if (isset($_SESSION['admin_id'])) {
   // Log the logout activity
   logAdminActivity($_SESSION['admin_id'], 'logout', 'Admin logged out');
}

// Unset all admin session variables
unset($_SESSION['admin_loggedin']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);
unset($_SESSION['admin_first_name']);

// Destroy the session
session_destroy();

// Redirect to admin signin page
header("Location: ../admin_signin.php?logout=1");
exit;
?>