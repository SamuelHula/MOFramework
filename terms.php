<?php
require_once './assets/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Terms of Service - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      .terms-content {
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
      }
      .terms-section,
      .terms-section * {
         display: block !important;
         visibility: visible !important;
         opacity: 1 !important;
         color: #1f2937 !important;
      }
      #header {
         height: 10vh;
      }
      .terms-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, var(--back-light) 0%, #f8f9fa 50%, var(--back-dark) 100%);
         position: relative;
      }
      .terms-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .terms-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .terms-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .terms-content {
         background: white;
         padding: 3rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
      }
      .terms-section {
         margin-bottom: 2.5rem;
         scroll-margin-top: 20px;
      }
      .terms-section h2 {
         color: var(--primary);
         margin-bottom: 1rem;
         font-size: 1.8rem;
         padding-bottom: 0.5rem;
         border-bottom: 2px solid var(--back-dark);
      }
      .terms-section h3 {
         color: var(--text-color);
         margin: 1.5rem 0 1rem 0;
         font-size: 1.3rem;
      }
      .terms-section p {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
         margin-bottom: 1rem;
      }
      .terms-list {
         list-style-type: disc;
         margin-left: 2rem;
         margin-bottom: 1rem;
      }
      .terms-list li {
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
      .notice-box {
         background: #fff3cd;
         border-left: 4px solid #ffc107;
         padding: 1.5rem;
         border-radius: 8px;
         margin: 1.5rem 0;
      }
      .notice-box h3 {
         color: #856404;
         margin-top: 0;
      }
      .notice-box p {
         color: #856404;
         opacity: 0.9;
      }
      .warning-box {
         background: #f8d7da;
         border-left: 4px solid #dc3545;
         padding: 1.5rem;
         border-radius: 8px;
         margin: 1.5rem 0;
      }
      .warning-box h3 {
         color: #721c24;
         margin-top: 0;
      }
      .warning-box p {
         color: #721c24;
         opacity: 0.9;
      }
      .info-box {
         background: #d1ecf1;
         border-left: 4px solid #17a2b8;
         padding: 1.5rem;
         border-radius: 8px;
         margin: 1.5rem 0;
      }
      .info-box h3 {
         color: #0c5460;
         margin-top: 0;
      }
      .info-box p {
         color: #0c5460;
         opacity: 0.9;
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
      .agreement-check {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin: 2rem 0;
         padding: 1.5rem;
         background: #e8f5e9;
         border-radius: 8px;
      }
      .agreement-check input[type="checkbox"] {
         width: 20px;
         height: 20px;
      }
      .agreement-check label {
         font-weight: 600;
         color: var(--text-color);
      }
      @media screen and (max-width: 768px) {
         .terms-container {
            padding: 2.5% 5% 5%;
         }
         .terms-content {
            padding: 2rem;
         }
         .terms-header h1 {
            font-size: 2.2rem;
         }
      }
      @media screen and (max-width: 1024px) {
         .terms-container {
            padding: 2.5% 8% 5%;
         }
         .terms-content {
            padding: 2.5rem;
         }
         .terms-header h1 {
            font-size: 2.5rem;
         }
         .table-of-contents {
            padding: 1.25rem;
         }
      }

      @media screen and (max-width: 768px) {
         .terms-container {
            padding: 2.5% 5% 5%;
         }
         .terms-content {
            padding: 2rem;
         }
         .terms-header h1 {
            font-size: 2.2rem;
         }
         .terms-section h2 {
            font-size: 1.6rem;
         }
         .terms-section h3 {
            font-size: 1.2rem;
         }
         .table-of-contents h3 {
            font-size: 1.3rem;
         }
         .toc-list a {
            font-size: 0.95rem;
         }
         .notice-box,
         .warning-box,
         .info-box {
            padding: 1.25rem;
         }
         .agreement-check {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.75rem;
         }
      }

      @media screen and (max-width: 480px) {
         .terms-container {
            padding: 2.5% 3% 5%;
         }
         .terms-content {
            padding: 1.5rem;
         }
         .terms-header h1 {
            font-size: 1.8rem;
         }
         .terms-header p {
            font-size: 1rem;
         }
         .terms-section h2 {
            font-size: 1.4rem;
         }
         .terms-section h3 {
            font-size: 1.1rem;
         }
         .terms-section p,
         .terms-list li {
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
         .notice-box,
         .warning-box,
         .info-box {
            padding: 1rem;
         }
         .notice-box h3,
         .warning-box h3,
         .info-box h3 {
            font-size: 1.1rem;
         }
         .agreement-check {
            padding: 1rem;
         }
         .agreement-check label {
            font-size: 0.95rem;
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
         .terms-container {
            padding: 2.5% 2% 5%;
         }
         .terms-content {
            padding: 1rem;
         }
         .terms-header h1 {
            font-size: 1.6rem;
         }
         .terms-section h2 {
            font-size: 1.3rem;
         }
         .table-of-contents {
            padding: 0.875rem;
         }
         .toc-list a svg {
            width: 14px;
            height: 14px;
         }
         .agreement-check label {
            font-size: 0.9rem;
         }
      }
      @media (hover: none) and (pointer: coarse) {
         .toc-list a {
            padding: 0.5rem 0;
            min-height: 44px;
         }
         .agreement-check {
            min-height: 44px;
         }
         .agreement-check input[type="checkbox"] {
            min-width: 20px;
            min-height: 20px;
         }
      }
      @media screen and (max-width: 768px) {
         .terms-content + button {
            bottom: 10px;
            right: 10px;
            padding: 8px 16px;
            font-size: 0.9rem;
         }
      }

      @media screen and (max-width: 480px) {
         .terms-content + button {
            padding: 6px 12px;
            font-size: 0.8rem;
            bottom: 5px;
            right: 5px;
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
      <section class="terms-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="terms-header scroll-effect">
            <h1>Terms of Service</h1>
            <p>Effective Date: December 15, 2024 | Last Updated: December 15, 2024</p>
         </div>
         
         <div class="terms-content scroll-effect">
            <div class="notice-box">
               <h3>⚠️ Important Notice</h3>
               <p>By accessing or using Code Library, you agree to be bound by these Terms of Service. If you disagree with any part of the terms, you may not access our services.</p>
            </div>
            
            <div class="table-of-contents">
               <h3>📋 Table of Contents</h3>
               <ul class="toc-list">
                  <li><a href="#acceptance"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> 1. Acceptance of Terms</a></li>
                  <li><a href="#accounts"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/></svg> 2. User Accounts</a></li>
                  <li><a href="#conduct"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 3h-4.18C14.4 1.84 13.3 1 12 1c-1.3 0-2.4.84-2.82 2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 0c.55 0 1 .45 1 1s-.45 1-1 1-1-.45-1-1 .45-1 1-1zm4 12h-4v3l-5-5 5-5v3h4v4z"/></svg> 3. User Conduct</a></li>
                  <li><a href="#content"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/></svg> 4. User Content</a></li>
                  <li><a href="#intellectual"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z"/></svg> 5. Intellectual Property</a></li>
                  <li><a href="#liability"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/></svg> 6. Limitation of Liability</a></li>
                  <li><a href="#termination"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z"/></svg> 7. Termination</a></li>
                  <li><a href="#governing"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg> 8. Governing Law</a></li>
                  <li><a href="#changes"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21 10.12h-6.78l2.74-2.82c-2.73-2.7-7.15-2.8-9.88-.1-2.73 2.71-2.73 7.08 0 9.79 2.73 2.71 7.15 2.71 9.88 0C18.32 15.65 19 14.08 19 12.1h2c0 1.98-.88 4.55-2.64 6.29-3.51 3.48-9.21 3.48-12.72 0-3.5-3.47-3.53-9.11-.02-12.58 3.51-3.47 9.14-3.47 12.65 0L21 3v7.12z"/></svg> 9. Changes to Terms</a></li>
                  <li><a href="#contact"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg> 10. Contact Information</a></li>
               </ul>
            </div>
            
            <div class="agreement-check">
               <input type="checkbox" id="agree-check">
               <label for="agree-check">I have read and agree to these Terms of Service</label>
            </div>
            
            <div class="terms-section" id="acceptance">
               <h2>1. Acceptance of Terms</h2>
               <p>By accessing and using Code Library ("the Service"), you accept and agree to be bound by the terms and provision of this agreement. In addition, when using this Service, you shall be subject to any posted guidelines or rules applicable to such services.</p>
               
               <h3>Eligibility</h3>
               <p>You must be at least 16 years of age to use this Service. By using the Service, you represent and warrant that you are at least 16 years old.</p>
               
               <div class="info-box">
                  <h3>📝 Agreement to Terms</h3>
                  <p>Your use of the Service constitutes your agreement to all terms, conditions, and notices contained in these Terms of Service.</p>
               </div>
            </div>
            
            <div class="terms-section" id="accounts">
               <h2>2. User Accounts</h2>
               
               <h3>Account Creation</h3>
               <p>To access certain features of the Service, you may be required to create an account. You agree to:</p>
               <ul class="terms-list">
                  <li>Provide accurate, current, and complete information during registration</li>
                  <li>Maintain and promptly update your account information</li>
                  <li>Maintain the security of your password and accept all risks of unauthorized access</li>
                  <li>Notify us immediately of any unauthorized use of your account</li>
                  <li>Take responsibility for all activities that occur under your account</li>
               </ul>
               
               <h3>Account Termination</h3>
               <p>We reserve the right to suspend or terminate your account at our sole discretion if we suspect:</p>
               <ul class="terms-list">
                  <li>Violation of these Terms of Service</li>
                  <li>Fraudulent, abusive, or illegal activity</li>
                  <li>Unauthorized access attempts</li>
                  <li>Extended periods of inactivity (typically 12 months or more)</li>
               </ul>
            </div>
            
            <div class="terms-section" id="conduct">
               <h2>3. User Conduct</h2>
               <p>You agree not to use the Service to:</p>
               
               <ul class="terms-list">
                  <li>Violate any laws, regulations, or third-party rights</li>
                  <li>Post, upload, or distribute any unlawful, defamatory, harassing, abusive, fraudulent, or obscene content</li>
                  <li>Impersonate any person or entity or falsely state your affiliation</li>
                  <li>Interfere with or disrupt the Service or servers/networks connected to the Service</li>
                  <li>Attempt to gain unauthorized access to any portion of the Service</li>
                  <li>Upload viruses, malware, or any other malicious code</li>
                  <li>Engage in any activity that could damage, disable, overburden, or impair the Service</li>
                  <li>Use automated systems (bots, scrapers, etc.) to access the Service without permission</li>
                  <li>Reverse engineer, decompile, or disassemble any part of the Service</li>
               </ul>
               
               <div class="warning-box">
                  <h3>🚫 Prohibited Activities</h3>
                  <p>Violation of these conduct rules may result in immediate account termination and legal action. We reserve the right to investigate and prosecute violations to the fullest extent of the law.</p>
               </div>
            </div>
            
            <div class="terms-section" id="content">
               <h2>4. User Content</h2>
               
               <h3>Content Ownership</h3>
               <p>You retain ownership of any content you submit, post, or display on or through the Service ("User Content"). By submitting User Content, you grant us a worldwide, non-exclusive, royalty-free license to use, reproduce, modify, adapt, publish, and display such content.</p>
               
               <h3>Content Guidelines</h3>
               <p>User Content must:</p>
               <ul class="terms-list">
                  <li>Be original or you must have the right to share it</li>
                  <li>Comply with all applicable laws and regulations</li>
                  <li>Not infringe any third-party rights</li>
                  <li>Not contain malicious code or viruses</li>
                  <li>Not contain personally identifiable information of others without consent</li>
               </ul>
               
               <h3>Content Moderation</h3>
               <p>We reserve the right to:</p>
               <ul class="terms-list">
                  <li>Remove any User Content that violates these Terms</li>
                  <li>Monitor User Content for compliance</li>
                  <li>Disable access to any User Content at our discretion</li>
               </ul>
            </div>
            
            <div class="terms-section" id="intellectual">
               <h2>5. Intellectual Property</h2>
               
               <h3>Our Property</h3>
               <p>The Service and its original content, features, and functionality are owned by Code Library and are protected by international copyright, trademark, patent, trade secret, and other intellectual property laws.</p>
               
               <h3>Your Property</h3>
               <p>You retain all intellectual property rights to your User Content. However, you grant us a license to use your content as described in Section 4.</p>
               
               <h3>Third-Party Property</h3>
               <p>All third-party trademarks, service marks, logos, and brand names are the property of their respective owners.</p>
               
               <div class="info-box">
                  <h3>💡 License to Use</h3>
                  <p>We grant you a limited, non-exclusive, non-transferable, revocable license to use the Service for personal or internal business purposes, subject to these Terms.</p>
               </div>
            </div>
            
            <div class="terms-section" id="liability">
               <h2>6. Limitation of Liability</h2>
               
               <h3>Disclaimer of Warranties</h3>
               <p>THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED. WE DISCLAIM ALL WARRANTIES, INCLUDING BUT NOT LIMITED TO IMPLIED WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, AND NON-INFRINGEMENT.</p>
               
               <h3>Limitation of Liability</h3>
               <p>TO THE MAXIMUM EXTENT PERMITTED BY LAW, CODE LIBRARY SHALL NOT BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, INCLUDING WITHOUT LIMITATION, LOSS OF PROFITS, DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES.</p>
               
               <h3>Maximum Liability</h3>
               <p>OUR TOTAL LIABILITY TO YOU FOR ALL CLAIMS ARISING OUT OF OR RELATING TO THE SERVICE SHALL NOT EXCEED THE AMOUNT YOU HAVE PAID TO US IN THE PAST SIX MONTHS.</p>
               
               <div class="warning-box">
                  <h3>⚠️ No Guarantees</h3>
                  <p>We do not guarantee that the Service will be uninterrupted, timely, secure, or error-free. We do not guarantee the accuracy or completeness of any content on the Service.</p>
               </div>
            </div>
            
            <div class="terms-section" id="termination">
               <h2>7. Termination</h2>
               
               <h3>By You</h3>
               <p>You may terminate your account at any time by contacting us or using the account deletion feature in your settings.</p>
               
               <h3>By Us</h3>
               <p>We may terminate or suspend your account immediately, without prior notice or liability, for any reason, including without limitation if you breach these Terms.</p>
               
               <h3>Effects of Termination</h3>
               <p>Upon termination:</p>
               <ul class="terms-list">
                  <li>Your right to use the Service will immediately cease</li>
                  <li>We may delete your account and associated data</li>
                  <li>Provisions that should survive termination will remain in effect</li>
               </ul>
            </div>
            
            <div class="terms-section" id="governing">
               <h2>8. Governing Law</h2>
               
               <h3>Jurisdiction</h3>
               <p>These Terms shall be governed and construed in accordance with the laws of the Slovak Republic, without regard to its conflict of law provisions.</p>
               
               <h3>Dispute Resolution</h3>
               <p>Any disputes arising out of or relating to these Terms or the Service shall be resolved by the competent courts of the Slovak Republic.</p>
               
               <h3>Consumer Rights</h3>
               <p>If you are a consumer residing in the European Union, you benefit from any mandatory provisions of the law of the country where you are resident. Nothing in these Terms affects your rights as a consumer to rely on such mandatory provisions.</p>
            </div>
            
            <div class="terms-section" id="changes">
               <h2>9. Changes to Terms</h2>
               
               <p>We reserve the right, at our sole discretion, to modify or replace these Terms at any time. If a revision is material, we will provide at least 30 days' notice prior to any new terms taking effect.</p>
               
               <h3>Your Acceptance</h3>
               <p>By continuing to access or use our Service after any revisions become effective, you agree to be bound by the revised terms. If you do not agree to the new terms, you must stop using the Service.</p>
               
               <div class="info-box">
                  <h3>🔄 Notification of Changes</h3>
                  <p>We will notify you of significant changes via email or through a notice on our website. It is your responsibility to review these Terms periodically for changes.</p>
               </div>
            </div>
            
            <div class="terms-section" id="contact">
               <h2>10. Contact Information</h2>
               <p>If you have any questions about these Terms of Service, please contact us:</p>
               
               <ul class="terms-list">
                  <li><strong>Email:</strong> legal@codelibrary.dev</li>
                  <li><strong>Address:</strong> Samuel Hula, [Your Street and Number], 968 01 Nová Baňa, Slovakia</li>
                  <li><strong>Website:</strong> codelibrary.dev/contact</li>
               </ul>
               
               <div class="contact-card">
                  <h3>Need Help?</h3>
                  <p>Our support team is here to answer any questions you may have about our Terms of Service.</p>
                  <a href="mailto:support@codelibrary.dev" class="contact-email">support@codelibrary.dev</a>
               </div>
               
               <div class="update-info">
                  <p><strong>Effective Date:</strong> February 21, 2025</p>
                  <p><strong>Last Updated:</strong> February 21, 2025</p>
                  <p>These Terms of Service constitute the entire agreement between you and Code Library regarding the Service and supersede any prior agreements.</p>
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
      
      const agreeCheckbox = document.getElementById('agree-check');
      if (agreeCheckbox) {
         agreeCheckbox.addEventListener('change', function() {
            if (this.checked) {
               this.parentElement.style.background = '#d4edda';
               this.parentElement.style.borderLeft = '4px solid #28a745';
            } else {
               this.parentElement.style.background = '#e8f5e9';
               this.parentElement.style.borderLeft = '4px solid var(--back-dark)';
            }
         });
      }
      
      const printButton = document.createElement('button');
      printButton.innerHTML = '🖨️ Print These Terms';
      printButton.style.cssText = `
         position: fixed;
         bottom: 20px;
         right: 20px;
         padding: 10px 20px;
         background: var(--primary);
         color: white;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         z-index: 1000;
         box-shadow: 0 2px 10px rgba(0,0,0,0.2);
      `;
      printButton.addEventListener('click', () => window.print());
      document.body.appendChild(printButton);
   </script>
</body>
</html>