<!-- [file name]: assets/config.php -->
<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'codelibrary');
define('DB_USER', 'root'); // Change as needed
define('DB_PASS', ''); // Change as needed

// Cookie consent configuration
define('COOKIE_CONSENT_VERSION', '1.0');
define('COOKIE_CONSENT_DAYS', 365);

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
?>