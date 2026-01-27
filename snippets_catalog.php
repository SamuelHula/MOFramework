<?php
// snippets_catalog.php
require_once './assets/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

$current_page = 'snippets';

// Fetch categories for filter
try {
   $categoryQuery = "SELECT id, name FROM categories ORDER BY name";
   $categoryStmt = $pdo->query($categoryQuery);
   $categories = $categoryStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   error_log("Failed to fetch categories: " . $e->getMessage());
   $categories = [];
}

// Get filter parameters
$category = isset($_GET['category']) ? intval($_GET['category']) : null;
$language = isset($_GET['language']) ? $_GET['language'] : null;
$search = isset($_GET['search']) ? trim($_GET['search']) : null;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Build the base query - SIMPLIFIED for better performance
$query = "SELECT SQL_CALC_FOUND_ROWS 
          s.*, 
          c.name as category_name,
          EXISTS(SELECT 1 FROM user_favorites WHERE snippet_id = s.id AND user_id = ?) as is_favorite
          FROM snippets s 
          LEFT JOIN categories c ON s.category_id = c.id 
          WHERE s.is_public = 1";

$params = [$_SESSION['user_id']];

// Add filters
if ($category) {
   $query .= " AND s.category_id = ?";
   $params[] = $category;
}

if ($language) {
   $query .= " AND s.language = ?";
   $params[] = $language;
}

if ($search) {
   $query .= " AND (s.title LIKE ? OR s.description LIKE ?)";
   $searchTerm = "%$search%";
   $params[] = $searchTerm;
   $params[] = $searchTerm;
}

// Add ordering and pagination - ORDER BY ID to maintain consistency
$query .= " ORDER BY s.id DESC LIMIT ? OFFSET ?";

// Prepare and execute the query
$stmt = $pdo->prepare($query);

// Bind parameters - IMPORTANT: Use correct types
for ($i = 0; $i < count($params); $i++) {
   $stmt->bindValue($i + 1, $params[$i]);
}

// Bind LIMIT and OFFSET
$stmt->bindValue(count($params) + 1, $limit, PDO::PARAM_INT);
$stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);

$stmt->execute();
$snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get total count using FOUND_ROWS()
$totalResult = $pdo->query("SELECT FOUND_ROWS()")->fetch();
$totalResults = $totalResult[0];
$totalPages = ceil($totalResults / $limit);

