<?php
require_once './assets/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

try {
   $favoritesQuery = "SELECT COUNT(*) as total FROM user_favorites WHERE user_id = ?";
   $stmt = $pdo->prepare($favoritesQuery);
   $stmt->execute([$_SESSION['user_id']]);
   $totalFavorites = $stmt->fetch()['total'];
   
   $snippetsQuery = "SELECT COUNT(*) as total FROM snippets WHERE is_public = 1";
   $stmt = $pdo->prepare($snippetsQuery);
   $stmt->execute();
   $totalSnippets = $stmt->fetch()['total'];
   
   $categoriesQuery = "SELECT COUNT(*) as total FROM categories";
   $stmt = $pdo->prepare($categoriesQuery);
   $stmt->execute();
   $totalCategories = $stmt->fetch()['total'];
   
   $languagesQuery = "SELECT COUNT(DISTINCT language) as total FROM snippets WHERE language IS NOT NULL AND language != ''";
   $stmt = $pdo->prepare($languagesQuery);
   $stmt->execute();
   $totalLanguages = $stmt->fetch()['total'];
   
   $usersQuery = "SELECT COUNT(*) as total FROM users";
   $stmt = $pdo->prepare($usersQuery);
   $stmt->execute();
   $totalUsers = $stmt->fetch()['total'];
   
   $totalViewsQuery = "SELECT SUM(views) as total FROM snippets";
   $stmt = $pdo->prepare($totalViewsQuery);
   $stmt->execute();
   $totalViews = $stmt->fetch()['total'];
   
} catch (PDOException $e) {
   error_log("Failed to fetch stats: " . $e->getMessage());
   $totalFavorites = 0;
   $totalSnippets = 0;
   $totalCategories = 0;
   $totalLanguages = 0;
   $totalUsers = 0;
   $totalViews = 0;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Dashboard - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      #header{
         height: 10vh;
      }
      .dashboard-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
      }
      .dashboard-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .dashboard-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .user-greeting {
         font-size: 1.5rem;
         color: var(--primary);
         margin-bottom: 0.5rem;
         font-weight: 600;
         font-family: var(--subheading);
      }
      .dashboard-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .dashboard-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .dashboard-card {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         transition: transform 0.3s ease;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         text-align: center;
      }
      .dashboard-card:hover {
         transform: translateY(-5px);
      }
      .dashboard-card h3 {
         color: var(--primary);
         margin-bottom: 1rem;
         font-size: 1.5rem;
      }
      .dashboard-card p {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.6;
      }
      .stats-container {
         background: white;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         padding: 3rem;
         margin-bottom: 3rem;
      }
      .stats-header {
         text-align: center;
         margin-bottom: 2.5rem;
      }
      .stats-header h2 {
         font-size: 2rem;
         color: var(--text-color);
         margin-bottom: 0.5rem;
      }
      .stats-header p {
         color: var(--text-color);
         opacity: 0.8;
      }
      .stats-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 1.5rem;
      }
      .stat-card {
         background: var(--back-light);
         padding: 1.8rem;
         border-radius: 12px;
         text-align: center;
         transition: all 0.3s ease;
         border: 2px solid transparent;
      }
      .stat-card:hover {
         border-color: var(--primary);
         transform: translateY(-3px);
         box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
      }
      .stat-icon {
         font-size: 2.5rem;
         margin-bottom: 1rem;
         color: var(--primary);
      }
      .stat-number {
         font-size: 2.8rem;
         font-weight: 700;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         line-height: 1;
      }
      .stat-label {
         font-size: 1.1rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .stat-card:nth-child(1) .stat-icon { color: #ff6b6b; }
      .stat-card:nth-child(2) .stat-icon { color: #4ecdc4; }
      .stat-card:nth-child(3) .stat-icon { color: #45b7d1; }
      .stat-card:nth-child(4) .stat-icon { color: #96ceb4; }
      .stat-card:nth-child(5) .stat-icon { color: #feca57; }
      .stat-card:nth-child(6) .stat-icon { color: #ff9ff3; }
      
      .features-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
         gap: 1.5rem;
      }
      .btns{
         display: flex;
         justify-content: center;
         align-items: center;
         gap: 20px;
      }
      @media screen and (max-width: 742px){
         .dashboard-container{
            padding: 5%;
         }
         .dashboard-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
         }
         .stats-grid {
            grid-template-columns: 1fr;
         }
         .stats-container {
            padding: 1.5rem;
         }
         .stat-number {
            font-size: 2.2rem;
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
      <section class="dashboard-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="dashboard-header scroll-effect">
            <div class="user-greeting">
               Welcome back, <?php echo htmlspecialchars($_SESSION['first_name']); ?>! 👋
            </div>
            <h1>Your Dashboard</h1>
            <p>Manage your code snippets, favorites, and profile</p>
         </div>
         
         <div class="dashboard-grid scroll-effect">
            <div class="dashboard-card">
               <h3>Snippets</h3>
               <p>View and manage all your saved code snippets in one place.</p>
               <a href="snippets_catalog.php" class="primary_btn" style="margin-top: 1rem; padding: .5rem 1.5rem; font-size: 1rem; ">My Snippets</a>
            </div>
            <div class="dashboard-card">
               <h3>Favorites</h3>
               <p>Quick access to your most-used code snippets and components.</p>
               <a href="favorites.php" class="primary_btn" style="margin-top: 1rem; padding: .5rem 1.5rem; font-size: 1rem; ">My Favorites</a>
            </div>
            <div class="dashboard-card">
               <h3>Web Tools</h3>
               <p>Collection of useful web development tools and utilities.</p>
               <a href="web_tools.php" class="primary_btn" style="margin-top: 1rem; padding: .5rem 1.5rem; font-size: 1rem; ">Web Tools</a>
            </div>
            <div class="dashboard-card">
               <h3>Profile Settings</h3>
               <p>Update your personal information and preferences.</p>
               <a href="account.php" class="primary_btn" style="margin-top: 1rem; padding: .5rem 1.5rem; font-size: 1rem; ">Manage Account</a>
            </div>
         </div>
         
         <div class="stats-container scroll-effect">
            <div class="stats-header">
               <h2>Code Library Statistics</h2>
               <p>Overall statistics of the code library platform</p>
            </div>
            
            <div class="stats-grid">
               <div class="stat-card">
                  <div class="stat-icon">📚</div>
                  <div class="stat-number"><?php echo $totalSnippets; ?></div>
                  <div class="stat-label">Total Snippets</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-icon">❤️</div>
                  <div class="stat-number"><?php echo $totalFavorites; ?></div>
                  <div class="stat-label">Your Favorites</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-icon">📁</div>
                  <div class="stat-number"><?php echo $totalCategories; ?></div>
                  <div class="stat-label">Categories</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-icon">💻</div>
                  <div class="stat-number"><?php echo $totalLanguages; ?></div>
                  <div class="stat-label">Languages</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-icon">👥</div>
                  <div class="stat-number"><?php echo $totalUsers; ?></div>
                  <div class="stat-label">Total Users</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-icon">👁️</div>
                  <div class="stat-number"><?php echo $totalViews ?: '0'; ?></div>
                  <div class="stat-label">Total Views</div>
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