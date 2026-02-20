<?php
require_once 'config.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? ''; 
$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';

function hasRecentRejection($pdo, $ip, $user_agent) {
   $sixMonthsAgo = date('Y-m-d H:i:s', strtotime('-6 months'));
   $stmt = $pdo->prepare("SELECT id FROM consent_logs WHERE user_ip = ? AND user_agent = ? AND consent_type IN ('rejected', 'preferences') AND created_at > ? LIMIT 1");
   $stmt->execute([$ip, $user_agent, $sixMonthsAgo]);
   return $stmt->fetch() !== false;
}

if (hasRecentRejection($pdo, $ip, $user_agent)) {
   header("Location: ../index.php");
   exit;
}

if ($action === 'reject_all') {
   $_SESSION['cookie_consent'] = [
      'accepted' => false,
      'preferences' => false,
      'statistics' => false,
      'marketing' => false,
      'timestamp' => date('Y-m-d H:i:s')
   ];
   
   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type) VALUES (?, ?, 'rejected')");
   $stmt->execute([$ip, $user_agent]);
   
   setcookie('cookie_consent_rejected', '1', time() + (86400 * 180), '/', '', true, true); 
   
   header("Location: ../index.php");
   exit;
   
} elseif ($action === 'accept_necessary') {
   $_SESSION['cookie_consent'] = [
      'accepted' => false,
      'preferences' => false,
      'statistics' => false,
      'marketing' => false,
      'timestamp' => date('Y-m-d H:i:s')
   ];
   
   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type) VALUES (?, ?, 'preferences')");
   $stmt->execute([$ip, $user_agent]);
   
   setcookie('cookie_consent_rejected', '1', time() + (86400 * 180), '/', '', true, true);
   header("Location: ../index.php");
   exit;
   
} elseif ($action === 'accept_all') {
   $_SESSION['cookie_consent'] = [
      'accepted' => true,
      'preferences' => true,
      'statistics' => true,
      'marketing' => true,
      'timestamp' => date('Y-m-d H:i:s')
   ];
   
   $stmt = $pdo->prepare("INSERT INTO consent_logs (user_ip, user_agent, consent_type, preferences) VALUES (?, ?, 'accepted', ?)");
   $preferences_json = json_encode($_SESSION['cookie_consent']);
   $stmt->execute([$ip, $user_agent, $preferences_json]);
   
   setcookie('cookie_consent', json_encode($_SESSION['cookie_consent']), time() + (86400 * 365), '/', '', true, true);
   header("Location: ../index.php");
   exit;
}
?>