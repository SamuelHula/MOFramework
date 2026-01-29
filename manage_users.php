<?php
require_once './assets/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

$active_page = 'manage_users';

try {
   $stmt = $pdo->query("SELECT id, first_name, last_name, email, role, created_at, updated_at FROM users ORDER BY created_at DESC");
   $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   error_log("Failed to fetch users: " . $e->getMessage());
   $users = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manage Users - Admin Dashboard</title>
   <link rel="stylesheet" href="./css/general.css">
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
      .manage-users-container {
         padding: 2rem;
         max-width: 1400px;
         margin: 0 auto;
      }
      .page-header {
         text-align: center;
         margin-bottom: 3rem;
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      }
      .page-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .page-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
      }
      .users-actions {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 2rem;
         background: white;
         padding: 1.5rem 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      }
      .search-box {
         display: flex;
         gap: 1rem;
         flex: 1;
         max-width: 500px;
      }
      .search-box input {
         flex: 1;
         padding: 0.8rem 1.2rem;
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         font-size: 1rem;
         transition: all 0.3s;
      }
      .search-box input:focus {
         outline: none;
         border-color: var(--primary);
      }
      .search-btn, .add-user-btn {
         padding: 0.8rem 1.5rem;
         border-radius: 10px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-family: var(--subheading);
      }
      .search-btn {
         background: var(--back-dark);
         color: var(--text-color);
      }
      .search-btn:hover {
         background: var(--primary);
         color: white;
      }
      .add-user-btn {
         background: var(--primary);
         color: white;
         text-decoration: none;
         display: inline-block;
      }
      .add-user-btn:hover {
         background: var(--secondary);
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(48, 188, 237, 0.3);
      }
      .users-table-container {
         background: white;
         border-radius: 20px;
         padding: 2rem;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         overflow-x: auto;
      }
      .users-table {
         width: 100%;
         border-collapse: collapse;
      }
      .users-table th {
         background: var(--back-light);
         padding: 1.2rem 1rem;
         text-align: left;
         font-weight: 600;
         color: var(--text-color);
         border-bottom: 2px solid var(--back-dark);
         font-family: var(--subheading);
      }
      .users-table td {
         padding: 1rem;
         border-bottom: 1px solid var(--back-dark);
         color: var(--text-color);
      }
      .users-table tr:hover {
         background: var(--back-light);
      }
      .user-avatar {
         width: 40px;
         height: 40px;
         border-radius: 50%;
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         color: white;
         display: flex;
         align-items: center;
         justify-content: center;
         font-weight: 600;
         font-size: 1.2rem;
      }
      .role-badge {
         padding: 0.4rem 0.8rem;
         border-radius: 20px;
         font-size: 0.85rem;
         font-weight: 600;
         display: inline-block;
      }
      .role-user {
         background: #e3f2fd;
         color: #1976d2;
      }
      .role-premium {
         background: #fff3e0;
         color: #f57c00;
      }
      .role-admin {
         background: #e8f5e9;
         color: #388e3c;
      }
      .role-moderator {
         background: #f3e5f5;
         color: #7b1fa2;
      }
      .action-buttons {
         display: flex;
         gap: 0.5rem;
      }
      .btn-edit, .btn-delete {
         padding: 0.5rem 1rem;
         border-radius: 6px;
         border: none;
         cursor: pointer;
         font-weight: 600;
         font-size: 0.9rem;
         transition: all 0.3s;
         text-decoration: none;
         display: inline-block;
      }
      .btn-edit {
         background: var(--primary);
         color: white;
      }
      .btn-edit:hover {
         background: var(--secondary);
      }
      .btn-delete {
         background: #f44336;
         color: white;
      }
      .btn-delete:hover {
         background: #d32f2f;
      }
      .no-users {
         text-align: center;
         padding: 3rem;
         color: var(--text-color);
         opacity: 0.7;
      }
      .stats-cards {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
         gap: 1.5rem;
         margin-bottom: 2rem;
      }
      .stat-card {
         background: white;
         padding: 1.5rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
         text-align: center;
      }
      .stat-number {
         font-size: 2.5rem;
         font-weight: 700;
         color: var(--primary);
         margin-bottom: 0.5rem;
         font-family: var(--heading);
      }
      .stat-label {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1rem;
      }
      .bulk-actions {
         display: flex;
         gap: 1rem;
         align-items: center;
         margin-top: 1rem;
         padding-top: 1rem;
         border-top: 1px solid var(--back-dark);
      }
      .select-all {
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .bulk-select-btn {
         padding: 0.5rem 1rem;
         background: var(--back-dark);
         border: none;
         border-radius: 6px;
         cursor: pointer;
      }
      .btn-danger {
         background: #f44336;
         color: white;
         padding: 0.5rem 1rem;
         border-radius: 6px;
         border: none;
         cursor: pointer;
         font-weight: 600;
      }
      .btn-danger:hover {
         background: #d32f2f;
      }
      @media screen and (max-width: 768px) {
         body {
               padding-top: 120px;
         }
         .manage-users-container {
               padding: 1rem;
         }
         .users-actions {
               flex-direction: column;
               gap: 1rem;
               align-items: stretch;
         }
         .search-box {
               max-width: 100%;
         }
         .users-table-container {
               padding: 1rem;
         }
         .users-table {
               font-size: 0.9rem;
         }
         .action-buttons {
               flex-direction: column;
         }
         .stats-cards {
               grid-template-columns: 1fr;
         }
      }
            @media screen and (max-width: 768px) {
         .users-table-container {
            padding: 1rem;
            overflow-x: auto;
         }
         
         .users-table {
            min-width: 800px; 
         }
         
         .users-actions {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
         }
         
         .search-box {
            width: 100%;
            max-width: 100%;
         }
         
         .stats-cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
         }
         
         .stat-card {
            padding: 1rem;
         }
         
         .stat-number {
            font-size: 2rem;
         }
         
         .bulk-actions {
            flex-wrap: wrap;
         }
      }
      @media screen and (max-width: 480px) {
         .manage-users-container {
            padding: 0.5rem;
         }
         
         .page-header {
            padding: 1.5rem;
         }
         
         .page-header h1 {
            font-size: 1.8rem;
         }
         
         .page-header p {
            font-size: 1rem;
         }
         
         .users-actions {
            flex-direction: column;
            gap: 1rem;
            padding: 1rem;
         }
         
         .search-box {
            flex-direction: column;
            gap: 0.8rem;
            width: 100%;
         }
         
         .search-box input {
            width: 100%;
            font-size: 16px;
            padding: 0.8rem;
         }
         
         .search-btn,
         .add-user-btn {
            width: 100%;
            text-align: center;
            padding: 0.9rem;
         }
         
         .users-table-container {
            padding: 0.8rem;
            overflow-x: auto;
         }
         
         .users-table {
            min-width: 600px;
            font-size: 0.85rem;
         }
         
         .users-table th,
         .users-table td {
            padding: 0.6rem;
         }
         
         .user-avatar {
            width: 30px;
            height: 30px;
            font-size: 0.9rem;
         }
         
         .role-badge {
            font-size: 0.75rem;
            padding: 0.3rem 0.6rem;
         }
         
         .action-buttons {
            flex-direction: column;
            gap: 0.3rem;
         }
         
         .btn-edit,
         .btn-delete {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
         }
         
         .stats-cards {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.8rem;
         }
         
         .stat-card {
            padding: 1rem;
         }
         
         .stat-number {
            font-size: 1.8rem;
         }
         
         .stat-label {
            font-size: 0.9rem;
         }
         
         .bulk-actions {
            flex-wrap: wrap;
            gap: 0.5rem;
         }
      }
      @media screen and (max-width: 350px) {
         body {
            padding-top: 60px;
         }
         
         .page-header {
            padding: 1rem;
         }
         
         .page-header h1 {
            font-size: 1.5rem;
         }
         
         .page-header p {
            font-size: 0.9rem;
         }
         
         .search-box input {
            font-size: 14px;
            padding: 0.7rem;
         }
         
         .search-btn,
         .add-user-btn {
            padding: 0.8rem;
            font-size: 0.9rem;
         }
         
         .users-table {
            min-width: 500px;
            font-size: 0.8rem;
         }
         
         .users-table th,
         .users-table td {
            padding: 0.5rem;
         }
         
         .stats-cards {
            grid-template-columns: 1fr;
         }
         
         .stat-number {
            font-size: 1.5rem;
         }
         
         .bulk-actions {
            flex-direction: column;
         }
         
         .bulk-select-btn,
         .btn-danger {
            width: 100%;
            padding: 0.6rem;
         }
      }
   </style>
