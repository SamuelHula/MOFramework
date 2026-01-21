<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'codelibrary');
define('DB_USER', 'root'); // Change as needed
define('DB_PASS', 'root'); // Change as needed

// Cookie consent configuration
define('COOKIE_CONSENT_VERSION', '1.0');
define('COOKIE_CONSENT_DAYS', 365);

// Admin configuration
define('SUPER_ADMIN_EMAIL', 'admin@code.dev');

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
session_start();
}

// Initialize cookie consent in session if not set
if (!isset($_SESSION['cookie_consent'])) {
// Check if cookie consent exists in cookie
if (isset($_COOKIE['cookie_consent'])) {
   $cookie_data = json_decode($_COOKIE['cookie_consent'], true);
   if ($cookie_data && isset($cookie_data['version']) && $cookie_data['version'] === COOKIE_CONSENT_VERSION) {
      $_SESSION['cookie_consent'] = $cookie_data;
   } else {
      $_SESSION['cookie_consent'] = [
            'version' => COOKIE_CONSENT_VERSION,
            'accepted' => false,
            'necessary' => true,
            'preferences' => false,
            'statistics' => false,
            'marketing' => false,
            'timestamp' => null
      ];
   }
} else {
   $_SESSION['cookie_consent'] = [
      'version' => COOKIE_CONSENT_VERSION,
      'accepted' => false,
      'necessary' => true,
      'preferences' => false,
      'statistics' => false,
      'marketing' => false,
      'timestamp' => null
   ];
}
}

// Check if we need to show cookie consent
if (!$_SESSION['cookie_consent']['accepted'] && basename($_SERVER['PHP_SELF']) !== 'process_cookie_consent.php') {
// Store current URL to return after consent
$_SESSION['return_url'] = $_SERVER['REQUEST_URI'];
}

// Apply cookie consent settings to session
if ($_SESSION['cookie_consent']['accepted']) {
// Apply user preferences from cookie
if ($_SESSION['cookie_consent']['preferences'] && isset($_COOKIE['user_preferences'])) {
   $user_preferences = json_decode($_COOKIE['user_preferences'], true);
   if ($user_preferences) {
      $_SESSION['user_preferences'] = $user_preferences;
   }
}
}

// Create database connection
try {
$pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
error_log("Database connection failed: " . $e->getMessage());
header("Location: ../error.php?code=500&message=Database+connection+failed");
exit;
}

// Function to get all languages from database
function getAllLanguages($pdo) {
   try {
      $stmt = $pdo->query("SELECT name FROM languages ORDER BY display_order, name");
      return $stmt->fetchAll(PDO::FETCH_COLUMN, 0);
   } catch (PDOException $e) {
      error_log("Failed to fetch languages: " . $e->getMessage());
      return ['HTML', 'CSS', 'JavaScript', 'PHP', 'Python', 'SQL']; // Fallback default languages
   }
}

// Function to get CodeMirror mode for a language
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

// Check admin permissions
function checkAdminPermission($required_role = 'admin') {
   if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
      return false;
   }
   
   $role_hierarchy = [
      'super_admin' => 3,
      'admin' => 2,
      'moderator' => 1
   ];
   
   $user_role = $_SESSION['admin_role'] ?? 'moderator';
   $required_level = $role_hierarchy[$required_role] ?? 1;
   $user_level = $role_hierarchy[$user_role] ?? 0;
   
   return $user_level >= $required_level;
}
?>