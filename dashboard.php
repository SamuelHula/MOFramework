<?php
// Include config first - it handles session starting
require_once './assets/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
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
      .placeholder-content {
         text-align: center;
         padding: 4rem 2rem;
         background: white;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      .placeholder-content h2 {
         color: var(--primary);
         margin-bottom: 1rem;
         font-size: 2rem;
      }
      .placeholder-content p {
         color: var(--text-color);
         opacity: 0.8;
         font-size: 1.1rem;
         margin-bottom: 2rem;
      }
      .coming-soon {
         font-size: 4rem;
         color: var(--secondary);
         margin-bottom: 1rem;
      }
      .btns{
         display: flex;
         justify-content: center;
         align-items: center;
         gap: 20px;
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
               <h3>My Snippets</h3>
               <p>View and manage all your saved code snippets in one place.</p>
            </div>
            <div class="dashboard-card">
               <h3>Favorites</h3>
               <p>Quick access to your most-used code snippets and components.</p>
            </div>
            <div class="dashboard-card">
               <h3>Recent Activity</h3>
               <p>Track your recent views and interactions with the library.</p>
            </div>
            <div class="dashboard-card">
               <h3>Profile Settings</h3>
               <p>Update your personal information and preferences.</p>
               <a href="account.php" class="primary_btn" style="margin-top: 1rem; padding: .5rem 1.5rem; font-size: 1rem; "> Manage Account</a>
            </div>
         </div>
         
         <div class="placeholder-content scroll-effect">
            <div class="coming-soon">🚀</div>
            <h2>Dashboard Under Development</h2>
            <p>We're working hard to bring you an amazing dashboard experience. This area will soon be filled with powerful features to help you manage your code library.</p>
            <div class="btns">
               <a href="index.php" class="primary_btn">
                  <span>Back to Home</span>
               </a>
               <a href="#contact_form" class="secondary_btn">
                  <span>Give Feedback</span>
               </a>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
</body>
</html>