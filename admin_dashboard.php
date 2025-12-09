<?php
// admin_dashboard.php
require_once './assets/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

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
   
   // Get admin list for management (only for super_admin)
   $adminList = [];
   if ($_SESSION['admin_role'] === 'super_admin') {
      $stmt = $pdo->query("
         SELECT a.*, creator.first_name as creator_first, creator.last_name as creator_last 
         FROM admins a 
         LEFT JOIN admins creator ON a.created_by = creator.id 
         WHERE a.is_active = 1 
         ORDER BY a.created_at DESC
      ");
      $adminList = $stmt->fetchAll(PDO::FETCH_ASSOC);
   }
   
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
      #header {
         height: 10vh;
         background: #1a237e;
      }
      .admin-dashboard-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, #e8eaf6 0%, #c5cae9 50%, #9fa8da 100%);
         position: relative;
         padding-top: 100px;
      }
      .admin-header {
         text-align: center;
         margin-bottom: 3rem;
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      .admin-header h1 {
         font-size: 2.5rem;
         margin-bottom: 1rem;
         color: #1a237e;
      }
      .admin-greeting {
         font-size: 1.5rem;
         color: #ff9800;
         margin-bottom: 0.5rem;
         font-weight: 600;
         font-family: var(--subheading);
      }
      .admin-role {
         display: inline-block;
         background: #ff9800;
         color: white;
         padding: 0.3rem 1rem;
         border-radius: 20px;
         font-size: 0.9rem;
         font-weight: 600;
         margin-top: 0.5rem;
      }
      .admin-grid {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .admin-card {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         transition: transform 0.3s ease;
         border-top: 4px solid;
      }
      .admin-card:hover {
         transform: translateY(-5px);
         box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
      }
      .admin-card.stats {
         border-color: #2196f3;
      }
      .admin-card.users {
         border-color: #4caf50;
      }
      .admin-card.admins {
         border-color: #ff9800;
      }
      .admin-card.system {
         border-color: #9c27b0;
      }
      .admin-card h3 {
         color: #1a237e;
         margin-bottom: 1rem;
         font-size: 1.5rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .admin-card p {
         color: #5c6bc0;
         opacity: 0.8;
         line-height: 1.6;
         margin-bottom: 1.5rem;
      }
      .stat-number {
         font-size: 2.5rem;
         font-weight: 700;
         color: #1a237e;
         display: block;
         margin-bottom: 0.5rem;
      }
      .stat-label {
         color: #5c6bc0;
         font-size: 0.9rem;
         text-transform: uppercase;
         letter-spacing: 1px;
      }
      .admin-actions {
         display: flex;
         flex-direction: column;
         gap: 1rem;
         margin-top: 1rem;
      }
      .admin-btn {
         padding: 0.8rem 1.5rem;
         border-radius: 8px;
         text-decoration: none;
         font-weight: 600;
         transition: all 0.3s;
         text-align: center;
         border: 2px solid transparent;
         cursor: pointer;
      }
      .admin-btn-primary {
         background: #2196f3;
         color: white;
      }
      .admin-btn-primary:hover {
         background: transparent;
         color: #2196f3;
         border-color: #2196f3;
      }
      .admin-btn-success {
         background: #4caf50;
         color: white;
      }
      .admin-btn-success:hover {
         background: transparent;
         color: #4caf50;
         border-color: #4caf50;
      }
      .admin-btn-warning {
         background: #ff9800;
         color: white;
      }
      .admin-btn-warning:hover {
         background: transparent;
         color: #ff9800;
         border-color: #ff9800;
      }
      .admin-btn-danger {
         background: #f44336;
         color: white;
      }
      .admin-btn-danger:hover {
         background: transparent;
         color: #f44336;
         border-color: #f44336;
      }
      .recent-activities {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
      }
      .recent-activities h3 {
         color: #1a237e;
         margin-bottom: 1.5rem;
         font-size: 1.8rem;
         display: flex;
         justify-content: space-between;
         align-items: center;
      }
      .activity-list {
         list-style: none;
      }
      .activity-item {
         padding: 1rem;
         border-bottom: 1px solid #e8eaf6;
         display: flex;
         align-items: flex-start;
         gap: 1rem;
      }
      .activity-item:last-child {
         border-bottom: none;
      }
      .activity-icon {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         background: #e8eaf6;
         display: flex;
         align-items: center;
         justify-content: center;
         color: #1a237e;
         flex-shrink: 0;
      }
      .activity-content {
         flex: 1;
      }
      .activity-title {
         font-weight: 600;
         color: #1a237e;
         margin-bottom: 0.2rem;
      }
      .activity-description {
         color: #5c6bc0;
         font-size: 0.9rem;
         margin-bottom: 0.5rem;
      }
      .activity-meta {
         display: flex;
         justify-content: space-between;
         font-size: 0.8rem;
         color: #9fa8da;
      }
      .admin-nav-bar {
         background: #1a237e;
         padding: 1rem 15%;
         display: flex;
         justify-content: space-between;
         align-items: center;
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         z-index: 1000;
         box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      }
      .admin-nav-brand {
         color: white;
         font-size: 1.5rem;
         font-weight: 700;
         text-decoration: none;
         font-family: var(--heading);
      }
      .admin-nav-menu {
         display: flex;
         gap: 1.5rem;
         align-items: center;
      }
      .admin-nav-link {
         color: white;
         text-decoration: none;
         font-weight: 600;
         padding: 0.5rem 1rem;
         border-radius: 5px;
         transition: all 0.3s;
         font-size: 0.9rem;
      }
      .admin-nav-link:hover {
         background: rgba(255, 255, 255, 0.1);
      }
      .admin-signout-btn {
         background: #f44336;
         color: white;
         border: none;
         padding: 0.5rem 1.5rem;
         border-radius: 5px;
         cursor: pointer;
         font-weight: 600;
         transition: all 0.3s;
         font-size: 0.9rem;
      }
      .admin-signout-btn:hover {
         background: #d32f2f;
      }
      .admin-management {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 3rem;
      }
      .admin-table {
         width: 100%;
         border-collapse: collapse;
         margin-top: 1rem;
      }
      .admin-table th {
         background: #e8eaf6;
         color: #1a237e;
         padding: 1rem;
         text-align: left;
         font-weight: 600;
      }
      .admin-table td {
         padding: 1rem;
         border-bottom: 1px solid #e8eaf6;
         color: #5c6bc0;
      }
      .admin-table tr:hover {
         background: #f5f5f5;
      }
      .role-badge {
         display: inline-block;
         padding: 0.2rem 0.8rem;
         border-radius: 20px;
         font-size: 0.8rem;
         font-weight: 600;
      }
      .role-super_admin {
         background: #ff9800;
         color: white;
      }
      .role-admin {
         background: #2196f3;
         color: white;
      }
      .role-moderator {
         background: #4caf50;
         color: white;
      }
      .action-buttons {
         display: flex;
         gap: 0.5rem;
      }
      .modal-overlay {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         background: rgba(0, 0, 0, 0.5);
         display: flex;
         align-items: center;
         justify-content: center;
         z-index: 2000;
         display: none;
      }
      .modal {
         background: white;
         border-radius: 15px;
         padding: 2rem;
         width: 90%;
         max-width: 500px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
      }
      .modal h3 {
         color: #1a237e;
         margin-bottom: 1.5rem;
      }
      .form-group {
         margin-bottom: 1.5rem;
      }
      .form-group label {
         display: block;
         margin-bottom: 0.5rem;
         color: #1a237e;
         font-weight: 600;
      }
      .form-group input,
      .form-group select {
         width: 100%;
         padding: 0.8rem;
         border: 2px solid #c5cae9;
         border-radius: 8px;
         font-size: 1rem;
      }
      .form-group input:focus,
      .form-group select:focus {
         outline: none;
         border-color: #ff9800;
      }
      .modal-buttons {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      @media screen and (max-width: 768px) {
         .admin-nav-bar {
               padding: 1rem 5%;
               flex-direction: column;
               gap: 1rem;
         }
         .admin-nav-menu {
               gap: 1rem;
               flex-wrap: wrap;
               justify-content: center;
         }
         .admin-dashboard-container {
               padding: 2.5% 5% 5%;
               padding-top: 120px;
         }
         .admin-table {
               display: block;
               overflow-x: auto;
         }
      }
   </style>
</head>
<body>
   <div class="progress-container">
      <div id="scrollProgress"></div>
   </div>
   
   <nav class="admin-nav-bar">
      <a href="admin_dashboard.php" class="admin-nav-brand">Code Library Admin</a>
      <div class="admin-nav-menu">
         <a href="admin_dashboard.php" class="admin-nav-link">Dashboard</a>
         <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
               <a href="#admin-management" class="admin-nav-link">Manage Admins</a>
         <?php endif; ?>
         <a href="#" class="admin-nav-link">Reports</a>
         <a href="#" class="admin-nav-link">Settings</a>
         <form action="./assets/admin_logout.php" method="POST" style="display: inline;">
               <button type="submit" class="admin-signout-btn">Sign Out</button>
         </form>
      </div>
   </nav>
   
   <main id="main">
      <section class="admin-dashboard-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="admin-header scroll-effect">
               <div class="admin-greeting">
                  Welcome back, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>! 👑
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
                     echo $lastLogin ? date('F j, Y H:i:s', strtotime($lastLogin)) : 'First login';
                  } catch (PDOException $e) {
                     echo 'N/A';
                  }
               ?></p>
         </div>
         
         <div class="admin-grid scroll-effect">
               <div class="admin-card stats">
                  <h3>📊 Statistics</h3>
                  <div class="stat-number"><?php echo $totalUsers; ?></div>
                  <div class="stat-label">Total Users</div>
                  <p>Manage user accounts and permissions</p>
                  <div class="admin-actions">
                     <a href="#" class="admin-btn admin-btn-primary">View Users</a>
                     <a href="#" class="admin-btn admin-btn-success">Add User</a>
                  </div>
               </div>
               
               <div class="admin-card users">
                  <h3>👥 Admin Team</h3>
                  <div class="stat-number"><?php echo $totalAdmins; ?></div>
                  <div class="stat-label">Active Admins</div>
                  <p>Manage administrator accounts and permissions</p>
                  <div class="admin-actions">
                     <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
                           <button onclick="showAddAdminModal()" class="admin-btn admin-btn-success">Add Admin</button>
                     <?php endif; ?>
                     <a href="#admin-management" class="admin-btn admin-btn-primary">View All</a>
                  </div>
               </div>
               
               <div class="admin-card admins">
                  <h3>🔧 System</h3>
                  <div class="stat-number">⚙️</div>
                  <div class="stat-label">Configuration</div>
                  <p>System settings and configuration</p>
                  <div class="admin-actions">
                     <a href="#" class="admin-btn admin-btn-warning">System Settings</a>
                     <a href="#" class="admin-btn admin-btn-primary">Backup</a>
                  </div>
               </div>
               
               <div class="admin-card system">
                  <h3>📈 Analytics</h3>
                  <div class="stat-number">📊</div>
                  <div class="stat-label">Performance</div>
                  <p>System performance and analytics</p>
                  <div class="admin-actions">
                     <a href="#" class="admin-btn admin-btn-primary">View Analytics</a>
                     <a href="#" class="admin-btn admin-btn-success">Generate Report</a>
                  </div>
               </div>
         </div>
         
         <!-- Admin Management Section (Super Admin Only) -->
         <?php if ($_SESSION['admin_role'] === 'super_admin'): ?>
         <div class="admin-management scroll-effect" id="admin-management">
               <h3>👑 Admin Management</h3>
               <p>Create and manage administrator accounts</p>
               
               <table class="admin-table">
                  <thead>
                     <tr>
                           <th>Name</th>
                           <th>Email</th>
                           <th>Role</th>
                           <th>Created By</th>
                           <th>Last Login</th>
                           <th>Status</th>
                           <th>Actions</th>
                     </tr>
                  </thead>
                  <tbody>
                     <?php foreach ($adminList as $admin): ?>
                     <tr>
                           <td><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></td>
                           <td><?php echo htmlspecialchars($admin['email']); ?></td>
                           <td>
                              <span class="role-badge role-<?php echo htmlspecialchars($admin['role']); ?>">
                                 <?php echo htmlspecialchars($admin['role']); ?>
                              </span>
                           </td>
                           <td>
                              <?php 
                              if ($admin['created_by']) {
                                 echo htmlspecialchars($admin['creator_first'] . ' ' . $admin['creator_last']);
                              } else {
                                 echo 'System';
                              }
                              ?>
                           </td>
                           <td>
                              <?php echo $admin['last_login'] ? date('M j, Y H:i', strtotime($admin['last_login'])) : 'Never'; ?>
                           </td>
                           <td>
                              <span style="color: <?php echo $admin['is_active'] ? '#4caf50' : '#f44336'; ?>">
                                 ● <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                              </span>
                           </td>
                           <td>
                              <div class="action-buttons">
                                 <button onclick="editAdmin(<?php echo $admin['id']; ?>)" 
                                          class="admin-btn admin-btn-warning" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">
                                       Edit
                                 </button>
                                 <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                 <button onclick="deleteAdmin(<?php echo $admin['id']; ?>)" 
                                          class="admin-btn admin-btn-danger" style="padding: 0.3rem 0.8rem; font-size: 0.8rem;">
                                       Remove
                                 </button>
                                 <?php endif; ?>
                              </div>
                           </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
         </div>
         <?php endif; ?>
         
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
                              <?php echo htmlspecialchars($activity['activity_type']); ?>
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
         
         <div class="admin-card scroll-effect" style="background: #1a237e; color: white;">
               <h3 style="color: white;">⚠️ Security Notes</h3>
               <p>As an administrator, you have elevated privileges. Please ensure:</p>
               <ul style="margin-top: 1rem; padding-left: 1.5rem;">
                  <li>Keep your credentials secure</li>
                  <li>Log out after each session</li>
                  <li>Review logs regularly</li>
                  <li>Follow security protocols</li>
               </ul>
               <div class="admin-actions" style="margin-top: 2rem;">
                  <a href="#" class="admin-btn" style="background: white; color: #1a237e;">My Profile</a>
                  <a href="#" class="admin-btn" style="background: #ff9800; color: white;">Change Password</a>
               </div>
         </div>
      </section>
   </main>
   
   <!-- Add Admin Modal -->
   <div class="modal-overlay" id="addAdminModal">
      <div class="modal">
         <h3>Add New Administrator</h3>
         <form id="addAdminForm" action="./assets/process_add_admin.php" method="POST">
               <div class="form-group">
                  <label for="first_name">First Name</label>
                  <input type="text" id="first_name" name="first_name" required>
               </div>
               <div class="form-group">
                  <label for="last_name">Last Name</label>
                  <input type="text" id="last_name" name="last_name" required>
               </div>
               <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" id="email" name="email" required>
               </div>
               <div class="form-group">
                  <label for="role">Role</label>
                  <select id="role" name="role" required>
                     <option value="admin">Admin</option>
                     <option value="moderator">Moderator</option>
                  </select>
               </div>
               <div class="form-group">
                  <label for="password">Temporary Password</label>
                  <input type="password" id="password" name="password" required>
                  <small style="color: #5c6bc0; display: block; margin-top: 0.5rem;">
                     Admin will be required to change password on first login
                  </small>
               </div>
               <div class="modal-buttons">
                  <button type="button" onclick="hideAddAdminModal()" class="admin-btn admin-btn-danger">Cancel</button>
                  <button type="submit" class="admin-btn admin-btn-success">Create Admin</button>
               </div>
         </form>
      </div>
   </div>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      // Modal functions
      function showAddAdminModal() {
         document.getElementById('addAdminModal').style.display = 'flex';
      }
      
      function hideAddAdminModal() {
         document.getElementById('addAdminModal').style.display = 'none';
      }
      
      function editAdmin(adminId) {
         // Implement edit functionality
         alert('Edit admin with ID: ' + adminId);
      }
      
      function deleteAdmin(adminId) {
         if (confirm('Are you sure you want to remove this admin?')) {
               // Implement delete functionality
               window.location.href = './assets/process_delete_admin.php?id=' + adminId;
         }
      }
      
      // Close modal when clicking outside
      document.getElementById('addAdminModal').addEventListener('click', function(e) {
         if (e.target === this) {
         }
      });
      
      // Form validation for add admin
      document.getElementById('addAdminForm').addEventListener('submit', function(e) {
         const password = document.getElementById('password').value;
         if (password.length < 8) {
               e.preventDefault();
               alert('Password must be at least 8 characters long.');
               return false;
         }
      });
      
      // Auto-refresh dashboard every 60 seconds
      setInterval(() => {
         // You can implement AJAX refresh here if needed
      }, 60000);
   </script>
</body>
</html>