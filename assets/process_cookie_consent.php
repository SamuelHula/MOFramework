<?php
require_once 'config.php';  

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

if ($action === 'reject_all') {
   $_SESSION['cookie_consent'] = [
      'accepted'    => false,
      'necessary'   => true,
      'preferences' => false,
      'statistics'  => false,
      'marketing'   => false,
      'timestamp'   => date('Y-m-d H:i:s'),
      'version'     => COOKIE_CONSENT_VERSION
   ];

   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type) VALUES (?, ?, 'rejected')");
   $stmt->execute([$ip, $user_agent]);

   setcookie('cookie_consent_rejected', '1', time() + (86400 * 180), '/', '', true, true);

   $redirect = $_SESSION['return_url'] ?? '../index.php';
   unset($_SESSION['return_url']);
   header("Location: $redirect");
   exit;

} elseif ($action === 'accept_necessary') {
   $_SESSION['cookie_consent'] = [
      'accepted'    => false,
      'necessary'   => true,
      'preferences' => false,
      'statistics'  => false,
      'marketing'   => false,
      'timestamp'   => date('Y-m-d H:i:s'),
      'version'     => COOKIE_CONSENT_VERSION
   ];

   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type) VALUES (?, ?, 'preferences')");
   $stmt->execute([$ip, $user_agent]);

   setcookie('cookie_consent_rejected', '1', time() + (86400 * 180), '/', '', true, true);

   $redirect = $_SESSION['return_url'] ?? '../index.php';
   unset($_SESSION['return_url']);
   header("Location: $redirect");
   exit;

} elseif ($action === 'accept_all') {
   $_SESSION['cookie_consent'] = [
      'accepted'    => true,
      'necessary'   => true,
      'preferences' => true,
      'statistics'  => true,
      'marketing'   => true,
      'timestamp'   => date('Y-m-d H:i:s'),
      'version'     => COOKIE_CONSENT_VERSION
   ];

   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type, preferences) VALUES (?, ?, 'accepted', ?)");
   $preferences_json = json_encode($_SESSION['cookie_consent']);
   $stmt->execute([$ip, $user_agent, $preferences_json]);

   setcookie('cookie_consent', json_encode($_SESSION['cookie_consent']), time() + (86400 * 365), '/', '', true, true);

   $redirect = $_SESSION['return_url'] ?? '../index.php';
   unset($_SESSION['return_url']);
   header("Location: $redirect");
   exit;

} elseif ($action === 'show_settings') {
   if (isset($_SERVER['HTTP_REFERER']) && !isset($_SESSION['return_url'])) {
      $_SESSION['return_url'] = $_SERVER['HTTP_REFERER'];
   }
   header("Location: ../cookie_consent.php");
   exit;

} elseif ($action === 'reset_consent') {
   unset($_SESSION['cookie_consent']);
   setcookie('cookie_consent', '', time() - 3600, '/', '', true, true);
   setcookie('cookie_consent_rejected', '', time() - 3600, '/', '', true, true);
   $stmt = $pdo->prepare("DELETE FROM consent_logs WHERE user_ip = ? AND user_agent = ?");
   $stmt->execute([$ip, $user_agent]);
   header("Location: ../cookie_consent.php");
   exit;

} elseif ($action === 'save_preferences') {
   $preferences = isset($_POST['preferences']) ? true : false;
   $statistics  = isset($_POST['statistics']) ? true : false;
   $marketing   = isset($_POST['marketing']) ? true : false;

   $consent = [
      'accepted'    => true,
      'necessary'   => true,
      'preferences' => $preferences,
      'statistics'  => $statistics,
      'marketing'   => $marketing,
      'timestamp'   => date('Y-m-d H:i:s'),
      'version'     => COOKIE_CONSENT_VERSION
   ];

   $_SESSION['cookie_consent'] = $consent;

   setcookie(
      'cookie_consent',
      json_encode($consent),
      time() + (86400 * COOKIE_CONSENT_DAYS),
      '/',
      '',
      true,
      true
   );

   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type, preferences) VALUES (?, ?, 'custom', ?)");
   $stmt->execute([$ip, $user_agent, json_encode($consent)]);

   $redirect = $_SESSION['return_url'] ?? '../index.php';
   unset($_SESSION['return_url']);
   header("Location: $redirect");
   exit;
}
?>