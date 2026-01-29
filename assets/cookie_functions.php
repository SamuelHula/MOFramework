<?php
function needs_cookie_consent() {
   if (!isset($_SESSION['cookie_consent'])) {
      return true;
   }
   
   return !$_SESSION['cookie_consent']['accepted'];
}

function is_cookie_allowed($type) {
   if (!isset($_SESSION['cookie_consent'])) {
      return false;
   }
   
   if (!$_SESSION['cookie_consent']['accepted']) {
      return false;
   }
   
   switch ($type) {
      case 'necessary':
         return $_SESSION['cookie_consent']['necessary'];
      case 'preferences':
         return $_SESSION['cookie_consent']['preferences'];
      case 'statistics':
         return $_SESSION['cookie_consent']['statistics'];
      case 'marketing':
         return $_SESSION['cookie_consent']['marketing'];
      default:
         return false;
   }
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
      'theme' => 'light',
      'language' => 'en',
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
?>