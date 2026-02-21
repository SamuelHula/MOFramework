<?php
require_once './assets/config.php';
require_once './assets/cookie_functions.php';

if (needs_cookie_consent() && basename($_SERVER['PHP_SELF']) !== 'cookie_consent.php') {
   header("Location: cookie_consent.php");
   exit;
}

try {
   $categoryStmt = $pdo->prepare("SELECT id, name FROM categories ORDER BY name");
   $categoryStmt->execute();
   $allCategories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
   
   $categoryMap = [];
   foreach ($allCategories as $cat) {
      $categoryMap[$cat['name']] = $cat['id'];
   }
} catch (PDOException $e) {
   error_log("Failed to fetch categories: " . $e->getMessage());
   $categoryMap = [];
}

try {
   $recentStmt = $pdo->prepare("
      SELECT s.*, c.name as category_name 
      FROM snippets s 
      LEFT JOIN categories c ON s.category_id = c.id 
      WHERE s.is_public = 1 
      ORDER BY s.created_at DESC 
      LIMIT 3
   ");
   $recentStmt->execute();
   $recentSnippets = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   error_log("Failed to fetch recent snippets: " . $e->getMessage());
   $recentSnippets = [];
}

function normalizeCodeToSameHeight($code, $target_lines = 8) {
   $lines = explode("\n", $code);
   $total_original_lines = count($lines);
   
   $selected_lines = array_slice($lines, 0, $target_lines);
   
   if (count($selected_lines) < $target_lines) {
      $missing_lines = $target_lines - count($selected_lines);
      for ($i = 0; $i < $missing_lines; $i++) {
         $selected_lines[] = "";
      }
   }
   
   $last_line = end($selected_lines);
   $last_tag_open = strrpos($last_line, '<');
   $last_tag_close = strrpos($last_line, '>');
   
   if ($last_tag_open !== false && ($last_tag_close === false || $last_tag_open > $last_tag_close)) {
      array_pop($selected_lines);
      $selected_lines[] = "&lt;...&gt;";
      if (count($selected_lines) < $target_lines) {
         $selected_lines[] = "";
      }
   }
   
   $last_php_open = strrpos($last_line, '<?php');
   $last_php_close = strrpos($last_line, '?>');
   
   if ($last_php_open !== false && ($last_php_close === false || $last_php_open > $last_php_close)) {
      array_pop($selected_lines);
      $selected_lines[] = "&lt;?php ... ?&gt;";
      if (count($selected_lines) < $target_lines) {
         $selected_lines[] = "";
      }
   }
   
   if ($total_original_lines > $target_lines) {
      array_pop($selected_lines);
      $selected_lines[] = "...";
   }
   
   $selected_lines = array_slice($selected_lines, 0, $target_lines);
   
   return implode("\n", $selected_lines);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Homepage</title>
   <meta name="theme-color" content="var(--back-light)" />
   <meta name="background-color" content="var(--back-light)" />
   <meta name="description" content="Discover ready-to-use code snippets, components, and assets to accelerate your web development projects. From CSS animations to PHP utilities.">
   <meta name="keywords" content="code snippets, web development, CSS, JavaScript, PHP, components, assets, free code, programming, frontend, backend, developers">
   <meta name="author" content="Code Library">
   <meta name="robots" content="index, follow">
   <meta property="og:type" content="website">
   <meta property="og:url" content="https://codelibrary.dev/">
   <meta property="og:title" content="Code Library - Free Code Snippets for Developers">
   <meta property="og:description" content="Discover ready-to-use code snippets, components, and assets to accelerate your web development projects. From CSS animations to PHP utilities.">
   <meta property="og:image" content="https://codelibrary.dev/media/og-image.jpg">
   <meta property="twitter:card" content="summary_large_image">
   <meta property="twitter:url" content="https://codelibrary.dev/">
   <meta property="twitter:title" content="Code Library - Free Code Snippets for Developers">
   <meta property="twitter:description" content="Discover ready-to-use code snippets, components, and assets to accelerate your web development projects. From CSS animations to PHP utilities.">
   <meta property="twitter:image" content="https://codelibrary.dev/media/twitter-image.jpg">
   <meta name="language" content="English">
   <meta name="revisit-after" content="7 days">
   <meta name="rating" content="general">
   <meta name="distribution" content="global">
   <link rel="canonical" href="https://codelibrary.dev/">
   <meta property="og:locale" content="en_US">
   <meta property="og:image:width" content="1200">
   <meta property="og:image:height" content="630">
   <meta property="og:image:type" content="image/jpeg">
   <meta name="twitter:creator" content="@codelibrary">
   <meta name="twitter:site" content="@codelibrary">
   
   <script type="application/ld+json">
      {
         "@context": "https://schema.org",
         "@type": "WebSite",
         "name": "Code Library",
         "url": "https://codelibrary.dev/",
         "description": "A free library of code snippets for developers",
         "sameAs": [],
         "potentialAction": {
            "@type": "SearchAction",
            "target": "https://codelibrary.dev/search?q={search_term_string}",
            "query-input": "required name=search_term_string"
         }
      }
   </script>
   <script type="application/ld+json">
      {
         "@context": "https://schema.org",
         "@type": "ContactPage",
         "name": "Contact Code Library",
         "description": "Get in touch with Code Library for support and inquiries",
         "mainEntity": {
            "@type": "Organization",
            "name": "Code Library",
            "email": "samuel.hula.dev@gmail.com",
            "contactPoint": {
            "@type": "ContactPoint",
            "contactType": "customer service",
            "email": "samuel.hula.dev@gmail.com",
            "availableLanguage": ["English"],
            "areaServed": "Worldwide"
            },
            "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nová Baňa",
            "addressCountry": "SK"
            }
         }
      }
   </script>
   <script type="application/ld+json">
      {
         "@context": "https://schema.org",
         "@type": "WebPage",
         "name": "Code Library Homepage",
         "description": "Free code snippets for developers",
         "publisher": {
            "@type": "Organization",
            "name": "Code Library",
            "url": "https://codelibrary.dev"
         },
         "mainEntity": {
            "@type": "Collection",
            "name": "Code Snippets",
            "numberOfItems": 500,
            "description": "Collection of free code snippets for web development"
         }
      }
   </script>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <script src="./js/fly-in.js"></script>
   <script src="./js/scroll.js"></script>
   <script src="./js/line.js"></script>
</head>
<body>
   <div class="progress-container">
      <div id="scrollProgress"></div>
   </div>
   <header id="header">
      <?php include './assets/nav_bar.php' ?>
      <section id="landing_page">
      <div class="floating-balls">
         <div class="ball ball-1"></div>
         <div class="ball ball-2"></div>
         <div class="ball ball-3"></div>
         <div class="ball ball-4"></div>
         <div class="ball ball-5"></div>
         <div class="ball ball-6"></div>
         <div class="ball ball-7"></div>
         <div class="ball ball-8"></div>
      </div>
      <div class="landing-content scroll-effect">
         <div class="text-content">
            <h1 class="main-title">
               <span class="title-line">Code Library</span>
               <span class="title-line accent">For Developers</span>
            </h1>
            <p class="subtitle">
               Discover ready-to-use code snippets, components, and assets to accelerate your web development projects. From CSS animations to PHP utilities.
            </p>
            <div class="btns">
               <a href="#categories" class="primary_btn">
                  <span>Explore Library</span>
               </a>
               <a href="#process" class="secondary_btn">
                  <span>How It Works</span>
               </a>
            </div>
            <div class="stats-preview">
                  <div class="stat">
                  <span class="number">500+</span>
                  <span class="label">Code Snippets</span>
                  </div>
                  <div class="stat">
                  <span class="number">4</span>
                  <span class="label">Categories</span>
                  </div>
                  <div class="stat">
                  <span class="number">100%</span>
                  <span class="label">Free</span>
                  </div>
               </div>
            </div>
            <div class="visual-content">
               <div class="code-window">
                  <div class="window-header">
                     <div class="window-controls">
                        <span class="control red"></span>
                        <span class="control yellow"></span>
                        <span class="control green"></span>
                     </div>
                     <span class="file-name">responsive-navbar.css</span>
                  </div>
                  <div class="code-content">
                     <pre>
                     <code class="language-css">
   .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem 2rem;
      background: var(--primary);
      position: fixed;
      width: 100%;
      top: 0;
      z-index: 1000;
   }
   @media (max-width: 768px) {
      .navbar {
         flex-direction: column;
      }
   }
                  </code>
                  </pre>
                  </div>
               </div>
            </div>
         </div>
         <div class="scroll-indicator">
            <span>Scroll to explore</span>
            <div class="arrow"></div>
         </div>
      </section>
   </header>
   <main id="main">
      <section id="top_snippets" class="scroll-effect">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         <h2>Recent Code Snippets</h2>
         
         <?php if (empty($recentSnippets)): ?>
            <div class="no-results" style="text-align: center; padding: 4rem; background: white; border-radius: 15px; grid-column: 1 / -1;">
                  <h3 style="font-size: 1.5rem; margin-bottom: 1rem; color: var(--text-color);">No recent snippets available</h3>
                  <p style="color: var(--text-color); opacity: 0.7; margin-bottom: 2rem;">Check back later for new additions.</p>
            </div>
         <?php else: ?>
            <div class="catalog-grid" style="margin-top: 2rem; margin-bottom: 3rem;">
                  <?php foreach ($recentSnippets as $snippet): ?>
                     <?php 
                     $description_preview = strip_tags($snippet['description']);
                     if (strlen($description_preview) > 150) {
                        $description_preview = substr($description_preview, 0, 150) . '...';
                     }
                     
                     $code_preview = htmlspecialchars(substr($snippet['code'], 0, 200));
                     if (strlen($snippet['code']) > 200) {
                        $code_preview .= '...';
                     }
                     
                     $category_name = !empty($snippet['category_name']) ? htmlspecialchars($snippet['category_name']) : 'Uncategorized';
                     ?>
                     
                     <div class="snippet-card">
                        <div class="snippet-header">
                              <div class="snippet-title">
                                 <span><?php echo htmlspecialchars($snippet['title']); ?></span>
                                 <span class="language-badge"><?php echo htmlspecialchars($snippet['language']); ?></span>
                              </div>
                              <div class="snippet-meta">
                                 <span><?php echo $category_name; ?></span>
                                 <span><?php echo date('M j, Y', strtotime($snippet['created_at'])); ?></span>
                                 <span><?php echo $snippet['views']; ?> views</span>
                              </div>
                        </div>
                        
                        <div class="snippet-content">
                              <p class="snippet-description">
                                 <?php echo htmlspecialchars($description_preview); ?>
                              </p>
                              
                              <div class="snippet-preview">
                                 <pre><code><?php echo $code_preview; ?></code></pre>
                              </div>
                        </div>
                        
                        <div class="snippet-actions">
                              <a href="snippet_view.php?id=<?php echo $snippet['id']; ?>" class="view-btn">
                                 View Full Code
                              </a>
                              <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                                 <button class="favorite-btn" onclick="alert('Login to add favorites')">
                                    <i class="far fa-heart"></i>
                                 </button>
                              <?php else: ?>
                                 <button class="favorite-btn" onclick="location.href='signin.php'">
                                    <i class="far fa-heart"></i>
                                 </button>
                              <?php endif; ?>
                        </div>
                     </div>
                  <?php endforeach; ?>
            </div>
            
            <div class="see-all-btn-container">
                  <a href="snippets_catalog.php" class="primary_btn">View All Snippets</a>
            </div>
         <?php endif; ?>
      </section>
      <section id="hero">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         <article class="hero_art scroll-effect">
            <h2>Hero Section</h2>
            <p>
               Our comprehensive code library is designed to help developers of all skill levels build better web applications faster. With carefully curated and tested code snippets, you can eliminate repetitive coding tasks and focus on creating unique features for your projects.
            </p>
            <p>
               Each snippet in our collection is optimized for performance, accessibility, and cross-browser compatibility. We regularly update our library with new content based on current web development trends and community feedback.
            </p>
         </article>
         <figure class="scroll-effect">
            <img src="./media/hero-image1.jpg" alt="Hero section image" title="Hero section image">
         </figure>
      </section>
      <section id="categories">      
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         <h2>Browse Categories</h2>
         <article class="categories_art_wrapper scroll-effect">
               <aside>
                  <figure>
                     <div class="category-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" width="40" height="40">
                              <path fill="var(--back-light)" d="M64 0C28.7 0 0 28.7 0 64V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V160H256c-17.7 0-32-14.3-32-32V0H64zM256 0V128H384L256 0zM96 48c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16zm0 64c0-8.8 7.2-16 16-16h32c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16zm-6.3 71.8c3.7-14 16.4-23.8 30.9-23.8h14.8c14.5 0 27.2 9.7 30.9 23.8l23.5 88.2c1.4 5.4 2.1 10.9 2.1 16.4c0 35.2-28.8 63.7-64 63.7s-64-28.5-64-63.7c0-5.5 .7-11.1 2.1-16.4l23.5-88.2zM112 336c8.8 0 16 7.2 16 16s-7.2 16-16 16s-16-7.2-16-16s7.2-16 16-16zM256 64H112c-8.8 0-16 7.2-16 16s7.2 16 16 16H256c8.8 0 16-7.2 16-16s-7.2-16-16-16zm0 64H112c-8.8 0-16 7.2-16 16s7.2 16 16 16H256c8.8 0 16-7.2 16-16s-7.2-16-16-16z"/>
                           </svg>
                     </div>
                  </figure>
                  <div class="content">
                     <h3>Assets</h3>
                     <p>PHP utilities, server-side scripts, and backend functionality for dynamic web applications</p>
                     <a href="snippets_catalog.php?category=<?php echo isset($categoryMap['Assets']) ? $categoryMap['Assets'] : ''; ?>" class="primary_btn">Explore Assets</a>
                  </div>
               </aside>
               <aside>
                  <figure>
                     <div class="category-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                              <path fill="var(--back-light)" d="M78.6 5C69.1-2.4 55.6-1.5 47 7L7 47c-8.5 8.5-9.4 22-2.1 31.6l80 104c4.5 5.9 11.6 9.4 19 9.4h54.1l109 109c-14.7 29-10 65.4 14.3 89.6l112 112c12.5 12.5 32.8 12.5 45.3 0l64-64c12.5-12.5 12.5-32.8 0-45.3l-112-112c-24.2-24.2-60.6-29-89.6-14.3l-109-109V104c0-7.5-3.5-14.5-9.4-19L78.6 5zM19.9 396.1C7.2 408.8 0 426.1 0 444.1C0 481.6 30.4 512 67.9 512c18 0 35.3-7.2 48-19.9L233.7 374.3c-7.8-20.9-9-43.6-3.6-65.1l-61.7-61.7L19.9 396.1zM512 144c0-10.5-1.1-20.7-3.2-30.5c-2.4-11.2-16.1-14.1-24.2-6l-63.9 63.9c-3 3-7.1 4.7-11.3 4.7H352c-8.8 0-16-7.2-16-16V102.6c0-4.2 1.7-8.3 4.7-11.3l63.9-63.9c8.1-8.1 5.2-21.8-6-24.2C388.7 1.1 378.5 0 368 0C288.5 0 224 64.5 224 144l0 .8 85.3 85.3c36-9.1 75.8 .5 104 28.7L429 274.5c49-23 83-72.8 83-130.5zM424 144a24 24 0 1 0 0-48 24 24 0 1 0 0 48z"/>
                           </svg>
                     </div>
                  </figure>
                  <div class="content">
                     <h3>Components</h3>
                     <p>Essential tools and utilities including CSS reset, frameworks, and development helpers</p>
                     <a href="snippets_catalog.php?category=<?php echo isset($categoryMap['Components']) ? $categoryMap['Components'] : ''; ?>" class="primary_btn">Explore Components</a>
                  </div>
               </aside>
               <aside>
                  <figure>
                     <div class="category-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="40" height="40">
                              <path fill="var(--back-light)" d="M264.5 5.2c14.9-6.9 32.1-6.9 47 0l218.6 101c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 149.8C37.4 145.8 32 137.3 32 128s5.4-17.9 13.9-21.8L264.5 5.2zM476.9 209.6l53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 277.8C37.4 273.8 32 265.3 32 256s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0l152-70.2zm-152 198.2l152-70.2 53.2 24.6c8.5 3.9 13.9 12.4 13.9 21.8s-5.4 17.9-13.9 21.8l-218.6 101c-14.9 6.9-32.1 6.9-47 0L45.9 405.8C37.4 401.8 32 393.3 32 384s5.4-17.9 13.9-21.8l53.2-24.6 152 70.2c23.4 10.8 50.4 10.8 73.8 0z"/>
                           </svg>
                     </div>
                  </figure>
                  <div class="content">
                     <h3>Elements</h3>
                     <p>Beautifully designed HTML & CSS elements including buttons, forms, cards and navigation</p>
                     <a href="snippets_catalog.php?category=<?php echo isset($categoryMap['Elements']) ? $categoryMap['Elements'] : ''; ?>" class="primary_btn">Explore Elements</a>
                  </div>
               </aside>
               <aside>
                  <figure>
                     <div class="category-icon">
                           <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="40" height="40">
                              <path fill="var(--back-light)" d="M392.8 1.2c-17-4.9-34.7 5-39.6 22l-128 448c-4.9 17 5 34.7 22 39.6s34.7-5 39.6-22l128-448c4.9-17-5-34.7-22-39.6zm80.6 120.1c-12.5 12.5-12.5 32.8 0 45.3L562.7 256l-89.4 89.4c-12.5 12.5-12.5 32.8 0 45.3s32.8 12.5 45.3 0l112-112c12.5-12.5 12.5-32.8 0-45.3l-112-112c-12.5-12.5-32.8-12.5-45.3 0zm-306.7 0c-12.5-12.5-32.8-12.5-45.3 0l-112 112c-12.5 12.5-12.5 32.8 0 45.3l112 112c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256l89.4-89.4c12.5-12.5 12.5-32.8 0-45.3z"/>
                           </svg>
                     </div>
                  </figure>
                  <div class="content">
                     <h3>JS Effects</h3>
                     <p>Interactive JavaScript DOM effects, animations, and dynamic user interface enhancements</p>
                     <a href="snippets_catalog.php?category=<?php echo isset($categoryMap['JS Effects']) ? $categoryMap['JS Effects'] : ''; ?>" class="primary_btn">Explore JS Effects</a>
                  </div>
               </aside>
         </article>
      </section>
      <section id="process">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         <h2>How to Use Our Code Snippets</h2>
         <article class="process-wrapper scroll-effect">
            <aside>
               <div class="step-number">01</div>
               <div class="step-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                     <path fill="var(--back-light)" d="M416 208c0 45.9-14.9 88.3-40 122.7L502.6 457.4c12.5 12.5 12.5 32.8 0 45.3s-32.8 12.5-45.3 0L330.7 376c-34.4 25.2-76.8 40-122.7 40C93.1 416 0 322.9 0 208S93.1 0 208 0S416 93.1 416 208zM208 352a144 144 0 1 0 0-288 144 144 0 1 0 0 288z"/>
                  </svg>
               </div>
               <h3>Search & Find</h3>
               <p>Browse our organized categories or use the search function to find exactly what you need. Filter by technology, complexity, or popularity.</p>
            </aside>
            <aside>
               <div class="step-number">02</div>
               <div class="step-icon">
                  <svg xmlns="http://www.w3.org2000/svg" viewBox="0 0 384 512" width="40" height="40">
                     <path fill="var(--back-light)" d="M192 0c-41.8 0-77.4 26.7-90.5 64H64C28.7 64 0 92.7 0 128V448c0 35.3 28.7 64 64 64H320c35.3 0 64-28.7 64-64V128c0-35.3-28.7-64-64-64H282.5C269.4 26.7 233.8 0 192 0zm0 64a32 32 0 1 1 0 64 32 32 0 1 1 0-64zM112 192H272c8.8 0 16 7.2 16 16s-7.2 16-16 16H112c-8.8 0-16-7.2-16-16s7.2-16 16-16z"/>
                  </svg>
               </div>
               <h3>Copy the Code</h3>
               <p>Select and copy the clean, well-documented code with a single click. All snippets include comments and are ready to use.</p>
            </aside>
            <aside>
               <div class="step-number">03</div>
               <div class="step-icon">
                  <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="40" height="40">
                     <path fill="var(--back-light)" d="M160 0c17.7 0 32 14.3 32 32V64H320V32c0-17.7 14.3-32 32-32s32 14.3 32 32V64h32c35.3 0 64 28.7 64 64v32H0V128C0 92.7 28.7 64 64 64h32V32c0-17.7 14.3-32 32-32zM0 192H512V464c0 26.5-21.5 48-48 48H48c-26.5 0-48-21.5-48-48V192zm192 80c0-8.8 7.2-16 16-16h96c8.8 0 16 7.2 16 16v96c0 8.8-7.2 16-16 16H208c-8.8 0-16-7.2-16-16V272z"/>
                  </svg>
               </div>
               <h3>Implement & Customize</h3>
               <p>Paste the code directly into your project and customize it to fit your needs. Modify colors, sizes, and functionality as required.</p>
            </aside>
         </article>
      </section>
      <section id="faq">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         <h2>Frequently Asked Questions</h2>
         <article class="faq-wrapper scroll-effect">
            <div class="faq-item">
               <input type="checkbox" id="faq1" class="faq-toggle">
               <label for="faq1" class="faq-question">
                  <span>How do I use the code snippets?</span>
                  <span class="faq-icon"></span>
               </label>
               <div class="faq-answer">
                  <p>Simply browse our categories, find the code you need, copy it with the copy button, and paste it directly into your project. All snippets are production-ready and include comments for easy customization.</p>
               </div>
            </div>
            <div class="faq-item">
               <input type="checkbox" id="faq2" class="faq-toggle">
               <label for="faq2" class="faq-question">
                  <span>Are the code snippets free to use?</span>
                  <span class="faq-icon"></span>
               </label>
               <div class="faq-answer">
                  <p>Yes! All code snippets in our library are completely free to use in personal and commercial projects. No attribution required, though we appreciate mentions.</p>
               </div>
            </div>
            <div class="faq-item">
               <input type="checkbox" id="faq3" class="faq-toggle">
               <label for="faq3" class="faq-question">
                  <span>Do you provide support for the code?</span>
                  <span class="faq-icon"></span>
               </label>
               <div class="faq-answer">
                  <p>While we don't offer dedicated support, each snippet includes detailed documentation and comments. For community help, you can join our Discord channel where other developers share solutions.</p>
               </div>
            </div>
            <div class="faq-item">
               <input type="checkbox" id="faq4" class="faq-toggle">
               <label for="faq4" class="faq-question">
                  <span>Can I contribute my own code snippets?</span>
                  <span class="faq-icon"></span>
               </label>
               <div class="faq-answer">
                  <p>Absolutely! We welcome contributions from the community. Visit our GitHub repository to submit your code snippets through pull requests. All submissions are reviewed for quality and best practices.</p>
               </div>
            </div>
            <div class="faq-item">
               <input type="checkbox" id="faq5" class="faq-toggle">
               <label for="faq5" class="faq-question">
                  <span>How often is new content added?</span>
                  <span class="faq-icon"></span>
               </label>
               <div class="faq-answer">
                  <p>We add new code snippets weekly. You can subscribe to our newsletter or follow us on social media to get notifications when new content is published.</p>
               </div>
            </div>
            <div class="faq-item">
               <input type="checkbox" id="faq6" class="faq-toggle">
               <label for="faq6" class="faq-question">
                  <span>What technologies are supported?</span>
                  <span class="faq-icon"></span>
               </label>
               <div class="faq-answer">
                  <p>We cover HTML, CSS, JavaScript, PHP, and popular frameworks. Our library focuses on vanilla solutions when possible, but we also include framework-specific snippets for React, Vue, and others.</p>
               </div>
            </div>
         </article>
      </section>
      <section id="contact_form">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         <h2>Contact Us</h2>
         <article class="contact-wrapper scroll-effect">
            <aside class="contact-info">
               <h3>Contact Info</h3>
               <div class="contact-methods">
                  <div class="contact-method">
                     <div class="method-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                           <path fill="var(--primary)" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
                        </svg>
                     </div>
                     <span>Technical support: <a href="mailto:samuel.hula.dev@gmail.com" style="text-decoration: none;">samuel.hula.dev@gmail.com<!--contact@mail.com--></a></span>
                  </div>
                  <div class="contact-method">
                     <div class="method-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                           <path fill="var(--primary)" d="M48 64C21.5 64 0 85.5 0 112c0 15.1 7.1 29.3 19.2 38.4L236.8 313.6c11.4 8.5 27 8.5 38.4 0L492.8 150.4c12.1-9.1 19.2-23.3 19.2-38.4c0-26.5-21.5-48-48-48H48zM0 176V384c0 35.3 28.7 64 64 64H448c35.3 0 64-28.7 64-64V176L294.4 339.2c-22.8 17.1-54 17.1-76.8 0L0 176z"/>
                        </svg>
                     </div>
                     <span>Business Inquiries: <a href="mailto:samuel_hula_dev@proton.me" style="text-decoration: none;">samuel_hula_dev@proton.me<!--contact@mail.com--></a></span>
                  </div>
               </div>
               <div class="contact-details">
                  <div class="contact-detail">
                     <strong>Address:</strong> Nová Baňa, Slovakia
                  </div>
                  <div class="contact-detail">
                     <strong>Phone:</strong> <a href="tel:+421951098064" style="text-decoration: none;">+421 951 098 064</a>
                  </div>
                  <div class="contact-detail">
                     <strong>Email:</strong> <a href="mailto:samuel.hula.dev@gmail.com" style="text-decoration: none;">samuel.hula.dev@gmail.com<!--contact@mail.com--></a>
                  </div>
               </div>
            </aside>
            <aside class="contact-form">
               <h3>Send us a message</h3>
               <form class="message-form" action="./assets/send_message.php" method="POST">
                  <div class="form-row">
                     <div class="form-group">
                        <label for="name">Name:</label>
                        <input type="text" id="name" name="name" required>
                     </div>
                     <div class="form-group">
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                     </div>
                  </div>
                  <div class="form-group">
                     <label for="email">Email:</label>
                     <input type="email" id="email" name="email" required>
                  </div>
                  <div class="form-group">
                     <label for="message">Message:</label>
                     <textarea id="message" name="message" rows="5" required></textarea>
                  </div>
                  <p style="font-size: 0.85rem; margin: 1rem 0; color: var(--text-color); opacity: 0.7;">
                     By submitting this form, you agree to our <a href="privacy.php">Privacy Policy</a>.
                  </p>
                  <button type="submit" class="primary_btn">Send Message</button>
               </form>
            </aside>
         </article>
      </section>
   </main>
   <?php include './assets/footer.php' ?>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const contactForm = document.querySelector('.message-form');
         
         if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                  let valid = true;
                  const inputs = contactForm.querySelectorAll('input[required], textarea[required]');
                  
                  inputs.forEach(input => {
                     if (!input.value.trim()) {
                        valid = false;
                        input.style.borderColor = 'red';
                     } else {
                        input.style.borderColor = '';
                     }
                  });
                  
                  const emailInput = contactForm.querySelector('#email');
                  if (emailInput && !isValidEmail(emailInput.value)) {
                     valid = false;
                     emailInput.style.borderColor = 'red';
                  }
                  
                  if (!valid) {
                     e.preventDefault();
                     alert('Please fill in all required fields correctly.');
                  }
            });
         }
         
         function isValidEmail(email) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return emailRegex.test(email);
         }
      });
   </script>
</body>
</html>