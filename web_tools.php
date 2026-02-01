<?php
require_once './assets/config.php';

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
               <!-- Tool 1: Code Formatter -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-code"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Code Formatter</h3>
                     <p>Format and beautify your HTML, CSS, and JavaScript code with proper indentation and structure.</p>
                     <div class="tool-actions">
                           <a href="./tools/code_formatter.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 2: SEO Meta Tags Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-search"></i>
                  </div>
                  <div class="tool-content">
                     <h3>SEO Meta Tags Generator</h3>
                     <p>Generate comprehensive SEO meta tags including Open Graph, Twitter Cards, and standard metadata.</p>
                     <div class="tool-actions">
                        <a href="./tools/seo_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 3: CSS Grid Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-th"></i>
                  </div>
                  <div class="tool-content">
                     <h3>CSS Grid Generator</h3>
                     <p>Visualize and generate CSS Grid code with interactive controls. Create responsive layouts easily.</p>
                     <div class="tool-actions">
                           <a href="./tools/css_grid_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 4: Schema Markup Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-sitemap"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Schema Markup Generator</h3>
                     <p>Generate structured data (JSON-LD) for better SEO and rich results in search engines.</p>
                     <div class="tool-actions">
                           <a href="./tools/schema_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 5: SVG Wave Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-wave-square"></i>
                  </div>
                  <div class="tool-content">
                     <h3>SVG Wave Generator</h3>
                     <p>Create beautiful SVG waves for your designs. Customize curves, complexity, and colors.</p>
                     <div class="tool-actions">
                        <a href="./tools/wave_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 6: Glassmorphism Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-glass-whiskey"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Glassmorphism Generator</h3>
                     <p>Generate glassmorphism effects with background blur, transparency, and color effects.</p>
                     <div class="tool-actions">
                        <a href="./tools/glass_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 7: Fancy Border Radius Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-border-all"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Border Radius Generator</h3>
                     <p>Create custom border-radius shapes with visual controls. Generate CSS for unique corner styles.</p>
                     <div class="tool-actions">
                        <a href="./tools/border_radius_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 8: Robots.txt Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-robot"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Robots.txt Generator</h3>
                     <p>Generate robots.txt files to control search engine crawlers with customizable rules.</p>
                     <div class="tool-actions">
                        <a href="./tools/robots_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 9: Sitemap Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-sitemap"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Sitemap Generator</h3>
                     <p>Create XML sitemaps for search engines with URL prioritization and change frequency.</p>
                     <div class="tool-actions">
                        <a href="./tools/sitemap_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 10: Background Gradient Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-fill-drip"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Background Gradient Generator</h3>
                     <p>Create beautiful CSS gradients with visual controls. Customize colors, stops, and gradient types.</p>
                     <div class="tool-actions">
                        <a href="./tools/gradient_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 11: Perspective Transform Tool -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-cube"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Perspective Transform Tool</h3>
                     <p>Create 3D perspective effects with interactive controls. Visualize and generate CSS 3D transforms.</p>
                     <div class="tool-actions">
                        <a href="./tools/perspective_transform.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 12: Box Shadow Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-square"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Box Shadow Generator</h3>
                     <p>Create beautiful CSS box shadows with real-time preview. Customize offsets, blur, spread, and colors.</p>
                     <div class="tool-actions">
                        <a href="./tools/box_shadow_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>

               <!-- Tool 13: Base64 Encoder/Decoder -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-exchange-alt"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Base64 Encoder/Decoder</h3>
                     <p>Encode text to Base64 or decode Base64 back to plain text. Useful for data transmission and encoding.</p>
                     <div class="tool-actions">
                           <a href="./tools/base64_tool.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 14: URL Encoder/Decoder -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-link"></i>
                  </div>
                  <div class="tool-content">
                     <h3>URL Encoder/Decoder</h3>
                     <p>Encode special characters in URLs or decode encoded URLs back to readable format.</p>
                     <div class="tool-actions">
                           <a href="./tools/url_encoder.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 15: Password Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-key"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Password Generator</h3>
                     <p>Generate strong, secure passwords with customizable length and character types.</p>
                     <div class="tool-actions">
                           <a href="./tools/password_generator.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 16: Color Palette Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                     <i class="fas fa-palette"></i>
                  </div>
                  <div class="tool-content">
                     <h3>Color Palette Generator</h3>
                     <p>Create beautiful color schemes for your projects with our advanced color palette generator.</p>
                     <div class="tool-actions">
                           <a href="./tools/color_palette.php" class="tool-btn primary">Use Tool</a>
                     </div>
                  </div>
               </div>
               
               <!-- Tool 17: Lorem Ipsum Generator -->
               <div class="tool-card">
                  <div class="tool-icon">
                        <i class="fas fa-paragraph"></i>
                  </div>
                  <div class="tool-content">
                        <h3>Lorem Ipsum Generator</h3>
                        <p>Generate placeholder text for your projects. Customize paragraphs, sentences, and word count.</p>
                        <div class="tool-actions">
                           <a href="./tools/lorem_ipsum_generator.php" class="tool-btn primary">Use Tool</a>
                        </div>
                  </div>
               </div>
               
               <!-- Tool 18: Pixel to REM/EM Converter -->
               <div class="tool-card">
                  <div class="tool-icon">
                        <i class="fas fa-ruler-combined"></i>
                  </div>
                  <div class="tool-content">
                        <h3>Pixel to REM/EM Converter</h3>
                        <p>Convert pixel values to REM and EM units with customizable base font sizes for responsive design.</p>
                        <div class="tool-actions">
                           <a href="./tools/pixel_converter.php" class="tool-btn primary">Use Tool</a>
                        </div>
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