// Fetch distinct languages for filter dropdown
try {
   $languageQuery = "SELECT DISTINCT language FROM snippets WHERE language IS NOT NULL AND language != '' ORDER BY language";
   $languageStmt = $pdo->query($languageQuery);
   $languages = $languageStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   error_log("Failed to fetch languages: " . $e->getMessage());
   $languages = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Code Snippets Catalog - Code Library</title>
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
      .catalog-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .catalog-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .catalog-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .catalog-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .filters-container {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
      }
      .filters-form {
         display: grid;
         grid-template-columns: 2fr 1fr 1fr 1fr;
         gap: 1rem;
         align-items: end;
      }
      .filter-group {
         display: flex;
         flex-direction: column;
      }
      .filter-group label {
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 0.9rem;
      }
      .filter-group input,
      .filter-group select {
         padding: 0.75rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
         background: white;
      }
      .filter-group input:focus,
      .filter-group select:focus {
         outline: none;
         border-color: var(--primary);
      }
      .filter-actions {
         display: flex;
         gap: 0.5rem;
      }
      .filter-btn {
         padding: 0.75rem 1.5rem;
         background: var(--primary);
         color: white;
         border: none;
         border-radius: 8px;
         cursor: pointer;
         font-weight: 600;
         transition: all 0.3s;
         text-decoration: none;
      }
      .filter-btn:hover {
         background: var(--secondary);
         transform: translateY(-2px);
      }
      .clear-btn {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .clear-btn:hover {
         background: var(--back-dark);
      }
      
      /* NEW: Simple grid system */
      .catalog-grid {
         display: flex;
         flex-wrap: wrap;
         gap: 2rem;
         margin-bottom: 3rem;
         justify-content: flex-start;
      }
      
      .snippet-card {
         background: white;
         border-radius: 15px;
         overflow: hidden;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         transition: all 0.3s ease;
         display: flex;
         flex-direction: column;
         width: calc(33.333% - 1.34rem); /* 3 cards per row with gap */
         min-height: 400px;
      }
      
      .snippet-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      }
      
      .snippet-header {
         padding: 1.5rem 1.5rem 1rem;
         border-bottom: 1px solid var(--back-dark);
         flex-shrink: 0;
      }
      
      .snippet-title {
         font-size: 1.3rem;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         display: flex;
         justify-content: space-between;
         align-items: flex-start;
         gap: 1rem;
      }
      
      .language-badge {
         display: inline-block;
         padding: 0.2rem 0.8rem;
         background: var(--primary);
         color: white;
         border-radius: 20px;
         font-size: 0.8rem;
         font-weight: 500;
         flex-shrink: 0;
      }
      
      .snippet-meta {
         display: flex;
         gap: 1rem;
         font-size: 0.9rem;
         color: var(--text-color);
         opacity: 0.7;
         flex-wrap: wrap;
      }
      
      .snippet-content {
         padding: 1rem 1.5rem;
         flex: 1;
         display: flex;
         flex-direction: column;
      }
      
      .snippet-description {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.5;
         font-size: 0.95rem;
         margin-bottom: 1rem;
         overflow: hidden;
         display: -webkit-box;
         -webkit-line-clamp: 3;
         -webkit-box-orient: vertical;
         min-height: 4.5em;
      }
      
      .snippet-preview {
         background: #f8f9fa;
         border-radius: 8px;
         padding: 1rem;
         font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
         font-size: 0.85rem;
         color: #333;
         flex: 1;
         position: relative;
         overflow: hidden;
      }
      
      .snippet-preview pre {
         margin: 0;
         white-space: pre-wrap;
         word-wrap: break-word;
      }
      
      .snippet-preview code {
         display: block;
         font-family: inherit;
         line-height: 1.5;
         max-height: 150px;
         overflow: hidden;
         position: relative;
      }
      
      .snippet-preview code::after {
         content: '';
         position: absolute;
         bottom: 0;
         left: 0;
         right: 0;
         height: 40px;
         background: linear-gradient(to top, #f8f9fa, transparent);
      }
      
      .snippet-actions {
         padding: 1rem 1.5rem;
         border-top: 1px solid var(--back-dark);
         display: flex;
         justify-content: space-between;
         align-items: center;
         flex-shrink: 0;
      }
      
      .view-btn {
         padding: 0.6rem 1.2rem;
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
      
      .favorite-btn {
         background: none;
         border: none;
         color: #ccc;
         cursor: pointer;
         font-size: 1.5rem;
         transition: all 0.3s;
         padding: 0.5rem;
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         width: 40px;
         height: 40px;
      }
      
      .favorite-btn:hover {
         color: #ff6b6b;
         background: rgba(255, 107, 107, 0.1);
      }
      
      .favorite-btn.active {
         color: #ff6b6b;
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
      
      .no-results {
         text-align: center;
         padding: 4rem;
         background: white;
         border-radius: 15px;
         width: 100%;
      }
      
      .no-results h3 {
         font-size: 1.5rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      
      .no-results p {
         color: var(--text-color);
         opacity: 0.7;
         margin-bottom: 2rem;
      }
      
      .results-info {
         text-align: center;
         margin-bottom: 1rem;
         color: var(--text-color);
         opacity: 0.7;
         width: 100%;
      }
      
      /* Responsive adjustments */
      @media screen and (max-width: 1200px) {
         .catalog-container {
               padding: 2% 5% 5%;
         }
         .snippet-card {
               width: calc(50% - 1rem); /* 2 cards per row on medium screens */
         }
      }
      
      @media screen and (max-width: 968px) {
         .filters-form {
               grid-template-columns: 1fr 1fr;
         }
         .snippet-card {
               width: calc(50% - 1rem);
         }
      }
      
      @media screen and (max-width: 768px) {
         .catalog-container {
               padding: 2% 1rem 5%;
         }
         .filters-form {
               grid-template-columns: 1fr;
         }
         .snippet-card {
               width: 100%; /* 1 card per row on mobile */
         }
         .catalog-header h1 {
               font-size: 2.2rem;
         }
         .snippet-title {
               font-size: 1.1rem;
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
      <section class="catalog-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="catalog-header scroll-effect">
               <h1>Code Snippets Catalog</h1>
               <p>Browse and save useful code snippets</p>
         </div>
         
         <div class="filters-container scroll-effect">
               <form class="filters-form" method="GET" action="">
                  <div class="filter-group">
                     <label for="search">Search Snippets</label>
                     <input type="text" id="search" name="search" placeholder="Search by title, description, or code..." 
                              value="<?php echo htmlspecialchars($search ?? ''); ?>">
                  </div>
                  
                  <div class="filter-group">
                     <label for="category">Category</label>
                     <select id="category" name="category">
                           <option value="">All Categories</option>
                           <?php foreach ($categories as $cat): ?>
                              <option value="<?php echo $cat['id']; ?>" 
                                 <?php echo ($category == $cat['id']) ? 'selected' : ''; ?>>
                                 <?php echo htmlspecialchars($cat['name']); ?>
                              </option>
                           <?php endforeach; ?>
                     </select>
                  </div>
                  
                  <div class="filter-group">
                     <label for="language">Language</label>
                     <select id="language" name="language">
                        <option value="">All Languages</option>
                        <?php foreach ($languages as $lang): 
                           $langName = ucfirst(strtolower($lang['language']));
                        ?>
                              <option value="<?php echo htmlspecialchars($lang['language']); ?>" 
                                 <?php echo ($language == $lang['language']) ? 'selected' : ''; ?>>
                                 <?php echo htmlspecialchars($langName); ?>
                              </option>
                        <?php endforeach; ?>
                     </select>
                  </div>
                  
                  <div class="filter-actions">
                     <button type="submit" class="filter-btn">Apply Filters</button>
                     <a href="snippets_catalog.php" class="filter-btn clear-btn">Clear</a>
                  </div>
               </form>
         </div>
         
         <?php if ($totalResults > 0): ?>
               <div class="results-info scroll-effect">
                  Found <?php echo $totalResults; ?> snippet<?php echo $totalResults != 1 ? 's' : ''; ?>
                  <?php if ($category || $language || $search): ?>
                     matching your criteria
                  <?php endif; ?>
               </div>
               
               <div class="catalog-grid scroll-effect">
                  <?php foreach ($snippets as $snippet): ?>
                     <?php 
                     // Simple text truncation for preview
                     $description_preview = strip_tags($snippet['description']);
                     if (strlen($description_preview) > 150) {
                        $description_preview = substr($description_preview, 0, 150) . '...';
                     }
                     
                     // Simple code preview (first 200 characters)
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
                              <button class="favorite-btn <?php echo $snippet['is_favorite'] ? 'active' : ''; ?>" 
                                       data-snippet-id="<?php echo $snippet['id']; ?>"
                                       data-is-favorite="<?php echo $snippet['is_favorite'] ? '1' : '0'; ?>"
                                       onclick="toggleFavorite(this)">
                                 <i class="<?php echo $snippet['is_favorite'] ? 'fas' : 'far'; ?> fa-heart"></i>
                              </button>
                           </div>
                     </div>
                  <?php endforeach; ?>
               </div>
               
               <?php if ($totalPages > 1): ?>
                  <div class="pagination scroll-effect">
                     <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                           <a href="?<?php 
                              echo http_build_query(array_merge($_GET, ['page' => $i]));
                           ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                              <?php echo $i; ?>
                           </a>
                     <?php endfor; ?>
                  </div>
               <?php endif; ?>
               
         <?php else: ?>
               <div class="no-results scroll-effect">
                  <h3>No snippets found</h3>
                  <p>Try adjusting your search filters or check back later for new additions.</p>
                  <a href="snippets_catalog.php" class="primary_btn">View All Snippets</a>
               </div>
         <?php endif; ?>
      </section>
   </main>

   <?php include './assets/footer.php' ?>

   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
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
                  button.classList.toggle('active');
                  const icon = button.querySelector('i');
                  if (data.is_favorite) {
                     icon.classList.remove('far');
                     icon.classList.add('fas');
                     button.setAttribute('data-is-favorite', '1');
                  } else {
                     icon.classList.remove('fas');
                     icon.classList.add('far');
                     button.setAttribute('data-is-favorite', '0');
                  }
                  // Optional: Show notification
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

      // Helper function for notifications
      function showNotification(message) {
         const notification = document.createElement('div');
         notification.style.cssText = `
               position: fixed;
               top: 20px;
               right: 20px;
               background: var(--primary);
               color: white;
               padding: 1rem 1.5rem;
               border-radius: 8px;
               box-shadow: 0 5px 15px rgba(0,0,0,0.2);
               z-index: 10000;
               animation: slideIn 0.3s ease;
         `;
         notification.textContent = message;
         document.body.appendChild(notification);
         
         setTimeout(() => {
               notification.style.animation = 'slideOut 0.3s ease';
               setTimeout(() => notification.remove(), 300);
         }, 3000);
      }

      // Add CSS for animation
      const style = document.createElement('style');
      style.textContent = `
         @keyframes slideIn {
               from { transform: translateX(100%); opacity: 0; }
               to { transform: translateX(0); opacity: 1; }
         }
         @keyframes slideOut {
               from { transform: translateX(0); opacity: 1; }
               to { transform: translateX(100%); opacity: 0; }
         }
      `;
      document.head.appendChild(style);
   </script>
   <script src="./js/notifications.js"></script>
</body>
</html>