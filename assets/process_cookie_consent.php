<?php
require_once 'config.php';

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
   
   $cookie_data = json_encode($consent);
   setcookie(
      'cookie_consent',
      $cookie_data,
      time() + (COOKIE_CONSENT_DAYS * 24 * 60 * 60),
      '/',
      '',
      false,
      true 
   );
   
   $_SESSION['cookie_consent'] = $consent;
   
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
   
   $return_url = $_SESSION['return_url'] ?? '../index.php';
   unset($_SESSION['return_url']);
   
   $message = urlencode('Cookie preferences saved successfully!');
   header("Location: $return_url?cookie_success=$message");
   exit;
   
} elseif (isset($_GET['action']) && $_GET['action'] === 'show_settings') {
   header("Location: ../cookie_consent.php?show_modal=true");
   exit;
   
} elseif (isset($_GET['action']) && $_GET['action'] === 'reset_consent') {
   setcookie('cookie_consent', '', time() - 3600, '/');
   setcookie('user_preferences', '', time() - 3600, '/');
   unset($_SESSION['cookie_consent']);
   unset($_SESSION['user_preferences']);
   
   header("Location: ../index.php?cookie_reset=1");
   exit;
}

header("Location: ../index.php");
exit;
?>