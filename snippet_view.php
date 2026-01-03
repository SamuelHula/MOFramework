<?php
// snippet_view.php
require_once './assets/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

if (!isset($_GET['id'])) {
   header("Location: snippets_catalog.php");
   exit;
}

$snippet_id = intval($_GET['id']);

// Fetch snippet details
try {
   $stmt = $pdo->prepare("
      SELECT s.*, c.name as category_name, 
            a.first_name as admin_first, a.last_name as admin_last,
            (SELECT COUNT(*) FROM user_favorites WHERE snippet_id = s.id AND user_id = ?) as is_favorite
      FROM snippets s 
      LEFT JOIN categories c ON s.category_id = c.id 
      LEFT JOIN admins a ON s.admin_id = a.id 
      WHERE s.id = ? AND s.is_public = 1
   ");
   $stmt->execute([$_SESSION['user_id'], $snippet_id]);
   $snippet = $stmt->fetch(PDO::FETCH_ASSOC);
   
   if (!$snippet) {
      header("Location: snippets_catalog.php?error=Snippet+not+found");
      exit;
   }
   
   // Increment view count
   $stmt = $pdo->prepare("UPDATE snippets SET views = views + 1 WHERE id = ?");
   $stmt->execute([$snippet_id]);
   
   // Fetch tags
   $stmt = $pdo->prepare("
      SELECT t.name FROM tags t 
      JOIN snippet_tags st ON t.id = st.tag_id 
      WHERE st.snippet_id = ?
   ");
   $stmt->execute([$snippet_id]);
   $tags = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch (PDOException $e) {
   error_log("Failed to fetch snippet: " . $e->getMessage());
   header("Location: snippets_catalog.php?error=Database+error");
   exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title><?php echo htmlspecialchars($snippet['title']); ?> - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github.min.css">
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
      .snippet-view-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .snippet-header {
         background: white;
         padding: 2.5rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 2rem;
         position: relative;
      }
      .snippet-header h1 {
         font-size: 2.5rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .snippet-meta {
         display: flex;
         flex-wrap: wrap;
         gap: 1.5rem;
         margin-bottom: 1.5rem;
         padding-bottom: 1.5rem;
         border-bottom: 1px solid var(--back-dark);
      }
      .meta-item {
         display: flex;
         align-items: center;
         gap: 0.5rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .meta-item i {
         color: var(--primary);
      }
      .language-badge {
         display: inline-block;
         padding: 0.3rem 1rem;
         background: var(--primary);
         color: white;
         border-radius: 20px;
         font-size: 0.9rem;
         font-weight: 500;
      }
      .snippet-actions {
         display: flex;
         gap: 1rem;
         margin-top: 1.5rem;
      }
      .action-btn {
         padding: 0.8rem 1.5rem;
         border-radius: 8px;
         text-decoration: none;
         font-weight: 600;
         transition: all 0.3s;
         border: 2px solid transparent;
         cursor: pointer;
         font-size: 1rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .btn-primary {
         background: var(--primary);
         color: white;
      }
      .btn-primary:hover {
         background: var(--secondary);
         transform: translateY(-2px);
      }
      .btn-outline {
         background: transparent;
         color: var(--text-color);
         border-color: var(--back-dark);
      }
      .btn-outline:hover {
         background: var(--back-dark);
      }
      .favorite-btn {
         background: none;
         border: none;
         color: <?php echo $snippet['is_favorite'] ? '#ff6b6b' : '#ccc'; ?>;
         cursor: pointer;
         font-size: 1.5rem;
         padding: 0.5rem;
         border-radius: 50%;
         transition: all 0.3s;
      }
      .favorite-btn:hover {
         background: rgba(255, 107, 107, 0.1);
         transform: scale(1.1);
      }
      .snippet-content {
         background: white;
         padding: 2.5rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 2rem;
      }
      .section-title {
         font-size: 1.5rem;
         margin-bottom: 1rem;
         color: var(--text-color);
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .section-title i {
         color: var(--primary);
      }
      .snippet-description {
         color: var(--text-color);
         line-height: 1.8;
         margin-bottom: 2rem;
         font-size: 1.1rem;
      }
      .code-container {
         background: #f8f9fa;
         border-radius: 10px;
         overflow: hidden;
         margin-bottom: 2rem;
      }
      .code-header {
         background: #2d2d2d;
         padding: 1rem 1.5rem;
         display: flex;
         justify-content: space-between;
         align-items: center;
      }
      .code-header h4 {
         color: white;
         margin: 0;
         font-size: 1rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .code-actions {
         display: flex;
         gap: 0.5rem;
      }
      .copy-btn {
         background: #444;
         color: white;
         border: none;
         padding: 0.5rem 1rem;
         border-radius: 5px;
         cursor: pointer;
         font-size: 0.9rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
         transition: background 0.3s;
      }
      .copy-btn:hover {
         background: #555;
      }
      .copy-btn.copied {
         background: #4CAF50;
      }
      .code-block {
         padding: 1.5rem;
         margin: 0;
         overflow-x: auto;
      }
      .code-block pre {
         margin: 0;
         font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
         font-size: 0.95rem;
         line-height: 1.5;
      }
      .tags-container {
         display: flex;
         flex-wrap: wrap;
         gap: 0.5rem;
         margin-top: 1rem;
      }
      .tag {
         background: var(--back-light);
         color: var(--text-color);
         padding: 0.3rem 0.8rem;
         border-radius: 20px;
         font-size: 0.9rem;
         text-decoration: none;
         transition: all 0.3s;
      }
      .tag:hover {
         background: var(--primary);
         color: white;
         transform: translateY(-2px);
      }
      .related-snippets {
         background: white;
         padding: 2.5rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      .related-grid {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
         gap: 1.5rem;
         margin-top: 1.5rem;
      }
      .related-card {
         background: var(--back-light);
         padding: 1.5rem;
         border-radius: 10px;
         transition: all 0.3s;
         text-decoration: none;
         display: block;
         border: 1px solid transparent;
      }
      .related-card:hover {
         border-color: var(--primary);
         transform: translateY(-3px);
         box-shadow: 0 5px 15px rgba(48, 188, 237, 0.1);
      }
      .related-card h4 {
         color: var(--text-color);
         margin-bottom: 0.5rem;
      }
      .related-meta {
         display: flex;
         gap: 1rem;
         font-size: 0.9rem;
         color: var(--text-color);
         opacity: 0.7;
      }
      @media screen and (max-width: 1200px) {
         .snippet-view-container {
               padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .snippet-view-container {
               padding: 2% 1rem 5%;
         }
         .snippet-header h1 {
               font-size: 2rem;
         }
         .snippet-meta {
               flex-direction: column;
               gap: 0.8rem;
         }
         .snippet-actions {
               flex-direction: column;
         }
         .related-grid {
               grid-template-columns: 1fr;
         }
         .code-block {
               padding: 1rem;
         }
         .related-snippets,
         .snippet-content,
         .snippet-header{
            padding: 1rem;
         }
         .snippet-description{
            line-height: 1.5;
            font-size: .9rem;
         }     
         .related-card h4{
            font-size: 1rem;
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
      <section class="snippet-view-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="snippet-header scroll-effect">
               <h1><?php echo htmlspecialchars($snippet['title']); ?></h1>
               
               <div class="snippet-meta">
                  <span class="meta-item">
                     <i class="fas fa-code"></i>
                     <span class="language-badge"><?php echo htmlspecialchars($snippet['language']); ?></span>
                  </span>
                  
                  <?php if ($snippet['category_name']): ?>
                  <span class="meta-item">
                     <i class="fas fa-folder"></i>
                     <?php echo htmlspecialchars($snippet['category_name']); ?>
                  </span>
                  <?php endif; ?>
                  
                  <span class="meta-item">
                     <i class="fas fa-eye"></i>
                     <?php echo $snippet['views'] + 1; ?> views
                  </span>
                  
                  <span class="meta-item">
                     <i class="fas fa-calendar"></i>
                     <?php echo date('F j, Y', strtotime($snippet['created_at'])); ?>
                  </span>
                  
                  <?php if ($snippet['admin_first']): ?>
                  <span class="meta-item">
                     <i class="fas fa-user"></i>
                     Added by <?php echo htmlspecialchars($snippet['admin_first'] . ' ' . $snippet['admin_last']); ?>
                  </span>
                  <?php endif; ?>
               </div>
               
               <?php if (!empty($tags)): ?>
               <div class="tags-container">
                  <?php foreach ($tags as $tag): ?>
                     <a href="snippets_catalog.php?search=<?php echo urlencode($tag['name']); ?>" class="tag">
                           #<?php echo htmlspecialchars($tag['name']); ?>
                     </a>
                  <?php endforeach; ?>
               </div>
               <?php endif; ?>
               
               <div class="snippet-actions">
                  <button class="favorite-btn" onclick="toggleFavorite(this)" 
                        data-snippet-id="<?php echo $snippet['id']; ?>"
                        data-is-favorite="<?php echo $snippet['is_favorite'] ? '1' : '0'; ?>"
                        style="color: <?php echo $snippet['is_favorite'] ? '#ff6b6b' : '#ccc'; ?>">
                     <i class="<?php echo $snippet['is_favorite'] ? 'fas' : 'far'; ?> fa-heart"></i>
                  </button>
                  
                  <button class="action-btn btn-primary" onclick="copyCode()">
                     <i class="fas fa-copy"></i> Copy Code
                  </button>
                  
                  <a href="snippets_catalog.php" class="action-btn btn-outline">
                     <i class="fas fa-arrow-left"></i> Back to Catalog
                  </a>
               </div>
         </div>
         
         <div class="snippet-content scroll-effect">
               <h3 class="section-title">
                  <i class="fas fa-file-alt"></i> Description
               </h3>
               <div class="snippet-description">
                  <?php echo nl2br(htmlspecialchars($snippet['description'])); ?>
               </div>
               
               <h3 class="section-title">
                  <i class="fas fa-code"></i> Code
               </h3>
               <div class="code-container">
                  <div class="code-header">
                     <h4>
                           <i class="fas fa-file-code"></i>
                           <?php echo htmlspecialchars($snippet['title']); ?>
                     </h4>
                     <div class="code-actions">
                           <button class="copy-btn" onclick="copyCode()">
                              <i class="fas fa-copy"></i> Copy
                           </button>
                     </div>
                  </div>
                  <div class="code-block">
                     <pre><code class="language-<?php echo htmlspecialchars($snippet['language']); ?>"><?php echo htmlspecialchars($snippet['code']); ?></code></pre>
                  </div>
               </div>
         </div>
         
         <?php
         // Fetch related snippets
         try {
               $stmt = $pdo->prepare("
                  SELECT s.id, s.title, s.language, s.views, s.created_at
                  FROM snippets s
                  WHERE s.id != ? 
                  AND s.is_public = 1 
                  AND (s.category_id = ? OR s.language = ?)
                  ORDER BY s.views DESC
                  LIMIT 3
               ");
               $stmt->execute([$snippet_id, $snippet['category_id'], $snippet['language']]);
               $related_snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);
               
               if (!empty($related_snippets)):
         ?>
         <div class="related-snippets scroll-effect">
               <h3 class="section-title">
                  <i class="fas fa-th-large"></i> Related Snippets
               </h3>
               <div class="related-grid">
                  <?php foreach ($related_snippets as $related): ?>
                  <a href="snippet_view.php?id=<?php echo $related['id']; ?>" class="related-card">
                     <h4><?php echo htmlspecialchars($related['title']); ?></h4>
                     <div class="related-meta">
                           <span class="language-badge"><?php echo htmlspecialchars($related['language']); ?></span>
                           <span><?php echo $related['views']; ?> views</span>
                     </div>
                  </a>
                  <?php endforeach; ?>
               </div>
         </div>
         <?php endif; } catch (PDOException $e) { error_log("Failed to fetch related snippets: " . $e->getMessage()); } ?>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/highlight.min.js"></script>
   <script>
      function toggleFavorite(button) {
         const snippetId = button.getAttribute('data-snippet-id');
         const isFavorite = button.getAttribute('data-is-favorite') === '1';
         const action = isFavorite ? 'remove' : 'add';
         
         fetch('./assets/process_toggle_favorite.php', {
            method: 'POST',
            headers: {
               'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'snippet_id=' + snippetId + '&action=' + action
         })
         .then(response => response.json())
         .then(data => {
            if (data.success) {
               // Update button state
               const icon = button.querySelector('i');
               if (data.is_favorite) {
                  icon.classList.remove('far');
                  icon.classList.add('fas');
                  button.style.color = '#ff6b6b';
                  button.setAttribute('data-is-favorite', '1');
               } else {
                  icon.classList.remove('fas');
                  icon.classList.add('far');
                  button.style.color = '#ccc';
                  button.setAttribute('data-is-favorite', '0');
               }
               // Show notification
               showNotification(data.message || 'Favorite updated');
            } else {
               alert(data.message || 'An error occurred');
            }
         })
         .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again.');
         });
      }
   </script>
   <script src="./js/notifications.js"></script>
</body>
</html>