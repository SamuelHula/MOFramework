<?php
session_start();
require_once 'config.php';

if (isset($_SESSION['admin_id'])) {
   logAdminActivity($_SESSION['admin_id'], 'logout', 'Admin logged out');
}

unset($_SESSION['admin_loggedin']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_email']);
unset($_SESSION['admin_name']);
unset($_SESSION['admin_role']);
unset($_SESSION['admin_first_name']);

session_destroy();

header("Location: ../admin_signin.php?logout=1");
exit;
?>