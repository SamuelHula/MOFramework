<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'codelibrary');
define('DB_USER', 'root');
define('DB_PASS', 'root');

define('COOKIE_CONSENT_VERSION', '1.0');
define('COOKIE_CONSENT_DAYS', 365);

define('SUPER_ADMIN_EMAIL', 'admin@code.dev');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}

// Initialize cookie consent in session if not present
if (!isset($_SESSION['cookie_consent'])) {
   if (isset($_COOKIE['cookie_consent'])) {
      $cookie_data = json_decode($_COOKIE['cookie_consent'], true);
      if ($cookie_data && isset($cookie_data['version']) && $cookie_data['version'] === COOKIE_CONSENT_VERSION) {
         $_SESSION['cookie_consent'] = $cookie_data;
      } else {
         $_SESSION['cookie_consent'] = [
               'version'     => COOKIE_CONSENT_VERSION,
               'accepted'    => false,
               'necessary'   => true,
               'preferences' => false,
               'statistics'  => false,
               'marketing'   => false,
               'timestamp'   => null
         ];
      }
   } else {
      $_SESSION['cookie_consent'] = [
         'version'     => COOKIE_CONSENT_VERSION,
         'accepted'    => false,
         'necessary'   => true,
         'preferences' => false,
         'statistics'  => false,
         'marketing'   => false,
         'timestamp'   => null
      ];
   }
}

// Save return URL for pages that require consent (except consent-related pages)
if (!$_SESSION['cookie_consent']['accepted'] && !in_array(basename($_SERVER['PHP_SELF']), ['cookie_consent.php', 'process_cookie_consent.php', 'cookie_policy.php'])) {
   $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
}

// Load user preferences if consent allows
if ($_SESSION['cookie_consent']['accepted'] && $_SESSION['cookie_consent']['preferences'] && isset($_COOKIE['user_preferences'])) {
   $user_preferences = json_decode($_COOKIE['user_preferences'], true);
   if ($user_preferences) {
      $_SESSION['user_preferences'] = $user_preferences;
   }
}

// Database connection
try {
   $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
   error_log("Database connection failed: " . $e->getMessage());
   // If the database is down, we cannot check consent logs – fallback to show consent
   // but we still need to let the user access the consent page itself.
   if (!in_array(basename($_SERVER['PHP_SELF']), ['cookie_consent.php', 'process_cookie_consent.php', 'cookie_policy.php'])) {
      header("Location: cookie_consent.php");
      exit;
   }
}

require_once __DIR__ . '/cookie_functions.php';

$exempt_pages = ['cookie_consent.php', 'process_cookie_consent.php', 'cookie_policy.php'];

if (needs_cookie_consent() && !in_array(basename($_SERVER['PHP_SELF']), $exempt_pages)) {
   if (!isset($_SESSION['return_url'])) {
      $_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
   }
   header('Location: cookie_consent.php');
   exit;
}

function getAllLanguages($pdo) {
   try {
      $stmt = $pdo->query("SELECT name FROM languages ORDER BY display_order, name");
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
   } catch (PDOException $e) {
      error_log("Failed to fetch languages: " . $e->getMessage());
      return ['HTML', 'CSS', 'JavaScript', 'PHP', 'Python', 'SQL'];
   }
}

function getCodeMirrorMode($language) {
   $modeMap = [
      'html' => 'htmlmixed',
      'css' => 'css',
      'javascript' => 'javascript',
      'php' => 'php',
      'python' => 'python',
      'sql' => 'sql',
      'java' => 'clike',
      'csharp' => 'clike',
      'cpp' => 'clike',
      'ruby' => 'ruby',
      'typescript' => 'javascript',
      'jsx' => 'jsx',
      'tsx' => 'jsx',
      'json' => 'javascript',
      'xml' => 'xml',
      'markdown' => 'markdown',
      'bash' => 'shell',
      'shell' => 'shell'
   ];
   
   $lowerLang = strtolower($language);
   return isset($modeMap[$lowerLang]) ? $modeMap[$lowerLang] : 'htmlmixed';
}

function logAdminActivity($admin_id, $activity_type, $description = '') {
   global $pdo;
   
   if (!$pdo) {
      error_log("Database connection not available for logging activity");
      return false;
   }
   
   try {
      $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
      $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
      
      $sql = "INSERT INTO admin_activities (admin_id, activity_type, description, ip_address, user_agent, created_at) 
               VALUES (?, ?, ?, ?, ?, NOW())";
      
      $stmt = $pdo->prepare($sql);
      $stmt->execute([$admin_id, $activity_type, $description, $ip_address, $user_agent]);
      
      return true;
   } catch (PDOException $e) {
      error_log("Failed to log admin activity: " . $e->getMessage());
      return false;
   }
}

function checkAdminPermission($required_role = 'admin') {
   if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
      return false;
   }
   
   $role_hierarchy = [
      'super_admin' => 3,
      'admin'       => 2,
      'moderator'   => 1
   ];
   
   $user_role = $_SESSION['admin_role'] ?? 'moderator';
   $required_level = $role_hierarchy[$required_role] ?? 1;
   $user_level = $role_hierarchy[$user_role] ?? 0;
   
   return $user_level >= $required_level;
}
?>