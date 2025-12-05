<?php
require_once './assets/config.php';
require_once './assets/cookie_functions.php';
if (!isset($_SESSION['cookie_consent']) || !$_SESSION['cookie_consent']['accepted']) {
   header("Location: cookie_consent.php");
   exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cookie Settings - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      #header{
         height: 10vh;
      }
      .cookie-settings-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
      }
      .cookie-settings-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .cookie-settings-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .cookie-settings-content {
         background: white;
         padding: 3rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
      }
      .current-settings {
         background: var(--back-light);
         padding: 1.5rem;
         border-radius: 10px;
         margin-bottom: 2rem;
         border-left: 4px solid var(--primary);
      }
      .current-settings h3 {
         color: var(--primary);
         margin-bottom: 1rem;
      }
      .settings-list {
         list-style-type: none;
         padding: 0;
      }
      .settings-list li {
         padding: 0.5rem 0;
         border-bottom: 1px solid var(--back-dark);
         display: flex;
         justify-content: space-between;
      }
      .settings-list li:last-child {
         border-bottom: none;
      }
      .setting-status {
         padding: 0.3rem 0.8rem;
         border-radius: 20px;
         font-size: 0.8rem;
         font-weight: 600;
      }
      .status-allowed {
         background: #e8f5e9;
         color: #2e7d32;
      }
      .status-denied {
         background: #ffebee;
         color: #c62828;
      }
      .status-required {
         background: #e3f2fd;
         color: #1565c0;
      }
      .cookie-buttons {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .cookie-btn {
         padding: 1rem 2rem;
         border-radius: 10px;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         border: 2px solid transparent;
         font-size: 1rem;
         text-decoration: none;
         display: inline-block;
         text-align: center;
      }
      .cookie-btn-change {
         background: var(--primary);
         color: white;
      }
      .cookie-btn-change:hover {
         background: transparent;
         color: var(--primary);
         border-color: var(--primary);
      }
      .cookie-btn-reset {
         background: #dc3545;
         color: white;
      }
      .cookie-btn-reset:hover {
         background: transparent;
         color: #dc3545;
         border-color: #dc3545;
      }
      .cookie-btn-export {
         background: var(--secondary);
         color: white;
      }
      .cookie-btn-export:hover {
         background: transparent;
         color: var(--secondary);
         border-color: var(--secondary);
      }
      .gdpr-info {
         background: #e8f5e8;
         padding: 1.5rem;
         border-radius: 8px;
         margin-top: 2rem;
         border-left: 4px solid #2e7d32;
      }
      .gdpr-info h3 {
         color: #2e7d32;
         margin-top: 0;
      }
      @media screen and (max-width: 768px) {
         .cookie-settings-container {
               padding: 2.5% 5% 5%;
         }
         .cookie-settings-content {
               padding: 2rem;
         }
         
         .cookie-settings-header h1 {
               font-size: 2.2rem;
         }
         .cookie-buttons {
               flex-direction: column;
         }
         .cookie-btn {
               width: 100%;
         }
      }
   </style>
</head>
<body>
   <div class="progress-container">
      <div id="scrollProgress"></div>
   </div>
   <header id="header">
      <?php include './assets/nav_bar.php' ?>
   </header>
   
   <main id="main">
      <section class="cookie-settings-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="cookie-settings-header scroll-effect">
               <h1>Cookie Settings</h1>
               <p>Manage your cookie preferences and data</p>
         </div>
         
         <div class="cookie-settings-content scroll-effect">
               <div class="current-settings">
                  <h3>Current Cookie Settings</h3>
                  <ul class="settings-list">
                     <li>
                           <span>Essential Cookies</span>
                           <span class="setting-status status-required">Always Active</span>
                     </li>
                     <li>
                           <span>Preference Cookies</span>
                           <span class="setting-status <?php echo $_SESSION['cookie_consent']['preferences'] ? 'status-allowed' : 'status-denied'; ?>">
                              <?php echo $_SESSION['cookie_consent']['preferences'] ? 'Allowed' : 'Denied'; ?>
                           </span>
                     </li>
                     <li>
                           <span>Statistics Cookies</span>
                           <span class="setting-status <?php echo $_SESSION['cookie_consent']['statistics'] ? 'status-allowed' : 'status-denied'; ?>">
                              <?php echo $_SESSION['cookie_consent']['statistics'] ? 'Allowed' : 'Denied'; ?>
                           </span>
                     </li>
                     <li>
                           <span>Marketing Cookies</span>
                           <span class="setting-status <?php echo $_SESSION['cookie_consent']['marketing'] ? 'status-allowed' : 'status-denied'; ?>">
                              <?php echo $_SESSION['cookie_consent']['marketing'] ? 'Allowed' : 'Denied'; ?>
                           </span>
                     </li>
                     <li>
                           <span>Last Updated</span>
                           <span><?php echo $_SESSION['cookie_consent']['timestamp'] ?? 'Never'; ?></span>
                     </li>
                  </ul>
               </div>
               
               <div class="cookie-buttons">
                  <a href="./assets/process_cookie_consent.php?action=show_settings" class="cookie-btn cookie-btn-change">
                     Change Cookie Preferences
                  </a>
                  <a href="./assets/process_cookie_consent.php?action=reset_consent" class="cookie-btn cookie-btn-reset"
                     onclick="return confirm('Are you sure you want to reset all cookie preferences? This will require you to set them again.')">
                     Reset All Cookies
                  </a>
               </div>
               
               <div class="gdpr-info">
                  <h3>Your GDPR Rights</h3>
                  <p>Under the General Data Protection Regulation (GDPR), you have the right to:</p>
                  <ul>
                     <li>Access your personal data</li>
                     <li>Rectify inaccurate data</li>
                     <li>Erase your personal data</li>
                     <li>Restrict processing of your data</li>
                     <li>Data portability</li>
                     <li>Object to processing</li>
                  </ul>
                  <p>To exercise these rights, please contact us at: <strong>privacy@codelibrary.dev</strong></p>
               </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
</body>
</html>