</head>
<body>
   <?php include_once './includes/admin_navbar.php'; ?>
   
   <main id="main">
      <section class="manage-users-container">
         <div class="page-header scroll-effect">
               <h1>User Management</h1>
               <p>Manage all registered users and their information</p>
         </div>
         
         <?php if (isset($_GET['success'])): ?>
               <div class="success-message" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border-left: 4px solid #2e7d32;">
                  <?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
               </div>
         <?php endif; ?>
         
         <?php if (isset($_GET['error'])): ?>
               <div class="error-message" style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border-left: 4px solid #c62828;">
                  <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
               </div>
         <?php endif; ?>
         
         <div class="stats-cards scroll-effect">
               <?php
               try {
                  $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
                  $totalUsers = $stmt->fetch()['total'];
                  
                  $stmt = $pdo->query("SELECT COUNT(*) as new_users FROM users WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
                  $newUsers = $stmt->fetch()['new_users'];
                  
                  $stmt = $pdo->query("SELECT role, COUNT(*) as count FROM users GROUP BY role");
                  $roleStats = $stmt->fetchAll(PDO::FETCH_ASSOC);
                  
                  $userCount = 0;
                  $premiumCount = 0;
                  foreach ($roleStats as $stat) {
                     if ($stat['role'] === 'user') $userCount = $stat['count'];
                     if ($stat['role'] === 'premium') $premiumCount = $stat['count'];
                  }
               } catch (PDOException $e) {
                  $totalUsers = $newUsers = $userCount = $premiumCount = 0;
               }
               ?>
               
               <div class="stat-card">
                  <div class="stat-number"><?php echo $totalUsers; ?></div>
                  <div class="stat-label">Total Users</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-number"><?php echo $newUsers; ?></div>
                  <div class="stat-label">New Users (7d)</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-number"><?php echo $userCount; ?></div>
                  <div class="stat-label">Regular Users</div>
               </div>
               
               <div class="stat-card">
                  <div class="stat-number"><?php echo $premiumCount; ?></div>
                  <div class="stat-label">Premium Users</div>
               </div>
         </div>
         
         <div class="users-actions scroll-effect">
               <div class="search-box">
                  <input type="text" id="searchInput" placeholder="Search users by name, email...">
                  <button class="search-btn" onclick="searchUsers()">Search</button>
               </div>
               <a href="add_user.php" class="add-user-btn">+ Add New User</a>
         </div>
         
         <div class="users-table-container scroll-effect">
               <?php if (empty($users)): ?>
                  <div class="no-users">
                     <h3>No users found</h3>
                     <p>Start by adding your first user</p>
                     <a href="add_user.php" class="add-user-btn" style="margin-top: 1rem;">Add First User</a>
                  </div>
               <?php else: ?>
                  <table class="users-table">
                     <thead>
                           <tr>
                              <th>
                                 <div class="select-all">
                                       <input type="checkbox" id="selectAll">
                                       <label for="selectAll">Select</label>
                                 </div>
                              </th>
                              <th>User</th>
                              <th>Email</th>
                              <th>Role</th>
                              <th>Joined</th>
                              <th>Last Updated</th>
                              <th>Actions</th>
                           </tr>
                     </thead>
                     <tbody>
                           <?php foreach ($users as $user): ?>
                              <tr>
                                 <td><input type="checkbox" class="user-checkbox" value="<?php echo $user['id']; ?>"></td>
                                 <td>
                                       <div style="display: flex; align-items: center; gap: 1rem;">
                                          <div class="user-avatar">
                                             <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
                                          </div>
                                          <div>
                                             <strong><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></strong><br>
                                             <small>ID: <?php echo $user['id']; ?></small>
                                          </div>
                                       </div>
                                 </td>
                                 <td><?php echo htmlspecialchars($user['email']); ?></td>
                                 <td>
                                       <span class="role-badge role-<?php echo $user['role']; ?>">
                                          <?php echo ucfirst($user['role']); ?>
                                       </span>
                                 </td>
                                 <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                 <td>
                                       <?php if ($user['updated_at']): ?>
                                          <?php echo date('M j, Y', strtotime($user['updated_at'])); ?>
                                       <?php else: ?>
                                          <span style="opacity: 0.5;">Never</span>
                                       <?php endif; ?>
                                 </td>
                                 <td>
                                       <div class="action-buttons">
                                          <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn-edit">Edit</a>
                                          <a href="./assets/process_delete_user.php?id=<?php echo $user['id']; ?>" 
                                             class="btn-delete"
                                             onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                                       </div>
                                 </td>
                              </tr>
                           <?php endforeach; ?>
                     </tbody>
                  </table>
                  
                  <div class="bulk-actions">
                     <button class="bulk-select-btn" onclick="selectAllUsers()">Select All</button>
                     <button class="bulk-select-btn" onclick="deselectAllUsers()">Deselect All</button>
                     <button class="btn-danger" onclick="deleteSelectedUsers()">Delete Selected</button>
                  </div>
               <?php endif; ?>
         </div>
      </section>
   </main>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      function searchUsers() {
         const searchTerm = document.getElementById('searchInput').value.toLowerCase();
         const rows = document.querySelectorAll('.users-table tbody tr');
         
         rows.forEach(row => {
               const text = row.textContent.toLowerCase();
               row.style.display = text.includes(searchTerm) ? '' : 'none';
         });
      }
      
      document.getElementById('searchInput').addEventListener('keyup', function(e) {
         if (e.key === 'Enter') {
               searchUsers();
         }
      });
      
      document.getElementById('selectAll').addEventListener('change', function() {
         const checkboxes = document.querySelectorAll('.user-checkbox');
         checkboxes.forEach(checkbox => {
               checkbox.checked = this.checked;
         });
      });
      
      function selectAllUsers() {
         const checkboxes = document.querySelectorAll('.user-checkbox');
         checkboxes.forEach(checkbox => {
               checkbox.checked = true;
         });
         document.getElementById('selectAll').checked = true;
      }
      
      function deselectAllUsers() {
         const checkboxes = document.querySelectorAll('.user-checkbox');
         checkboxes.forEach(checkbox => {
               checkbox.checked = false;
         });
         document.getElementById('selectAll').checked = false;
      }
      
      function getSelectedUserIds() {
         const checkboxes = document.querySelectorAll('.user-checkbox:checked');
         return Array.from(checkboxes).map(cb => cb.value);
      }
      
      function deleteSelectedUsers() {
         const ids = getSelectedUserIds();
         if (ids.length === 0) {
               alert('Please select at least one user.');
               return;
         }
         
         if (confirm(`Are you sure you want to delete ${ids.length} user(s)? This action cannot be undone.`)) {
               window.location.href = `./assets/process_bulk_delete_users.php?ids=${ids.join(',')}`;
         }
      }
   </script>
</body>
</html>