<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cookie Consent - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      .cookie-overlay {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background-color: var(--back-dark);
         z-index: 10000;
         display: flex;
         align-items: center;
         justify-content: center;
         padding: 20px;
         backdrop-filter: blur(5px);
      }
      .cookie-consent-modal {
         background: white;
         border-radius: 20px;
         max-width: 800px;
         width: 100%;
         max-height: 90vh;
         overflow-y: auto;
         box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
         animation: slideUp 0.3s ease;
      }
      .cookie-header {
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         color: white;
         padding: 2rem;
         border-radius: 20px 20px 0 0;
         text-align: center;
      }
      .cookie-header h2 {
         color: white;
         font-size: 2rem;
         margin-bottom: 0.5rem;
      }
      .cookie-header p {
         color: rgba(255, 255, 255, 0.9);
         font-size: 1rem;
      }
      .cookie-content {
         padding: 2rem;
      }
      .cookie-categories {
         margin: 2rem 0;
      }
      .cookie-category {
         background: var(--back-light);
         border-radius: 10px;
         padding: 1.5rem;
         margin-bottom: 1rem;
         border: 2px solid var(--back-dark);
      }
      .cookie-category.active {
         border-color: var(--primary);
         background: rgba(48, 188, 237, 0.05);
      }
      .active::after{
         display: none;
      }
      .category-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1rem;
      }
      .category-title {
         display: flex;
         align-items: center;
         gap: 1rem;
      }
      .category-icon {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         font-weight: bold;
         font-size: 0.9rem;
      }
      .essential-icon { background: #1565c0; }
      .preferences-icon { background: #2e7d32; }
      .statistics-icon { background: #ef6c00; }
      .marketing-icon { background: #c2185b; }
      .category-header h3 {
         margin: 0;
         font-size: 1.3rem;
      }
      .category-description {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
         margin-bottom: 1rem;
      }
      .category-essential .category-description {
         font-style: italic;
         color: #1565c0;
      }
      .checkbox-container {
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .cookie-checkbox {
         width: 20px;
         height: 20px;
         cursor: pointer;
      }
      .cookie-checkbox:disabled {
         cursor: not-allowed;
         opacity: 0.6;
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
         flex: 1;
         min-width: 200px;
         text-align: center;
         text-decoration: none;
         display: inline-block;
      }
      .cookie-btn-accept-all {
         background: var(--primary);
         color: white;
      }
      .cookie-btn-accept-all:hover {
         background: transparent;
         color: var(--primary);
         border-color: var(--primary);
      }
      .cookie-btn-necessary {
         background: var(--secondary);
         color: white;
      }
      .cookie-btn-necessary:hover {
         background: transparent;
         color: var(--secondary);
         border-color: var(--secondary);
      }
      .cookie-btn-save {
         background: var(--text-color);
         color: white;
      }      
      .cookie-btn-save:hover {
         background: transparent;
         color: var(--text-color);
         border-color: var(--text-color);
      }
      .cookie-footer {
         text-align: center;
         margin-top: 2rem;
         padding-top: 2rem;
         border-top: 1px solid var(--back-dark);
      }
      .cookie-footer a {
         color: var(--primary);
         text-decoration: none;
         font-weight: 600;
      }
      .cookie-footer a:hover {
         text-decoration: underline;
      }
      @keyframes slideUp {
         from {
               transform: translateY(50px);
               opacity: 0;
         }
         to {
               transform: translateY(0);
               opacity: 1;
         }
      }
      @media screen and (max-width: 768px) {
         .cookie-consent-modal {
               max-height: 95vh;
         }  
         .cookie-header {
               padding: 1.5rem;
         }
         .cookie-header h2 {
               font-size: 1.5rem;
         }
         .cookie-content {
               padding: 1.5rem;
         }
         .cookie-buttons {
               flex-direction: column;
         }
         .cookie-btn {
               min-width: 100%;
         }
      }
      body {
         overflow: hidden !important;
         height: 100vh !important;
      }
   </style>
</head>
<body>
   <div class="cookie-overlay">
      <div class="cookie-consent-modal">
         <div class="cookie-header">
               <h2>🍪 Cookie Consent</h2>
               <p>We use cookies to enhance your experience. Choose which cookies you allow us to use.</p>
         </div>
         
         <div class="cookie-content">
               <p>This website uses cookies to improve your browsing experience, analyze site traffic, and personalize content. By clicking "Accept All", you consent to our use of all cookies. You can also choose specific types of cookies below.</p>
               
               <form action="./assets/process_cookie_consent.php" method="POST">
                  <div class="cookie-categories">
                     <div class="cookie-category category-essential active">
                           <div class="category-header">
                              <div class="category-title">
                                 <div class="category-icon essential-icon">✓</div>
                                 <h3>Essential Cookies</h3>
                              </div>
                              <div class="checkbox-container">
                                 <input type="checkbox" class="cookie-checkbox" checked disabled>
                                 <span>Always Active</span>
                              </div>
                           </div>
                           <p class="category-description">Required for the website to function properly. Cannot be disabled.</p>
                           <input type="hidden" name="necessary" value="1">
                     </div>
                     
                     <div class="cookie-category">
                           <div class="category-header">
                              <div class="category-title">
                                 <div class="category-icon preferences-icon">⚙️</div>
                                 <h3>Preference Cookies</h3>
                              </div>
                              <div class="checkbox-container">
                                 <input type="checkbox" class="cookie-checkbox" name="preferences" value="1" 
                                          <?php echo (isset($_SESSION['cookie_consent']['preferences']) && $_SESSION['cookie_consent']['preferences']) ? 'checked' : ''; ?>>
                                 <span>Allow</span>
                              </div>
                           </div>
                           <p class="category-description">Remember your settings and preferences (language, theme, etc.)</p>
                     </div>
                     
                     <div class="cookie-category">
                           <div class="category-header">
                              <div class="category-title">
                                 <div class="category-icon statistics-icon">📊</div>
                                 <h3>Statistics Cookies</h3>
                              </div>
                              <div class="checkbox-container">
                                 <input type="checkbox" class="cookie-checkbox" name="statistics" value="1"
                                          <?php echo (isset($_SESSION['cookie_consent']['statistics']) && $_SESSION['cookie_consent']['statistics']) ? 'checked' : ''; ?>>
                                 <span>Allow</span>
                              </div>
                           </div>
                           <p class="category-description">Help us understand how visitors interact with our website</p>
                     </div>
                     
                     <div class="cookie-category">
                           <div class="category-header">
                              <div class="category-title">
                                 <div class="category-icon marketing-icon">📢</div>
                                 <h3>Marketing Cookies</h3>
                              </div>
                              <div class="checkbox-container">
                                 <input type="checkbox" class="cookie-checkbox" name="marketing" value="1"
                                          <?php echo (isset($_SESSION['cookie_consent']['marketing']) && $_SESSION['cookie_consent']['marketing']) ? 'checked' : ''; ?>>
                                 <span>Allow</span>
                              </div>
                           </div>
                           <p class="category-description">Used to deliver relevant advertisements and measure ad performance</p>
                     </div>
                  </div>
                  
                  <div class="cookie-buttons">
                     <button type="submit" name="action" value="accept_all" class="cookie-btn cookie-btn-accept-all">
                           Accept All
                     </button>
                     <button type="submit" name="action" value="accept_necessary" class="cookie-btn cookie-btn-necessary">
                           Accept Necessary Only
                     </button>
                     <button type="submit" name="action" value="save_preferences" class="cookie-btn cookie-btn-save">
                           Save Preferences
                     </button>
                  </div>
                  
                  <div class="cookie-footer">
                     <p>By continuing, you agree to our <a href="cookie_policy.php">Cookie Policy</a>. You can change your preferences at any time.</p>
                  </div>
               </form>
         </div>
      </div>
   </div>
</body>
</html>