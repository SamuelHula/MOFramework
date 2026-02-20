<?php
require_once './assets/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Privacy Policy - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      .policy-content {
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
      }
      .policy-section,
      .policy-section * {
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
         color: #1f2937 !important;
      }
      #header {
         height: 10vh;
      }
      .privacy-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
      }
      .privacy-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .privacy-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .privacy-header p {
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
         scroll-margin-top: 20px;
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
      .table-of-contents {
         background: var(--back-light);
         padding: 1.5rem;
         border-radius: 10px;
         margin-bottom: 2rem;
         border-left: 4px solid var(--primary);
      }
      .table-of-contents h3 {
         color: var(--primary);
         margin-top: 0;
         margin-bottom: 1rem;
      }
      .toc-list {
         list-style-type: none;
         margin: 0;
         padding: 0;
      }
      .toc-list li {
         margin-bottom: 0.5rem;
      }
      .toc-list a {
         color: var(--text-color);
         text-decoration: none;
         display: flex;
         align-items: center;
         gap: 0.5rem;
         transition: color 0.3s;
      }
      .toc-list a:hover {
         color: var(--primary);
      }
      .toc-list a svg {
         width: 16px;
         height: 16px;
         fill: var(--primary);
      }
      .data-type {
         display: inline-block;
         padding: 0.3rem 0.8rem;
         border-radius: 20px;
         font-size: 0.8rem;
         font-weight: 600;
         margin-right: 0.5rem;
         margin-bottom: 0.5rem;
      }
      .data-personal {
         background: #e3f2fd;
         color: #1565c0;
      }
      .data-usage {
         background: #e8f5e9;
         color: #2e7d32;
      }
      .data-technical {
         background: #fff3e0;
         color: #ef6c00;
      }
      .data-third-party {
         background: #fce4ec;
         color: #c2185b;
      }
      .highlight-box {
         background: #e8f5e8;
         padding: 1.5rem;
         border-radius: 8px;
         margin: 1.5rem 0;
         border-left: 4px solid #2e7d32;
      }
      .highlight-box h3 {
         color: #2e7d32;
         margin-top: 0;
      }
      .contact-card {
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         color: white;
         padding: 2rem;
         border-radius: 10px;
         margin-top: 2rem;
         text-align: center;
      }
      .contact-card h3 {
         color: white;
         margin-top: 0;
         margin-bottom: 1rem;
      }
      .contact-email {
         font-size: 1.2rem;
         font-weight: bold;
         color: white;
         text-decoration: none;
         display: inline-block;
         padding: 0.5rem 1rem;
         background: rgba(255, 255, 255, 0.2);
         border-radius: 5px;
         transition: background 0.3s;
      }
      .contact-email:hover {
         background: rgba(255, 255, 255, 0.3);
      }
      .update-info {
         background: var(--back-dark);
         padding: 1rem;
         border-radius: 8px;
         margin-top: 2rem;
         font-style: italic;
         color: var(--text-color);
         opacity: 0.7;
      }
      @media screen and (max-width: 768px) {
         .privacy-container {
            padding: 2.5% 5% 5%;
         }
         .policy-content {
            padding: 2rem;
         }
         .privacy-header h1 {
            font-size: 2.2rem;
         }
      }
      @media screen and (max-width: 1024px) {
         .privacy-container {
            padding: 2.5% 8% 5%;
         }
         .policy-content {
            padding: 2.5rem;
         }
         .privacy-header h1 {
            font-size: 2.5rem;
         }
         .table-of-contents {
            padding: 1.25rem;
         }
      }

      @media screen and (max-width: 768px) {
         .privacy-container {
            padding: 2.5% 5% 5%;
         }
         .policy-content {
            padding: 2rem;
         }
         .privacy-header h1 {
            font-size: 2.2rem;
         }
         .policy-section h2 {
            font-size: 1.6rem;
         }
         .policy-section h3 {
            font-size: 1.2rem;
         }
         .table-of-contents h3 {
            font-size: 1.3rem;
         }
         .toc-list a {
            font-size: 0.95rem;
         }
         .data-type {
            display: block;
            width: fit-content;
            margin-bottom: 0.5rem;
         }
         .highlight-box,
         .contact-card {
            padding: 1.25rem;
         }
      }

      @media screen and (max-width: 480px) {
         .privacy-container {
            padding: 2.5% 3% 5%;
         }
         .policy-content {
            padding: 1.5rem;
         }
         .privacy-header h1 {
            font-size: 1.8rem;
         }
         .privacy-header p {
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
         .table-of-contents {
            padding: 1rem;
         }
         .table-of-contents h3 {
            font-size: 1.1rem;
         }
         .toc-list li {
            margin-bottom: 0.75rem;
         }
         .toc-list a {
            font-size: 0.9rem;
            flex-wrap: wrap;
         }
         .data-type {
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
         }
         .contact-email {
            font-size: 1rem;
            padding: 0.5rem 0.875rem;
         }
         .update-info {
            padding: 0.875rem;
         }
      }

      @media screen and (max-width: 360px) {
         .privacy-container {
            padding: 2.5% 2% 5%;
         }
         .policy-content {
            padding: 1rem;
         }
         .privacy-header h1 {
            font-size: 1.6rem;
         }
         .policy-section h2 {
            font-size: 1.3rem;
         }
         .table-of-contents {
            padding: 0.875rem;
         }
         .toc-list a svg {
            width: 14px;
            height: 14px;
         }
      }
      @media (hover: none) and (pointer: coarse) {
         .toc-list a {
            padding: 0.5rem 0;
            min-height: 44px;
         }
         .toc-list li {
            margin-bottom: 0.5rem;
         }
      }
      @media screen and (max-width: 480px) {
         .policy-section p {
            text-align: justify;
            hyphens: auto;
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
      <section class="privacy-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="privacy-header scroll-effect">
            <h1>Privacy Policy</h1>
            <p>Effective Date: December 15, 2024 | Version: 1.1</p>
         </div>
         
         <div class="policy-content scroll-effect">
            <div class="table-of-contents">
               <h3>📋 Table of Contents</h3>
               <ul class="toc-list">
                  <li><a href="#introduction"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-6h2v6zm0-8h-2V7h2v2z"/></svg> 1. Introduction</a></li>
                  <li><a href="#data-collection"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm0 15l-5-5h3V9h4v4h3l-5 5z"/></svg> 2. Information We Collect</a></li>
                  <li><a href="#data-use"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> 3. How We Use Your Information</a></li>
                  <li><a href="#data-sharing"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/></svg> 4. Data Sharing & Disclosure</a></li>
                  <li><a href="#data-security"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg> 5. Data Security</a></li>
                  <li><a href="#your-rights"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> 6. Your Rights</a></li>
                  <li><a href="#cookies"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6z"/></svg> 7. Cookies & Tracking</a></li>
                  <li><a href="#third-party"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M9 4v3h5v12h3V7h5V4H9zm-6 8h3v7h3v-7h3V9H3v3z"/></svg> 8. Third-Party Services</a></li>
                  <li><a href="#children"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm3 11h-2v2h2v2h-2v2h-2v-2H9v-2h2v-2H9v-2h2V9h2v2h2v2z"/></svg> 9. Children's Privacy</a></li>
                  <li><a href="#changes"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12z"/></svg> 10. Changes to This Policy</a></li>
                  <li><a href="#contact"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> 11. Contact Us</a></li>
               </ul>
            </div>
            
            <div class="policy-section" id="introduction">
               <h2>1. Introduction</h2>
               <p>Welcome to Code Library. We respect your privacy and are committed to protecting your personal data. This privacy policy explains how we collect, use, disclose, and safeguard your information when you visit our website.</p>
               
               <div class="highlight-box">
                  <h3>Key Principles</h3>
                  <ul class="policy-list">
                     <li>We only collect necessary information</li>
                     <li>We're transparent about how we use your data</li>
                     <li>We protect your data with industry-standard security</li>
                     <li>We never sell your personal information</li>
                     <li>You have control over your data</li>
                  </ul>
               </div>
            </div>
            
            <div class="policy-section" id="data-collection">
               <h2>2. Information We Collect</h2>
               
               <h3>Personal Information You Provide</h3>
               <p>When you register for an account, contact us, or use our services, we may collect:</p>
               <ul class="policy-list">
                  <li><span class="data-type data-personal">Identity Data</span> Name, email address</li>
                  <li><span class="data-type data-personal">Contact Data</span> Email address, contact preferences</li>
                  <li><span class="data-type data-usage">Profile Data</span> Username, password, preferences</li>
                  <li><span class="data-type data-usage">Communication Data</span> Messages, feedback, inquiries</li>
               </ul>
               
               <h3>Automatically Collected Information</h3>
               <p>When you visit our website, we automatically collect:</p>
               <ul class="policy-list">
                  <li><span class="data-type data-technical">Technical Data</span> IP address, browser type, device information</li>
                  <li><span class="data-type data-technical">Usage Data</span> Pages visited, time spent, navigation patterns</li>
                  <li><span class="data-type data-technical">Cookie Data</span> Information from cookies and similar technologies</li>
               </ul>
            </div>
            
            <div class="policy-section" id="data-use">
               <h2>3. How We Use Your Information</h2>
               <p>We use your information for the following purposes:</p>
               
               <ul class="policy-list">
                  <li><strong>To Provide Services:</strong> Operate and maintain our website and services</li>
                  <li><strong>Account Management:</strong> Create and manage your user account</li>
                  <li><strong>Communication:</strong> Respond to your inquiries and provide support</li>
                  <li><strong>Improvement:</strong> Analyze usage to improve our website and services</li>
                  <li><strong>Security:</strong> Protect against fraudulent or unauthorized activity</li>
                  <li><strong>Legal Compliance:</strong> Comply with applicable laws and regulations</li>
               </ul>
               
               <div class="highlight-box">
                  <h3>Legal Basis for Processing (GDPR)</h3>
                  <p>For users in the European Economic Area (EEA), we process your personal data under the following legal bases:</p>
                  <ul class="policy-list">
                     <li><strong>Consent (čl. 6 ods. 1 písm. a) GDPR):</strong> Pre spracovanie analytických a marketingových cookies (Google Analytics, Facebook Pixel). Súhlas nám dávaš prostredníctvom cookie lišty.</li>
                     <li><strong>Plnenie zmluvy (čl. 6 ods. 1 písm. b) GDPR):</strong> Pre vytvorenie a správu tvojho používateľského účtu. Bez týchto údajov ti nemôžeme účet poskytnúť.</li>
                     <li><strong>Oprávnený záujem (čl. 6 ods. 1 písm. f) GDPR):</strong> Pre odpovedanie na správy z kontaktného formulára a pre zaistenie bezpečnosti našich systémov (logy, ochrana pred útokmi).</li>
                     <li><strong>Právna povinnosť (čl. 6 ods. 1 písm. c) GDPR):</strong> Ak sme povinní uchovávať údaje na základe daňových alebo iných predpisov.</li>
                  </ul>
               </div>
            </div>
            
            <div class="policy-section" id="data-sharing">
               <h2>4. Data Sharing & Disclosure</h2>
               
               <h3>We May Share Your Information With:</h3>
               <ul class="policy-list">
                  <li><strong>Service Providers:</strong> Trusted third parties who help us operate our website (hosting, analytics, email services)</li>
                  <li><strong>Legal Authorities:</strong> When required by law or to protect our rights</li>
                  <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
               </ul>
               
               <h3>We Do NOT:</h3>
               <ul class="policy-list">
                  <li>Sell your personal information to third parties</li>
                  <li>Share your information for marketing purposes without your consent</li>
                  <li>Disclose more information than necessary</li>
               </ul>

               <!-- KONKRÉTNI PRÍJEMCOVIA (Krok 4) -->
               <h3>Konkrétni príjemcovia tvojich údajov</h3>
               <ul class="policy-list">
                  <li><strong>Webhosting: [Názov tvojej hostingovej spoločnosti]</strong> (so sídlom v [krajina]). Ukladajú sa tam všetky údaje, ktoré na stránke vytvoríš (účty, snippety). Spracovanie prebieha na základe zmluvy so sprostredkovateľom.</li>
                  <li><strong>Google Analytics (USA):</strong> Používame na analýzu správania na stránke. Google môže mať prístup k tvojej IP adrese. Tento nástroj sa spúšťa len s tvojím súhlasom (kategória Štatistické cookies). Viac v <a href="https://policies.google.com/privacy">politike Google</a>.</li>
                  <li><strong>Facebook Pixel (USA):</strong> Používame na meranie účinnosti reklám. Facebook môže priradiť tvoju návštevu k tvojmu Facebook účtu. Tento nástroj sa spúšťa len s tvojím súhlasom (kategória Marketingové cookies). Viac v <a href="https://www.facebook.com/privacy/policy">politike Facebooku</a>.</li>
                  <li><strong>CDN služby (napr. Cloudflare, Google Fonts):</strong> Na urýchlenie načítania stránky. Tieto služby môžu zaznamenávať tvoju IP adresu.</li>
               </ul>

               <!-- PRENOS DO TRETÍCH KRAJÍN (Krok 4) -->
               <h3>Prenos údajov mimo Európskej únie</h3>
               <p>Niektorí naši partneri (napr. Google, Facebook) majú sídlo v Spojených štátoch amerických. To znamená, že tvoje údaje môžu byť prenesené do krajiny, ktorá nemá rovnakú úroveň ochrany údajov ako EÚ. Tento prenos je však chránený štandardnými zmluvnými doložkami (SCC), ktoré schválila Európska komisia, a ktoré zaväzujú týchto partnerov chrániť tvoje údaje. Viac informácií nájdeš v politike Google a Facebooku.</p>
            </div>
            
            <div class="policy-section" id="data-security">
               <h2>5. Data Security</h2>
               <p>We implement appropriate technical and organizational measures to protect your personal data:</p>
               
               <ul class="policy-list">
                  <li><strong>Encryption:</strong> SSL/TLS encryption for data transmission</li>
                  <li><strong>Access Controls:</strong> Limited access to authorized personnel only</li>
                  <li><strong>Secure Storage:</strong> Industry-standard security for data storage</li>
                  <li><strong>Regular Audits:</strong> Security assessments and vulnerability testing</li>
                  <li><strong>Employee Training:</strong> Security awareness training for all staff</li>
               </ul>
               
               <p>While we strive to protect your personal data, no method of transmission over the internet or electronic storage is 100% secure. We cannot guarantee absolute security.</p>
            </div>
            
            <div class="policy-section" id="your-rights">
               <h2>6. Your Rights</h2>
               <p>Depending on your location, you may have the following rights regarding your personal data:</p>
               
               <h3>For All Users:</h3>
               <ul class="policy-list">
                  <li><strong>Access:</strong> Request a copy of your personal data</li>
                  <li><strong>Correction:</strong> Request correction of inaccurate data</li>
                  <li><strong>Deletion:</strong> Request deletion of your personal data</li>
                  <li><strong>Opt-Out:</strong> Opt out of marketing communications</li>
               </ul>
               
               <h3>Additional Rights for EEA Users (GDPR):</h3>
               <ul class="policy-list">
                  <li><strong>Restriction:</strong> Request restriction of processing</li>
                  <li><strong>Portability:</strong> Request transfer of your data to another organization</li>
                  <li><strong>Objection:</strong> Object to processing based on legitimate interests</li>
                  <li><strong>Withdraw Consent:</strong> Withdraw consent at any time</li>
               </ul>
               
               <div class="contact-card">
                  <h3>Exercise Your Rights</h3>
                  <p>To exercise any of these rights, please contact us at:</p>
                  <a href="mailto:privacy@codelibrary.dev" class="contact-email">privacy@codelibrary.dev</a>
                  <p style="margin-top: 1rem; font-size: 0.9rem;">We will respond within 30 days of receiving your request.</p>
               </div>
            </div>
            
            <div class="policy-section" id="cookies">
               <h2>7. Cookies & Tracking Technologies</h2>
               <p>We use cookies and similar tracking technologies to track activity on our website and store certain information.</p>
               
               <h3>Types of Cookies We Use:</h3>
               <ul class="policy-list">
                  <li><strong>Essential Cookies:</strong> Necessary for the website to function</li>
                  <li><strong>Preference Cookies:</strong> Remember your settings and preferences</li>
                  <li><strong>Analytics Cookies:</strong> Help us understand website usage</li>
                  <li><strong>Marketing Cookies:</strong> Track effectiveness of advertising</li>
               </ul>
               
               <p>You can control cookies through your browser settings. For more detailed information, please see our <a href="cookie_policy.php" style="color: var(--primary); text-decoration: none; font-weight: 600;">Cookie Policy</a>.</p>
            </div>
            
            <div class="policy-section" id="third-party">
               <h2>8. Third-Party Services</h2>
               <p>We use the following third-party services that may collect information:</p>
               
               <ul class="policy-list">
                  <li><span class="data-type data-third-party">Analytics</span> <strong>Google Analytics:</strong> Website usage statistics</li>
                  <li><span class="data-type data-third-party">Hosting</span> <strong>Cloud Service Providers:</strong> Website hosting and infrastructure</li>
                  <li><span class="data-type data-third-party">Email</span> <strong>Email Service Providers:</strong> Transactional and marketing emails</li>
               </ul>
               
               <p>These third parties have their own privacy policies. We encourage you to review them.</p>
            </div>
            
            <div class="policy-section" id="children">
               <h2>9. Children's Privacy</h2>
               <p>Our website is not intended for children under 16 years of age. We do not knowingly collect personal information from children under 16. If you are a parent or guardian and believe your child has provided us with personal information, please contact us immediately.</p>
               
               <p>If we learn we have collected personal information from a child under 16, we will delete that information as quickly as possible.</p>
            </div>
            
            <div class="policy-section" id="changes">
               <h2>10. Changes to This Policy</h2>
               <p>We may update this privacy policy from time to time. We will notify you of any changes by:</p>
               
               <ul class="policy-list">
                  <li>Posting the new privacy policy on this page</li>
                  <li>Updating the "Effective Date" at the top of this policy</li>
                  <li>Sending an email notification for significant changes (if you have provided your email)</li>
               </ul>
               
               <p>You are advised to review this privacy policy periodically for any changes.</p>
            </div>
            
            <div class="policy-section" id="contact">
               <h2>11. Contact Us</h2>
               <p>If you have any questions about this privacy policy, please contact us:</p>
               
               <ul class="policy-list">
                  <li><strong>Email:</strong> privacy@codelibrary.dev</li>
                  <li><strong>Address:</strong> Samuel Hula, [Tvoja ulica a číslo], 968 01 Nová Baňa, Slovensko</li>
                  <li><strong>Website:</strong> codelibrary.dev/contact</li>
               </ul>
               
               <div class="update-info">
                  <p><strong>Effective Date:</strong> December 15, 2024</p>
                  <p><strong>Last Updated:</strong> [Aktuálny dátum]</p>
                  <p>This privacy policy describes our current policies and practices regarding personal data. We reserve the right to amend this policy at any time.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      document.querySelectorAll('.toc-list a').forEach(anchor => {
         anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);
            if (targetElement) {
               window.scrollTo({
                  top: targetElement.offsetTop - 100,
                  behavior: 'smooth'
               });
            }
         });
      });
   </script>
</body>
</html>