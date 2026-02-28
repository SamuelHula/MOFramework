<?php
function needs_cookie_consent() {
   global $pdo;

   if (isset($_SESSION['cookie_consent']['accepted']) && $_SESSION['cookie_consent']['accepted'] === true) {
      return false;
   }

   $ip = $_SERVER['HTTP_CF_CONNECTING_IP'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
   $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
   $sixMonthsAgo = date('Y-m-d H:i:s', strtotime('-6 months'));

   // If PDO is not available (database error), we cannot check logs – assume consent needed
   if (!$pdo) {
      return true;
   }

   $stmt = $pdo->prepare("SELECT id FROM consent_logs 
                           WHERE user_ip = ? AND user_agent = ? 
                           AND consent_type IN ('rejected', 'preferences') 
                           AND created_at > ? LIMIT 1");
   $stmt->execute([$ip, $user_agent, $sixMonthsAgo]);

   if ($stmt->fetch()) {
      return false;
   }

   return true;
}

function getCookieConsent() {
   $defaults = [
      'version'     => COOKIE_CONSENT_VERSION,
      'accepted'    => false,
      'necessary'   => true,
      'preferences' => false,
      'statistics'  => false,
      'marketing'   => false,
      'timestamp'   => null
   ];

   if (isset($_SESSION['cookie_consent']) && is_array($_SESSION['cookie_consent'])) {
      return array_merge($defaults, $_SESSION['cookie_consent']);
   }
   return $defaults;
}

function isCookieCategoryAllowed($category) {
   $consent = getCookieConsent();
   return isset($consent[$category]) && $consent[$category] === true;
}

function is_cookie_allowed($type) {
   if ($type === 'necessary') {
      return true;
   }

   if (!isset($_SESSION['cookie_consent']) || empty($_SESSION['cookie_consent']['accepted'])) {
      return false;
   }

   return $_SESSION['cookie_consent'][$type] ?? false;
}

function get_user_preferences() {
   if (isset($_SESSION['user_preferences'])) {
      return $_SESSION['user_preferences'];
   }
   
   if (isset($_COOKIE['user_preferences'])) {
      $preferences = json_decode($_COOKIE['user_preferences'], true);
      if ($preferences) {
         $_SESSION['user_preferences'] = $preferences;
         return $preferences;
      }
   }
   
   return [
      'theme'     => 'light',
      'language'  => 'en',
      'font_size' => 'medium'
   ];
}

function save_user_preferences($preferences) {
   if (!is_cookie_allowed('preferences')) {
      return false;
   }
   
   $_SESSION['user_preferences'] = $preferences;
   
   setcookie(
      'user_preferences',
      json_encode($preferences),
      time() + (365 * 24 * 60 * 60),
      '/',
      '',
      false,
      true
   );
   
   return true;
}

function show_cookie_banner() {
   if (!isset($_SESSION['cookie_consent']) || !$_SESSION['cookie_consent']['accepted']) {
      return false;
   }
   
   if (isset($_SESSION['cookie_banner_shown'])) {
      return false;
   }
   
   $_SESSION['cookie_banner_shown'] = true;
   return true;
}

function get_consent_status() {
   if (!isset($_SESSION['cookie_consent'])) {
      return 'No consent given';
   }
   
   if (!$_SESSION['cookie_consent']['accepted']) {
      return 'Consent required';
   }
   
   $types = [];
   if ($_SESSION['cookie_consent']['preferences']) $types[] = 'Preferences';
   if ($_SESSION['cookie_consent']['statistics']) $types[] = 'Statistics';
   if ($_SESSION['cookie_consent']['marketing']) $types[] = 'Marketing';
   
   if (empty($types)) {
      return 'Essential only';
   }
   
   return implode(', ', $types) . ' cookies accepted';
}

function load_analytics_scripts() {
   if (!is_cookie_allowed('statistics')) {
      return;
   }
   
   echo <<<HTML
   <script async src="https://www.googletagmanager.com/gtag/js?id=G-XXXXXXXXXX"></script>
   <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', 'G-XXXXXXXXXX');
   </script>
HTML;
}

function load_marketing_scripts() {
   if (!is_cookie_allowed('marketing')) {
      return;
   }
   
   echo <<<HTML
   <script>
      !function(f,b,e,v,n,t,s)
      {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
      n.callMethod.apply(n,arguments):n.queue.push(arguments)};
      if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
      n.queue=[];t=b.createElement(e);t.async=!0;
      t.src=v;s=b.getElementsByTagName(e)[0];
      s.parentNode.insertBefore(t,s)}(window, document,'script',
      'https://connect.facebook.net/en_US/fbevents.js');
      fbq('init', 'XXXXXXXXXXXXXXX');
      fbq('track', 'PageView');
   </script>
HTML;
}

function setCookieIfAllowed($name, $value, $expiry, $path = '/', $domain = '', $secure = true, $httponly = true, $category = 'necessary') {
   if ($category === 'necessary') {
      return setcookie($name, $value, $expiry, $path, $domain, $secure, $httponly);
   }
   if (isCookieCategoryAllowed($category)) {
      return setcookie($name, $value, $expiry, $path, $domain, $secure, $httponly);
   }
   return false;
}

function deleteCookie($name, $path = '/', $domain = '') {
   if (isset($_COOKIE[$name])) {
      unset($_COOKIE[$name]);
      setcookie($name, '', time() - 3600, $path, $domain, true, true);
   }
}
?>