<?php
// admin_dashboard.php
require_once './assets/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

// Set active page for navbar
$active_page = 'dashboard';

// Get admin statistics
try {
   // Get total admins
   $stmt = $pdo->query("SELECT COUNT(*) as total_admins FROM admins WHERE is_active = 1");
   $totalAdmins = $stmt->fetch()['total_admins'];
   
   // Get total users
   $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
   $totalUsers = $stmt->fetch()['total_users'];
   
   // Get recent activities
   $stmt = $pdo->prepare("
      SELECT a.*, ad.first_name, ad.last_name 
      FROM admin_activities a 
      LEFT JOIN admins ad ON a.admin_id = ad.id 
      ORDER BY a.created_at DESC 
      LIMIT 10
   ");
   $stmt->execute();
   $recentActivities = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
} catch (PDOException $e) {
   error_log("Failed to fetch admin statistics: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Dashboard - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
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
         position: relative;
         z-index: 0;
         overflow-x: hidden;
         padding-top: 70px;
      }
      .admin-dashboard-container {
         padding: 2rem;
         max-width: 1400px;
         margin: 0 auto;
      }
      .admin-header {
         text-align: center;
         margin-bottom: 3rem;
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         position: relative;
         z-index: 2;
      }
      .admin-greeting {
         font-size: 1.8rem;
         color: var(--primary);
         margin-bottom: 0.5rem;
         font-weight: 600;
         font-family: var(--subheading);
      }
      .admin-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .admin-role {
         display: inline-block;
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         color: white;
         padding: 0.4rem 1.5rem;
         border-radius: 20px;
         font-size: 1rem;
         font-weight: 600;
         margin: 1rem 0;
         box-shadow: 0 4px 15px rgba(var(--primary-rgb), 0.2);
      }
      .admin-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
         margin-top: 0.5rem;
      }
      .admin-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .admin-card {
         background: white;
         padding: 2rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         transition: all 0.3s ease;
         border: 2px solid transparent;
         position: relative;
         overflow: hidden;
      }
      .admin-card:hover {
         transform: translateY(-8px);
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.12);
         border-color: var(--primary);
      }
      .admin-card h3 {
         color: var(--text-color);
         margin-bottom: 1.2rem;
         font-size: 1.6rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
         font-family: var(--subheading);
      }
      .admin-card p {
         color: var(--text-color);
         opacity: 0.7;
         line-height: 1.6;
         margin-bottom: 1.5rem;
      }
      .stat-number {
         font-size: 3rem;
         font-weight: 700;
         color: var(--primary);
         display: block;
         margin-bottom: 0.5rem;
         font-family: var(--heading);
      }
      .stat-label {
         color: var(--text-color);
         opacity: 0.6;
         font-size: 1rem;
         text-transform: uppercase;
         letter-spacing: 1px;
         margin-bottom: 1rem;
      }
      .admin-actions {
         display: flex;
         flex-direction: column;
         gap: 0.8rem;
         margin-top: 1.5rem;
      }
      .admin-btn {
         padding: 0.9rem 1.5rem;
         border-radius: 10px;
         text-decoration: none;
         font-weight: 600;
         transition: all 0.3s;
         text-align: center;
         border: 2px solid transparent;
         cursor: pointer;
         font-family: var(--subheading);
         font-size: 1rem;
      }
      .admin-btn-primary {
         background: var(--primary);
         color: var(--back-light);
         border-color: var(--primary);
      }
      .admin-btn-primary:hover {
         background: transparent;
         color: var(--primary);
      }
      .admin-btn-success {
         background: #4caf50;
         color: white;
         border-color: #4caf50;
      }
      .admin-btn-success:hover {
         background: transparent;
         color: #4caf50;
      }
      .admin-btn-warning {
         background: #ff9800;
         color: white;
         border-color: #ff9800;
      }
      .admin-btn-warning:hover {
         background: transparent;
         color: #ff9800;
      }
      .admin-btn-danger {
         background: #f44336;
         color: white;
         border-color: #f44336;
      }
      .admin-btn-danger:hover {
         background: transparent;
         color: #f44336;
      }
      .recent-activities {
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         margin-bottom: 3rem;
      }
      .recent-activities h3 {
         color: var(--text-color);
         margin-bottom: 1.8rem;
         font-size: 1.8rem;
         font-family: var(--heading);
      }
      .activity-list {
         list-style: none;
      }
      .activity-item {
         padding: 1.2rem;
         border-bottom: 1px solid var(--back-dark);
         display: flex;
         align-items: flex-start;
         gap: 1rem;
         transition: background-color 0.3s;
      }
      .activity-item:hover {
         background: var(--back-light);
         border-radius: 10px;
      }
      .activity-item:last-child {
         border-bottom: none;
      }
      .activity-icon {
         width: 45px;
         height: 45px;
         border-radius: 50%;
         background: var(--back-light);
         display: flex;
         align-items: center;
         justify-content: center;
         color: var(--primary);
         flex-shrink: 0;
         font-size: 1.2rem;
      }
      .activity-content {
         flex: 1;
      }
      .activity-title {
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.3rem;
         font-size: 1.1rem;
      }
      .activity-description {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 0.95rem;
         margin-bottom: 0.5rem;
         line-height: 1.5;
      }
      .activity-meta {
         display: flex;
         justify-content: space-between;
         font-size: 0.85rem;
         color: var(--text-color);
         opacity: 0.5;
      }
      
      /* Floating Balls Styles */
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
         z-index: inherit;
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
      
      .security-notes {
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         color: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(var(--primary-rgb), 0.2);
      }
      .security-notes h3 {
         color: white;
         margin-bottom: 1.2rem;
         font-size: 1.6rem;
         font-family: var(--heading);
      }
      .security-notes p {
         color: white;
         opacity: 0.9;
         margin-bottom: 1.5rem;
      }
      .security-notes ul {
         margin-top: 1rem;
         padding-left: 1.5rem;
         margin-bottom: 2rem;
      }
      .security-notes li {
         color: white;
         opacity: 0.9;
         margin-bottom: 0.5rem;
         line-height: 1.6;
      }
      .security-notes .admin-btn {
         background: white;
         color: var(--primary);
         border-color: white;
      }
      .security-notes .admin-btn:hover {
         background: transparent;
         color: white;
         border-color: white;
      }
      
      .stats-card-decor {
         position: absolute;
         top: 0;
         right: 0;
         width: 80px;
         height: 80px;
         background: linear-gradient(135deg, var(--primary), transparent);
         border-radius: 0 20px 0 50px;
         opacity: 0.1;
      }
      
      @media screen and (max-width: 768px) {
         body {
            padding-top: 120px;
         }
         .admin-nav-bar {
            width: 95%;
            padding: 0.8rem;
            flex-direction: column;
            gap: 1rem;
            border-radius: 0 0 10px 10px;
         }
         .admin-nav-menu {
            gap: 0.5rem;
            flex-wrap: wrap;
            justify-content: center;
         }
         .admin-dashboard-container {
            padding: 1rem;
         }
         .admin-header {
            padding: 1.5rem;
         }
         .admin-header h1 {
            font-size: 2rem;
         }
         .admin-greeting {
            font-size: 1.4rem;
         }
         .recent-activities {
            padding: 1.5rem;
         }
         /* Hide some balls on mobile for better performance */
         .auth-ball-3, .auth-ball-5 {
            display: none;
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
   
   <?php include_once './includes/admin_navbar.php'; ?>
   
   <main id="main">
      <section class="admin-dashboard-container">
         <div class="admin-header scroll-effect">
               <div class="admin-greeting">
                  Welcome back<?php echo htmlspecialchars($_SESSION['admin_name']); ?>!
               </div>
               <h1>Admin Dashboard</h1>
               <div class="admin-role">
                  <?php echo htmlspecialchars($_SESSION['admin_role']); ?>
               </div>
               <p>Last login: <?php 
                  try {
                     $stmt = $pdo->prepare("SELECT last_login FROM admins WHERE id = ?");
                     $stmt->execute([$_SESSION['admin_id']]);
                     $lastLogin = $stmt->fetch()['last_login'];
                     echo $lastLogin ? date('F j, Y H:i', strtotime($lastLogin)) : 'First login';
                  } catch (PDOException $e) {
                     echo 'N/A';
                  }
               ?></p>
         </div>
         
         <div class="admin-grid scroll-effect">
            <div class="admin-card">
               <div class="stats-card-decor"></div>
               <h3>👤 User Management</h3>
               <div class="stat-number">
                  <?php 
                  try {
                        $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
                        $totalUsers = $stmt->fetch()['total'];
                        echo $totalUsers;
                  } catch (PDOException $e) {
                        echo '0';
                  }
                  ?>
               </div>
               <div class="stat-label">Registered Users</div>
               <p>Manage user accounts, view user information, and perform administrative actions.</p>
               <div class="admin-actions">
                  <a href="manage_users.php" class="admin-btn admin-btn-primary">Browse Users</a>
                  <a href="add_user.php" class="admin-btn admin-btn-success">Add New User</a>
               </div>
            </div>
            
            <div class="admin-card">
               <div class="stats-card-decor"></div>
               <h3>👥 Admin Team</h3>
               <div class="stat-number"><?php echo $totalAdmins; ?></div>
               <div class="stat-label">Active Administrators</div>
               <p>Manage administrator accounts, roles, permissions, and monitor admin activities.</p>
               <div class="admin-actions">
                  <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                        <a href="manage_admin.php?action=add" class="admin-btn admin-btn-success">Add Admin</a>
                  <?php endif; ?>
                  <a href="manage_admin.php" class="admin-btn admin-btn-primary">Manage Team</a>
               </div>
            </div>               
            <div class="admin-card">
               <div class="stats-card-decor"></div>
               <h3>💻 Code Snippets</h3>
               <?php 
               try {
                  $stmt = $pdo->query("SELECT COUNT(*) as total FROM snippets");
                  $totalSnippets = $stmt->fetch()['total'];
                  echo '<div class="stat-number">' . $totalSnippets . '</div>';
               } catch (PDOException $e) {
                  echo '<div class="stat-number">0</div>';
               }
               ?>
               <div class="stat-label">Total Snippets</div>
               <p>Manage code snippets, add new ones, and organize by categories.</p>
               <div class="admin-actions">
                  <a href="admin_add_snippet.php" class="admin-btn admin-btn-success">Add New Snippet</a>
                  <a href="admin_manage_snippets.php" class="admin-btn admin-btn-primary">Manage Snippets</a>
               </div>
            </div>
         </div>
         
         <div class="recent-activities scroll-effect">
               <h3>Recent Activities</h3>
               <ul class="activity-list">
                  <?php foreach ($recentActivities as $activity): ?>
                  <li class="activity-item">
                     <div class="activity-icon">
                           <?php 
                           switch($activity['activity_type']) {
                              case 'login': echo '🔐'; break;
                              case 'create_admin': echo '👥'; break;
                              case 'update_admin': echo '✏️'; break;
                              case 'delete_admin': echo '🗑️'; break;
                              default: echo '📝';
                           }
                           ?>
                     </div>
                     <div class="activity-content">
                           <div class="activity-title">
                              <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $activity['activity_type']))); ?>
                           </div>
                           <div class="activity-description">
                              <?php echo htmlspecialchars($activity['description']); ?>
                           </div>
                           <div class="activity-meta">
                              <span>
                                 By: <?php echo htmlspecialchars($activity['first_name'] . ' ' . $activity['last_name']); ?>
                              </span>
                              <span>
                                 <?php echo date('M j, Y H:i', strtotime($activity['created_at'])); ?>
                              </span>
                           </div>
                     </div>
                  </li>
                  <?php endforeach; ?>
               </ul>
         </div>
         
         <div class="security-notes scroll-effect">
               <h3>⚠️ Security Notes</h3>
               <p>As an administrator, you have elevated privileges. Please ensure:</p>
               <ul>
                  <li>Keep your credentials secure and change passwords regularly</li>
                  <li>Always log out after each session, especially on shared devices</li>
                  <li>Review activity logs regularly for any suspicious activities</li>
                  <li>Follow security protocols and report any anomalies immediately</li>
               </ul>
               <div class="admin-actions" style="margin-top: 2rem;">
                  <a href="#" class="admin-btn">My Profile</a>
                  <a href="#" class="admin-btn">Change Password</a>
               </div>
         </div>
      </section>
   </main>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
</body>
</html>