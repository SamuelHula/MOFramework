<?php
// favorites.php
require_once './assets/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

$current_page = 'favorites';

// Get pagination parameters
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Fetch user's favorite snippets
try {
   $query = "SELECT s.*, c.name as category_name 
            FROM snippets s 
            LEFT JOIN categories c ON s.category_id = c.id 
            JOIN user_favorites uf ON s.id = uf.snippet_id 
            WHERE uf.user_id = ? AND s.is_public = 1 
            ORDER BY uf.created_at DESC 
            LIMIT ? OFFSET ?";
   
   $stmt = $pdo->prepare($query);
   $stmt->execute([$_SESSION['user_id'], $limit, $offset]);
   $favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
   // Count total favorites
   $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM user_favorites WHERE user_id = ?");
   $stmt->execute([$_SESSION['user_id']]);
   $totalFavorites = $stmt->fetch()['total'];
   $totalPages = ceil($totalFavorites / $limit);
   
} catch (PDOException $e) {
   error_log("Failed to fetch favorites: " . $e->getMessage());
   $favorites = [];
   $totalFavorites = 0;
   $totalPages = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>My Favorites - Code Library</title>
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
      .favorites-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .favorites-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .favorites-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .favorites-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .favorites-count {
         background: white;
         padding: 1.5rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 2rem;
         text-align: center;
      }
      .favorites-count h3 {
         font-size: 1.5rem;
         margin-bottom: 0.5rem;
         color: var(--text-color);
      }
      .count-number {
         font-size: 2.5rem;
         font-weight: 700;
         color: var(--primary);
         display: block;
      }
      .favorites-grid {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .favorite-card {
         background: white;
         border-radius: 15px;
         overflow: hidden;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         transition: all 0.3s ease;
         display: flex;
         flex-direction: column;
         position: relative;
      }
      .favorite-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }
      .remove-favorite {
         position: absolute;
         top: 1rem;
         right: 1rem;
         background: rgba(255, 107, 107, 0.9);
         color: white;
         border: none;
         width: 36px;
         height: 36px;
         border-radius: 50%;
         cursor: pointer;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1.2rem;
         transition: all 0.3s;
         z-index: 2;
      }
      .remove-favorite:hover {
         background: #ff5252;
         transform: scale(1.1);
      }
      .snippet-header {
         padding: 1.5rem;
         border-bottom: 1px solid var(--back-dark);
      }
      .snippet-title {
         font-size: 1.3rem;
         font-weight: 600;
         margin-bottom: 0.5rem;
         color: var(--text-color);
      }
      .snippet-meta {
         display: flex;
         gap: 1rem;
         font-size: 0.9rem;
         color: var(--text-color);
         opacity: 0.7;
      }
      .language-badge {
         display: inline-block;
         padding: 0.2rem 0.8rem;
         background: var(--primary);
         color: white;
         border-radius: 20px;
         font-size: 0.8rem;
         font-weight: 500;
      }
      .snippet-content {
         padding: 1.5rem;
         flex: 1;
      }
      .snippet-description {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
         margin-bottom: 1.5rem;
         font-size: 0.95rem;
      }
      .snippet-preview {
         background: #f8f9fa;
         border-radius: 8px;
         padding: 1rem;
         font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
         font-size: 0.85rem;
         color: #333;
         max-height: 150px;
         overflow: hidden;
         position: relative;
      }
      .snippet-preview::after {
         content: '';
         position: absolute;
         bottom: 0;
         left: 0;
         right: 0;
         height: 40px;
         background: linear-gradient(to bottom, transparent, #f8f9fa);
      }
      .snippet-actions {
         padding: 1.5rem;
         border-top: 1px solid var(--back-dark);
      }
      .view-btn {
         display: block;
         padding: 0.8rem;
         background: var(--primary);
         color: white;
         text-decoration: none;
         border-radius: 8px;
         font-weight: 500;
         transition: all 0.3s;
         text-align: center;
      }
      .view-btn:hover {
         background: var(--secondary);
      }
      .no-favorites {
         text-align: center;
         padding: 4rem;
         background: white;
         border-radius: 15px;
         grid-column: 1 / -1;
      }
      .no-favorites h3 {
         font-size: 1.5rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .no-favorites p {
         color: var(--text-color);
         opacity: 0.7;
         margin-bottom: 2rem;
      }
      .pagination {
         display: flex;
         justify-content: center;
         gap: 0.5rem;
         margin-top: 3rem;
      }
      .page-link {
         padding: 0.5rem 1rem;
         background: white;
         border: 1px solid var(--back-dark);
         border-radius: 5px;
         color: var(--text-color);
         text-decoration: none;
         transition: all 0.3s;
      }
      .page-link:hover {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .page-link.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      @media screen and (max-width: 1200px) {
         .favorites-container {
               padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .favorites-container {
               padding: 2% 1rem 5%;
         }
         .favorites-header h1 {
               font-size: 2.2rem;
         }
         .favorites-grid {
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
      <section class="favorites-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="favorites-header scroll-effect">
               <h1>My Favorite Snippets</h1>
               <p>Your saved code snippets</p>
         </div>
         
         <div class="favorites-count scroll-effect">
               <h3>Total Favorites</h3>
               <span class="count-number"><?php echo $totalFavorites; ?></span>
         </div>
         
         <?php if ($totalFavorites > 0): ?>
               <div class="favorites-grid scroll-effect">
                  <?php foreach ($favorites as $snippet): ?>
                     <div class="favorite-card" id="favorite-<?php echo $snippet['id']; ?>">
                           <button class="remove-favorite" onclick="removeFavorite(<?php echo $snippet['id']; ?>)">
                              <i class="fas fa-times"></i>
                           </button>
                           
                           <div class="snippet-header">
                              <div class="snippet-title">
                                 <?php echo htmlspecialchars($snippet['title']); ?>
                                 <span class="language-badge"><?php echo htmlspecialchars($snippet['language']); ?></span>
                              </div>
                              <div class="snippet-meta">
                                 <?php if ($snippet['category_name']): ?>
                                       <span><?php echo htmlspecialchars($snippet['category_name']); ?></span>
                                 <?php endif; ?>
                                 <span><?php echo date('M j, Y', strtotime($snippet['created_at'])); ?></span>
                                 <span><?php echo $snippet['views']; ?> views</span>
                              </div>
                           </div>
                           
                           <div class="snippet-content">
                              <p class="snippet-description">
                                 <?php echo htmlspecialchars(substr($snippet['description'], 0, 150)); ?>
                                 <?php if (strlen($snippet['description']) > 150): ?>...<?php endif; ?>
                              </p>
                              
                              <div class="snippet-preview">
                                 <pre><code><?php echo htmlspecialchars(substr($snippet['code'], 0, 300)); ?>
<?php if (strlen($snippet['code']) > 300): ?>...<?php endif; ?></code></pre>
                              </div>
                           </div>
                           
                           <div class="snippet-actions">
                              <a href="snippet_view.php?id=<?php echo $snippet['id']; ?>" class="view-btn">
                                 View Full Code
                              </a>
                           </div>
                     </div>
                  <?php endforeach; ?>
               </div>
               
               <?php if ($totalPages > 1): ?>
                  <div class="pagination scroll-effect">
                     <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                           <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                              <?php echo $i; ?>
                           </a>
                     <?php endfor; ?>
                  </div>
               <?php endif; ?>
               
         <?php else: ?>
               <div class="no-favorites scroll-effect">
                  <h3>No favorites yet</h3>
                  <p>Start browsing code snippets and add your favorites to save them here.</p>
                  <a href="snippets_catalog.php" class="primary_btn">Browse Snippets</a>
               </div>
         <?php endif; ?>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      function removeFavorite(snippetId) {
         if (!confirm('Remove this snippet from your favorites?')) {
               return;
         }
         
         fetch('./assets/process_toggle_favorite.php', {
               method: 'POST',
               headers: {
                  'Content-Type': 'application/x-www-form-urlencoded',
               },
               body: 'snippet_id=' + snippetId + '&action=remove'
         })
         .then(response => response.json())
         .then(data => {
               if (data.success) {
                  const card = document.getElementById('favorite-' + snippetId);
                  if (card) {
                     card.style.opacity = '0';
                     card.style.transform = 'translateY(-20px)';
                     setTimeout(() => {
                           card.remove();
                           // Update count
                           const countElement = document.querySelector('.count-number');
                           if (countElement) {
                              const currentCount = parseInt(countElement.textContent);
                              countElement.textContent = currentCount - 1;
                           }
                     }, 300);
                  }
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
</body>
</html>