<?php
if (session_status() == PHP_SESSION_NONE) {
   session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cookie Policy - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      #header{
         height: 10vh;
      }
      .cookie-policy-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
      }
      .cookie-policy-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .cookie-policy-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .cookie-policy-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .policy-content {
         background: white;
         padding: 3rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
      }
      .policy-section {
         margin-bottom: 2.5rem;
      }
      .policy-section h2 {
         color: var(--primary);
         margin-bottom: 1rem;
         font-size: 1.8rem;
         padding-bottom: 0.5rem;
         border-bottom: 2px solid var(--back-dark);
      }
      .policy-section h3 {
         color: var(--text-color);
         margin: 1.5rem 0 1rem 0;
         font-size: 1.3rem;
      }
      .policy-section p {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
         margin-bottom: 1rem;
      }
      .policy-list {
         list-style-type: disc;
         margin-left: 2rem;
         margin-bottom: 1rem;
      }
      .policy-list li {
         color: var(--text-color);
         opacity: 0.8;
         margin-bottom: 0.5rem;
         line-height: 1.5;
      }
      .cookie-table {
         width: 100%;
         border-collapse: collapse;
         margin: 1.5rem 0;
         background: var(--back-light);
         border-radius: 8px;
         overflow: hidden;
      }
      .cookie-table th {
         background: var(--primary);
         color: white;
         padding: 1rem;
         text-align: left;
         font-weight: 600;
      }
      .cookie-table td {
         padding: 1rem;
         border-bottom: 1px solid var(--back-dark);
         color: var(--text-color);
      }
      .cookie-table tr:last-child td {
         border-bottom: none;
      }
      .cookie-type {
         display: inline-block;
         padding: 0.3rem 0.8rem;
         border-radius: 20px;
         font-size: 0.8rem;
         font-weight: 600;
         margin-right: 0.5rem;
      }
      .cookie-necessary {
         background: #e3f2fd;
         color: #1565c0;
      }
      .cookie-preferences {
         background: #e8f5e9;
         color: #2e7d32;
      }
      .cookie-statistics {
         background: #fff3e0;
         color: #ef6c00;
      }
      .cookie-marketing {
         background: #fce4ec;
         color: #c2185b;
      }
      .update-date {
         background: var(--back-dark);
         padding: 1rem;
         border-radius: 8px;
         margin-top: 2rem;
         font-style: italic;
         color: var(--text-color);
         opacity: 0.7;
      }
      .contact-gdpr {
         background: #e8f5e8;
         padding: 1.5rem;
         border-radius: 8px;
         margin-top: 2rem;
         border-left: 4px solid #2e7d32;
      }
      .contact-gdpr h3 {
         color: #2e7d32;
         margin-top: 0;
      }
      @media screen and (max-width: 768px) {
         .cookie-policy-container {
            padding: 2.5% 5% 5%;
         }
         .policy-content {
            padding: 2rem;
         }
         .cookie-policy-header h1 {
            font-size: 2.2rem;
         }
         .cookie-table {
            display: block;
            overflow-x: auto;
         }
      }
      @media screen and (max-width: 1024px) {
         .cookie-policy-container {
            padding: 2.5% 8% 5%;
         }
         .policy-content {
            padding: 2.5rem;
         }
         .cookie-policy-header h1 {
            font-size: 2.5rem;
         }
      }

      @media screen and (max-width: 768px) {
         .cookie-policy-container {
            padding: 2.5% 5% 5%;
         }
         .policy-content {
            padding: 2rem;
         }
         .cookie-policy-header h1 {
            font-size: 2.2rem;
         }
         .policy-section h2 {
            font-size: 1.6rem;
         }
         .policy-section h3 {
            font-size: 1.2rem;
         }
         .cookie-table {
            display: block;
            overflow-x: auto;
            white-space: nowrap;
            -webkit-overflow-scrolling: touch;
         }
         .cookie-table th,
         .cookie-table td {
            padding: 0.75rem;
            font-size: 0.9rem;
         }
         .cookie-type {
            display: block;
            margin-bottom: 0.25rem;
            width: fit-content;
         }
      }

      @media screen and (max-width: 480px) {
         .cookie-policy-container {
            padding: 2.5% 3% 5%;
         }
         .policy-content {
            padding: 1.5rem;
         }
         .cookie-policy-header h1 {
            font-size: 1.8rem;
         }
         .cookie-policy-header p {
            font-size: 1rem;
         }
         .policy-section h2 {
            font-size: 1.4rem;
         }
         .policy-section h3 {
            font-size: 1.1rem;
         }
         .policy-section p,
         .policy-list li {
            font-size: 0.95rem;
            line-height: 1.5;
         }
         .cookie-table {
            font-size: 0.85rem;
         }
         .cookie-table th,
         .cookie-table td {
            padding: 0.5rem;
         }
         .update-date,
         .contact-gdpr {
            padding: 1rem;
         }
      }

      @media screen and (max-width: 360px) {
         .cookie-policy-container {
            padding: 2.5% 2% 5%;
         }
         .policy-content {
            padding: 1rem;
         }
         .cookie-policy-header h1 {
            font-size: 1.6rem;
         }
         .policy-section h2 {
            font-size: 1.3rem;
         }
      }
      @media (hover: none) and (pointer: coarse) {
         .policy-list li {
            padding: 0.25rem 0;
         }
         .cookie-table th,
         .cookie-table td {
            min-height: 44px; 
            display: flex;
            align-items: center;
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
      <section class="cookie-policy-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="cookie-policy-header scroll-effect">
            <h1>Cookie Policy</h1>
            <p>Last Updated: [Aktuálny dátum] | Version: 1.1</p>
         </div>
         
         <div class="policy-content scroll-effect">
            <div class="policy-section">
               <h2>1. What Are Cookies?</h2>
               <p>Cookies are small text files that are placed on your computer or mobile device when you visit our website. They are widely used to make websites work more efficiently and provide information to the website owners.</p>
               
               <h3>How We Use Cookies</h3>
               <p>We use cookies for the following purposes:</p>
               <ul class="policy-list">
                  <li><strong>Essential Cookies:</strong> Required for the website to function properly</li>
                  <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                  <li><strong>Analytical Cookies:</strong> Help us understand how visitors interact with our website</li>
                  <li><strong>Marketing Cookies:</strong> Used to deliver relevant advertisements</li>
               </ul>
            </div>
            
            <div class="policy-section">
               <h2>2. Types of Cookies We Use</h2>
               
               <!-- AKTUALIZOVANÁ TABUĽKA PODĽA REÁLNYCH COOKIES (Krok 5) -->
               <table class="cookie-table">
                  <thead>
                     <tr>
                        <th>Cookie Type</th>
                        <th>Name</th>
                        <th>Purpose</th>
                        <th>Duration</th>
                     </tr>
                  </thead>
                  <tbody>
                     <tr>
                        <td><span class="cookie-type cookie-necessary">Essential</span></td>
                        <td>PHPSESSID</td>
                        <td>Udržiava prihlásenie používateľa a stav relácie.</td>
                        <td>Po zatvorení prehliadača</td>
                     </tr>
                     <tr>
                        <td><span class="cookie-type cookie-necessary">Essential</span></td>
                        <td>csrf_token</td>
                        <td>Chráni pred CSRF útokmi (bezpečnosť formulárov).</td>
                        <td>Po zatvorení prehliadača</td>
                     </tr>
                     <tr>
                        <td><span class="cookie-type cookie-preferences">Preferences</span></td>
                        <td>cookie_consent</td>
                        <td>Ukladá tvoje preferencie ohľadom cookies (aký súhlas si udelil).</td>
                        <td>1 rok</td>
                     </tr>
                     <tr>
                        <td><span class="cookie-type cookie-preferences">Preferences</span></td>
                        <td>user_preferences</td>
                        <td>Ukladá tvoje preferencie, napr. jazyk alebo tému.</td>
                        <td>1 rok</td>
                     </tr>
                     <tr>
                        <td><span class="cookie-type cookie-statistics">Statistics</span></td>
                        <td>_ga</td>
                        <td>Google Analytics – rozlišuje používateľov.</td>
                        <td>2 roky</td>
                     </tr>
                     <tr>
                        <td><span class="cookie-type cookie-statistics">Statistics</span></td>
                        <td>_gid</td>
                        <td>Google Analytics – rozlišuje používateľov.</td>
                        <td>24 hodín</td>
                     </tr>
                     <tr>
                        <td><span class="cookie-type cookie-marketing">Marketing</span></td>
                        <td>_fbp</td>
                        <td>Facebook Pixel – používa sa na zacielenie reklám.</td>
                        <td>3 mesiace</td>
                     </tr>
                  </tbody>
               </table>
               <p><em>Poznámka: Analytické a marketingové cookies sa ukladajú len po tvojom výslovnom súhlase.</em></p>
            </div>
            
            <div class="policy-section">
               <h2>3. Your Cookie Choices</h2>
               <p>You have the right to choose which cookies you accept. When you first visit our website, you will see a cookie consent banner where you can:</p>
               <ul class="policy-list">
                  <li><strong>Accept All Cookies:</strong> Accept all types of cookies</li>
                  <li><strong>Reject All:</strong> Odmietnuť všetky nevyhnutné cookies. Tvoja voľba bude rešpektovaná 6 mesiacov, počas ktorých ti banner znova neukážeme.</li>  <!-- DOPLNENÉ -->
                  <li><strong>Accept Necessary Only:</strong> Only accept essential cookies</li>
                  <li><strong>Customize Settings:</strong> Choose which cookie categories you want to accept</li>
               </ul>
               
               <h3>Managing Your Preferences</h3>
               <p>You can change your cookie preferences at any time by:</p>
               <ul class="policy-list">
                  <li>Clicking on the "Cookie Settings" link in the footer</li>
                  <li>Using the browser settings to manage cookies</li>
                  <li>Deleting existing cookies from your browser</li>
               </ul>
            </div>
            
            <div class="policy-section">
               <h2>4. GDPR Compliance</h2>
               <p>In accordance with the General Data Protection Regulation (GDPR), we ensure that:</p>
               <ul class="policy-list">
                  <li>We obtain explicit consent before setting non-essential cookies</li>
                  <li>You have the right to withdraw consent at any time</li>
                  <li>We provide clear information about data processing</li>
                  <li>We implement appropriate security measures</li>
                  <li>We respect your right to data portability and erasure</li>
               </ul>
               
               <div class="contact-gdpr">
                  <h3>Your Data Protection Rights</h3>
                  <p>Under GDPR, you have the right to:</p>
                  <ul class="policy-list">
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
            
            <div class="policy-section">
               <h2>5. Third-Party Cookies</h2>
               <p>We use the following third-party services that may set cookies:</p>
               <ul class="policy-list">
                  <li><strong>Google Analytics:</strong> For website analytics</li>
                  <li><strong>Google ReCAPTCHA:</strong> For security purposes</li>
                  <li><strong>Facebook Pixel:</strong> For advertising and analytics</li>
               </ul>
               <p>These third parties have their own privacy policies. We recommend reviewing them.</p>
            </div>
            
            <div class="policy-section">
               <h2>6. Updates to This Policy</h2>
               <p>We may update this Cookie Policy from time to time. We will notify you of any changes by posting the new Cookie Policy on this page and updating the "Last Updated" date.</p>
            </div>
            
            <div class="update-date">
               <p><strong>Last Updated:</strong> [Aktuálny dátum]</p>
               <p>This Cookie Policy is effective as of the date above.</p>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
</body>
</html>