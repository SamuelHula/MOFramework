<?php
// web_tools.php
require_once './assets/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

$current_page = 'web_tools';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Web Tools - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   <style>
      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }
      body {
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         min-height: 100vh;
         font-family: var(--text_font);
      }
      #header{
         height: 10vh;
      }
      .tools-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .tools-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .tools-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .tools-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .tools-intro {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
         text-align: center;
      }
      .tools-intro h2 {
         font-size: 1.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .tools-intro p {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
      }
      .tools-grid {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .tool-card {
         background: white;
         border-radius: 15px;
         overflow: hidden;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         transition: all 0.3s ease;
         display: flex;
         flex-direction: column;
      }
      .tool-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }
      .tool-card.coming-soon {
         opacity: 0.8;
         position: relative;
      }
      .tool-card.coming-soon::after {
         content: 'Coming Soon';
         position: absolute;
         top: 10px;
         right: -35px;
         background: var(--secondary);
         color: white;
         padding: 0.3rem 2.5rem;
         transform: rotate(45deg);
         font-size: 0.8rem;
         font-weight: 600;
      }
      .tool-icon {
         background: var(--primary);
         color: white;
         font-size: 2.5rem;
         padding: 2rem;
         text-align: center;
      }
      .tool-content {
         padding: 1.5rem;
         flex-grow: 1;
         display: flex;
         flex-direction: column;
      }
      .tool-content h3 {
         font-size: 1.5rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .tool-content p {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
         margin-bottom: 1.5rem;
         flex-grow: 1;
      }
      .tool-actions {
         display: flex;
         gap: 1rem;
      }
      .tool-btn {
         padding: 0.8rem 1.5rem;
         border-radius: 8px;
         text-decoration: none;
         font-weight: 500;
         transition: all 0.3s;
         text-align: center;
         flex: 1;
      }
      .tool-btn.primary {
         background: var(--primary);
         color: white;
      }
      .tool-btn.primary:hover {
         background: var(--secondary);
      }
      .tool-btn.secondary {
         background: transparent;
         color: var(--primary);
         border: 2px solid var(--primary);
      }
      .tool-btn.secondary:hover {
         background: var(--primary);
         color: white;
      }
      .tool-btn.disabled {
         background: #ccc;
         color: #666;
         cursor: not-allowed;
         opacity: 0.7;
      }
      .placeholder-section {
         background: white;
         padding: 4rem 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         text-align: center;
         margin-top: 3rem;
      }
      .placeholder-section h2 {
         font-size: 2rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .placeholder-section p {
         color: var(--text-color);
         opacity: 0.8;
         margin-bottom: 2rem;
         font-size: 1.1rem;
      }
      @media screen and (max-width: 1200px) {
         .tools-container {
               padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .tools-container {
               padding: 2% 1rem 5%;
         }
         .tools-header h1 {
               font-size: 2.2rem;
         }
         .tools-grid {
               grid-template-columns: 1fr;
         }
         .tool-actions {
               flex-direction: column;
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
      <section class="tools-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="tools-header scroll-effect">
               <h1>Web Development Tools</h1>
               <p>Collection of useful tools for developers</p>
         </div>
         
         <div class="tools-intro scroll-effect">
               <h2>Enhance Your Development Workflow</h2>
               <p>These tools are designed to help you work more efficiently. From code formatting to validation, find everything you need in one place.</p>
         </div>
         
         <div class="tools-grid scroll-effect">
               <!-- Tool 1 -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-code"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Code Formatter</h3>
                     <p>Format and beautify your HTML, CSS, and JavaScript code with proper indentation and structure.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary">Use Tool</a>
                           <a href="#" class="tool-btn secondary">Learn More</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 2 -->
               <div class="tool-card coming-soon">
                  <div class="tool-icon">
                     <i class="fas fa-palette"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Color Palette Generator</h3>
                     <p>Create beautiful color schemes for your projects with our advanced color palette generator.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary disabled">Coming Soon</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 3 -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-compress-arrows-alt"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Image Optimizer</h3>
                     <p>Compress and optimize images for web without losing quality. Supports PNG, JPG, and WebP.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary">Use Tool</a>
                           <a href="#" class="tool-btn secondary">Learn More</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 4 -->
               <div class="tool-card coming-soon">
                  <div class="tool-icon">
                     <i class="fas fa-shield-alt"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Security Scanner</h3>
                     <p>Check your code for common security vulnerabilities and get suggestions for improvement.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary disabled">Coming Soon</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 5 -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-tachometer-alt"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Performance Analyzer</h3>
                     <p>Analyze your website's performance and get recommendations for optimization.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary">Use Tool</a>
                           <a href="#" class="tool-btn secondary">View Demo</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 6 -->
               <div class="tool-card coming-soon">
                  <div class="tool-icon">
                     <i class="fas fa-mobile-alt"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Responsive Tester</h3>
                     <p>Test how your website looks on different devices and screen sizes in real-time.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary disabled">Coming Soon</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 7 -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-search"></i>
                  </div>
                  <div class="tool-content">
                     <h3>SEO Analyzer</h3>
                     <p>Check your website's SEO health and get actionable recommendations for improvement.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary">Use Tool</a>
                           <a href="#" class="tool-btn secondary">Guide</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 8 -->
               <div class="tool-card coming-soon">
                  <div class="tool-icon">
                     <i class="fas fa-database"></i>
                  </div>
                  <div class="tool-content">
                     <h3>JSON Formatter</h3>
                     <p>Format, validate, and beautify JSON data with syntax highlighting and error detection.</p>
                     <div class="tool-actions">
                           <a href="#" class="tool-btn primary disabled">Coming Soon</a>
                     </div>
                  </div>
               </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
</body>
</html>