<?php
// admin_manage_snippets.php

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './assets/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

$active_page = 'admin_manage_snippets';

// Handle delete action
if (isset($_GET['delete'])) {
   $snippet_id = intval($_GET['delete']);
   try {
      $stmt = $pdo->prepare("DELETE FROM snippets WHERE id = ?");
      $stmt->execute([$snippet_id]);
      
      logAdminActivity($_SESSION['admin_id'], 'delete_snippet', "Deleted snippet ID: $snippet_id");
      
      header("Location: admin_manage_snippets.php?success=Snippet+deleted+successfully");
      exit;
   } catch (PDOException $e) {
      error_log("Failed to delete snippet: " . $e->getMessage());
      header("Location: admin_manage_snippets.php?error=Failed+to+delete+snippet");
      exit;
   }
}

// Fetch snippets with pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

try {
   // Build query
   $query = "SELECT s.*, c.name as category_name, 
                  (SELECT COUNT(*) FROM user_favorites WHERE snippet_id = s.id) as favorite_count 
            FROM snippets s 
            LEFT JOIN categories c ON s.category_id = c.id 
            ORDER BY s.created_at DESC 
            LIMIT :limit OFFSET :offset";
   
   $stmt = $pdo->prepare($query);
   $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
   $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
   $stmt->execute();
   $snippets = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
   // Count total snippets
   $stmt = $pdo->query("SELECT COUNT(*) as total FROM snippets");
   $totalSnippets = $stmt->fetch()['total'];
   $totalPages = ceil($totalSnippets / $limit);
   
} catch (PDOException $e) {
   error_log("Failed to fetch snippets: " . $e->getMessage());
   $snippets = [];
   $totalSnippets = 0;
   $totalPages = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manage Snippets - Admin Panel</title>
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
         padding-top: 70px;
      }
      .manage-snippets-container {
         padding: 2rem;
         max-width: 1400px;
         margin: 0 auto;
      }
      .manage-header {
         text-align: center;
         margin-bottom: 3rem;
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      }
      .manage-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .manage-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
      }
      .snippets-table-container {
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         margin-bottom: 3rem;
      }
      .table-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1.5rem;
      }
      .table-header h2 {
         color: var(--text-color);
         font-size: 1.8rem;
         font-family: var(--heading);
      }
      .snippets-table {
         width: 100%;
         border-collapse: collapse;
      }
      .snippets-table th {
         background: var(--back-light);
         padding: 1rem;
         text-align: left;
         font-weight: 600;
         color: var(--text-color);
         border-bottom: 2px solid var(--back-dark);
         font-family: var(--subheading);
      }
      .snippets-table td {
         padding: 1rem;
         border-bottom: 1px solid var(--back-dark);
         color: var(--text-color);
         opacity: 0.8;
      }
      .snippets-table tr:hover {
         background: var(--back-light);
      }
      .status-badge {
         display: inline-block;
         padding: 0.3rem 0.8rem;
         border-radius: 20px;
         font-size: 0.85rem;
         font-weight: 600;
      }
      .status-public {
         background: #4caf50;
         color: white;
      }
      .status-private {
         background: #ff9800;
         color: white;
      }
      .status-featured {
         background: var(--primary);
         color: white;
      }
      .language-badge {
         display: inline-block;
         padding: 0.2rem 0.6rem;
         background: var(--secondary);
         color: white;
         border-radius: 12px;
         font-size: 0.8rem;
      }
      .actions {
         display: flex;
         gap: 0.5rem;
      }
      .action-btn {
         padding: 0.5rem 0.8rem;
         border-radius: 6px;
         text-decoration: none;
         font-size: 0.9rem;
         transition: all 0.3s;
         border: none;
         cursor: pointer;
         font-family: var(--subheading);
      }
      .btn-view {
         background: var(--primary);
         color: white;
      }
      .btn-view:hover {
         background: var(--secondary);
      }
      .btn-edit {
         background: #ff9800;
         color: white;
      }
      .btn-edit:hover {
         background: #f57c00;
      }
      .btn-delete {
         background: #f44336;
         color: white;
      }
      .btn-delete:hover {
         background: #d32f2f;
      }
      .pagination {
         display: flex;
         justify-content: center;
         gap: 0.5rem;
         margin-top: 2rem;
      }
      .page-link {
         padding: 0.5rem 1rem;
         background: var(--back-light);
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
      
      /* Floating Balls Background */
      .floating-balls {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         pointer-events: none;
         z-index: 0;
         overflow: hidden;
      }
      .ball {
         position: absolute;
         border-radius: 50%;
         opacity: 0.15;
         animation: float 15s infinite ease-in-out;
         filter: blur(1px);
      }
      .auth-ball-1 {
         width: 200px;
         height: 200px;
         top: 10%;
         left: 5%;
         background: radial-gradient(circle at 30% 30%, var(--primary), transparent);
         animation-delay: 0s;
      }
      .auth-ball-2 {
         width: 120px;
         height: 120px;
         top: 70%;
         left: 80%;
         background: radial-gradient(circle at 30% 30%, var(--secondary), transparent);
         animation-delay: -3s;
      }
      .auth-ball-3 {
         width: 180px;
         height: 180px;
         top: 40%;
         left: 85%;
         background: radial-gradient(circle at 30% 30%, var(--primary), transparent);
         animation-delay: -6s;
      }
      .auth-ball-4 {
         width: 150px;
         height: 150px;
         top: 80%;
         left: 10%;
         background: radial-gradient(circle at 30% 30%, var(--secondary), transparent);
         animation-delay: -9s;
      }
      .auth-ball-5 {
         width: 100px;
         height: 100px;
         top: 20%;
         left: 90%;
         background: radial-gradient(circle at 30% 30%, var(--primary), transparent);
         animation-delay: -12s;
      }
      .auth-ball-6 {
         width: 160px;
         height: 160px;
         top: 60%;
         left: 15%;
         background: radial-gradient(circle at 30% 30%, var(--secondary), transparent);
         animation-delay: -15s;
      }
      
      @keyframes float {
         0%, 100% {
               transform: translate(0, 0) rotate(0deg);
         }
         25% {
               transform: translate(20px, -15px) rotate(5deg);
         }
         50% {
               transform: translate(-15px, 10px) rotate(-5deg);
         }
         75% {
               transform: translate(10px, 20px) rotate(3deg);
         }
      }

      @media screen and (max-width: 768px) {
         body {
               padding-top: 120px;
         }
         .manage-snippets-container {
               padding: 1rem;
         }
         .snippets-table-container {
               padding: 1.5rem;
               overflow-x: auto;
         }
         .snippets-table {
               min-width: 800px;
         }
         .actions {
               flex-direction: column;
         }
      }
      @media screen and (max-width: 768px) {
         .snippets-table {
            display: block;
            overflow-x: auto;
         }
         
         .snippets-table th,
         .snippets-table td {
            padding: 0.8rem;
            font-size: 0.9rem;
         }
         
         .table-header {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
         }
      }
   </style>
</head>
<body>
   <!-- Floating Balls Background -->
   <div class="floating-balls">
      <div class="ball auth-ball-1"></div>
      <div class="ball auth-ball-2"></div>
      <div class="ball auth-ball-3"></div>
      <div class="ball auth-ball-4"></div>
      <div class="ball auth-ball-5"></div>
      <div class="ball auth-ball-6"></div>
   </div>
   
   <?php 
   // Include admin navbar
   $admin_navbar_path = './includes/admin_navbar.php';
   if (file_exists($admin_navbar_path)) {
      include_once $admin_navbar_path;
   } else {
      // Fallback navbar if admin_navbar doesn't exist
      echo '<nav class="admin-nav-bar">
               <a href="admin_dashboard.php" class="admin-nav-brand">Admin Panel</a>
               <div class="admin-nav-menu">
                  <a href="admin_dashboard.php" class="admin-nav-link">Dashboard</a>
                  <a href="admin_manage_snippets.php" class="admin-nav-link active">Snippets</a>
                  <a href="manage_users.php" class="admin-nav-link">Users</a>
                  <a href="manage_admin.php" class="admin-nav-link">Admins</a>
                  <a href="./assets/logout.php" class="admin-signout-btn">Logout</a>
               </div>
            </nav>';
   }
   ?>
   
   <main id="main">
      <section class="manage-snippets-container">
         <div class="manage-header scroll-effect">
               <h1>Manage Code Snippets</h1>
               <p>View, edit, and delete code snippets</p>
         </div>
         
         <div class="snippets-table-container scroll-effect">
               <div class="table-header">
                  <h2>All Snippets (<?php echo $totalSnippets; ?>)</h2>
                  <a href="admin_add_snippet.php" class="action-btn btn-view">+ Add New Snippet</a>
               </div>
               
               <?php if (!empty($snippets)) { ?>
                  <table class="snippets-table">
                     <thead>
                           <tr>
                              <th>Title</th>
                              <th>Language</th>
                              <th>Category</th>
                              <th>Status</th>
                              <th>Favorites</th>
                              <th>Views</th>
                              <th>Date</th>
                              <th>Actions</th>
                           </tr>
                     </thead>
                     <tbody>
                           <?php foreach ($snippets as $snippet) { ?>
                           <tr>
                              <td>
                                 <strong><?php echo htmlspecialchars($snippet['title']); ?></strong>
                              </td>
                              <td>
                                 <span class="language-badge"><?php echo htmlspecialchars($snippet['language']); ?></span>
                              </td>
                              <td>
                                 <?php echo $snippet['category_name'] ? htmlspecialchars($snippet['category_name']) : '—'; ?>
                              </td>
                              <td>
                                 <?php if ($snippet['is_featured']) { ?>
                                       <span class="status-badge status-featured">Featured</span>
                                 <?php } ?>
                                 <?php if ($snippet['is_public']) { ?>
                                       <span class="status-badge status-public">Public</span>
                                 <?php } else { ?>
                                       <span class="status-badge status-private">Private</span>
                                 <?php } ?>
                              </td>
                              <td><?php echo $snippet['favorite_count']; ?></td>
                              <td><?php echo $snippet['views']; ?></td>
                              <td><?php echo date('M j, Y', strtotime($snippet['created_at'])); ?></td>
                              <td>
                                 <div class="actions">
                                       <a href="snippet_view.php?id=<?php echo $snippet['id']; ?>" target="_blank" class="action-btn btn-view">View</a>
                                       <a href="admin_edit_snippet.php?id=<?php echo $snippet['id']; ?>" class="action-btn btn-edit">Edit</a>
                                       <button onclick="confirmDelete(<?php echo $snippet['id']; ?>)" class="action-btn btn-delete">Delete</button>
                                 </div>
                              </td>
                           </tr>
                           <?php } ?>
                     </tbody>
                  </table>
                  
                  <?php if ($totalPages > 1) { ?>
                     <div class="pagination">
                           <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                              <a href="?page=<?php echo $i; ?>" class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                                 <?php echo $i; ?>
                              </a>
                           <?php } ?>
                     </div>
                  <?php } ?>
                  
               <?php } else { ?>
                  <p style="text-align: center; padding: 2rem; color: var(--text-color); opacity: 0.7;">
                     No snippets found. <a href="admin_add_snippet.php">Add your first snippet</a>.
                  </p>
               <?php } ?>
         </div>
      </section>
   </main>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      function confirmDelete(snippetId) {
         if (confirm('Are you sure you want to delete this snippet? This action cannot be undone.')) {
               window.location.href = '?delete=' + snippetId;
         }
      }
      
      // Add scroll effect
      document.addEventListener('DOMContentLoaded', function() {
         const scrollElements = document.querySelectorAll('.scroll-effect');
         
         const elementInView = (el, percentageScroll = 100) => {
               const elementTop = el.getBoundingClientRect().top;
               return (
                  elementTop <= 
                  ((window.innerHeight || document.documentElement.clientHeight) * (percentageScroll/100))
               );
         };
         
         const displayScrollElement = (element) => {
               element.classList.add('visible');
         };
         
         const handleScrollAnimation = () => {
               scrollElements.forEach((el) => {
                  if (elementInView(el, 100)) {
                     displayScrollElement(el);
                  }
               });
         };
         
         window.addEventListener('scroll', () => {
               handleScrollAnimation();
         });
         
         // Initial check
         handleScrollAnimation();
      });
   </script>
</body>
</html>