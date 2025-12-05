<?php
require_once 'config.php';

// Check if this is a form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $action = $_POST['action'] ?? '';
   
   switch ($action) {
      case 'accept_all':
         $consent = [
               'version' => COOKIE_CONSENT_VERSION,
               'accepted' => true,
               'necessary' => true,
               'preferences' => true,
               'statistics' => true,
               'marketing' => true,
               'timestamp' => date('Y-m-d H:i:s')
         ];
         break;
         
      case 'accept_necessary':
         $consent = [
               'version' => COOKIE_CONSENT_VERSION,
               'accepted' => true,
               'necessary' => true,
               'preferences' => false,
               'statistics' => false,
               'marketing' => false,
               'timestamp' => date('Y-m-d H:i:s')
         ];
         break;
         
      case 'save_preferences':
         $consent = [
               'version' => COOKIE_CONSENT_VERSION,
               'accepted' => true,
               'necessary' => true,
               'preferences' => isset($_POST['preferences']) ? true : false,
               'statistics' => isset($_POST['statistics']) ? true : false,
               'marketing' => isset($_POST['marketing']) ? true : false,
               'timestamp' => date('Y-m-d H:i:s')
         ];
         break;
         
      default:
         // Default to necessary only
         $consent = [
               'version' => COOKIE_CONSENT_VERSION,
               'accepted' => true,
               'necessary' => true,
               'preferences' => false,
               'statistics' => false,
               'marketing' => false,
               'timestamp' => date('Y-m-d H:i:s')
         ];
   }
   
   // Save consent to cookie
   $cookie_data = json_encode($consent);
   setcookie(
      'cookie_consent',
      $cookie_data,
      time() + (COOKIE_CONSENT_DAYS * 24 * 60 * 60),
      '/',
      '',
      false,
      true // HttpOnly flag
   );
   
   // Save consent to session
   $_SESSION['cookie_consent'] = $consent;
   
   // Set user preferences cookie if allowed
   if ($consent['preferences']) {
      $user_preferences = [
         'theme' => 'light',
         'language' => 'en',
         'font_size' => 'medium'
      ];
      setcookie(
         'user_preferences',
         json_encode($user_preferences),
         time() + (COOKIE_CONSENT_DAYS * 24 * 60 * 60),
         '/',
         '',
         false,
         true
      );
      $_SESSION['user_preferences'] = $user_preferences;
   }
   
   // Redirect back to original page or home
   $return_url = $_SESSION['return_url'] ?? '../index.php';
   unset($_SESSION['return_url']);
   
   // Add success message
   $message = urlencode('Cookie preferences saved successfully!');
   header("Location: $return_url?cookie_success=$message");
   exit;
   
} elseif (isset($_GET['action']) && $_GET['action'] === 'show_settings') {
   // Show cookie settings modal
   header("Location: ../cookie_consent.php?show_modal=true");
   exit;
   
} elseif (isset($_GET['action']) && $_GET['action'] === 'reset_consent') {
   // Reset cookie consent
   setcookie('cookie_consent', '', time() - 3600, '/');
   setcookie('user_preferences', '', time() - 3600, '/');
   unset($_SESSION['cookie_consent']);
   unset($_SESSION['user_preferences']);
   
   header("Location: ../index.php?cookie_reset=1");
   exit;
}

// If direct access, redirect to home
header("Location: ../index.php");
exit;
?>