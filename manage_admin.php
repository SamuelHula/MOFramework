<?php
// manage_admin.php
require_once './assets/config.php';

// Check if admin is logged in and is super_admin
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true || $_SESSION['admin_role'] !== 'super_admin') {
   header("Location: admin_dashboard.php");
   exit;
}

// Set active page for navbar
$active_page = 'manage_admins';

// Handle actions
$action = $_GET['action'] ?? '';
$adminId = $_GET['id'] ?? 0;

// Get admin list
try {
   $stmt = $pdo->query("
      SELECT a.*, creator.first_name as creator_first, creator.last_name as creator_last 
      FROM admins a 
      LEFT JOIN admins creator ON a.created_by = creator.id 
      WHERE a.is_active = 1 
      ORDER BY a.created_at DESC
   ");
   $adminList = $stmt->fetchAll(PDO::FETCH_ASSOC);
   
   // Get specific admin for editing
   $editAdmin = null;
   if ($action === 'edit' && $adminId > 0) {
      $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ? AND is_active = 1");
      $stmt->execute([$adminId]);
      $editAdmin = $stmt->fetch(PDO::FETCH_ASSOC);
   }
   
} catch (PDOException $e) {
   error_log("Failed to fetch admin data: " . $e->getMessage());
   $adminList = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Manage Admins - Code Library</title>
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
         overflow-x: hidden;
         padding-top: 70px;
      }
      .manage-admin-container {
         padding: 2rem;
         max-width: 1400px;
         margin: 0 auto;
      }
      .admin-management-header {
         text-align: center;
         margin-bottom: 3rem;
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         position: relative;
         z-index: 2;
      }
      .admin-management-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .admin-management-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
         margin-bottom: 1.5rem;
      }
      .admin-management {
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         margin-bottom: 3rem;
         position: relative;
      }
      .admin-management h2 {
         color: var(--text-color);
         margin-bottom: 1.5rem;
         font-size: 2rem;
         font-family: var(--heading);
         display: flex;
         justify-content: space-between;
         align-items: center;
         text-align: center;
      }
      .admin-table {
         width: 100%;
         border-collapse: separate;
         border-spacing: 0;
         margin-top: 1rem;
         border-radius: 10px;
         overflow: hidden;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
      }
      .admin-table th {
         background: var(--back-light);
         color: var(--text-color);
         padding: 1.2rem 1rem;
         text-align: left;
         font-weight: 600;
         font-family: var(--subheading);
         border-bottom: 2px solid var(--back-dark);
      }
      .admin-table td {
         padding: 1rem;
         color: var(--text-color);
         opacity: 0.8;
         border-bottom: 1px solid var(--back-dark);
      }
      .admin-table tr:hover {
         background: var(--back-light);
      }
      .role-badge {
         display: inline-block;
         padding: 0.3rem 1rem;
         border-radius: 20px;
         font-size: 0.85rem;
         font-weight: 600;
         font-family: var(--subheading);
      }
      .role-super_admin {
         background: linear-gradient(135deg, #ff9800, #ff5722);
         color: white;
      }
      .role-admin {
         background: linear-gradient(135deg, #2196f3, #1976d2);
         color: white;
      }
      .role-moderator {
         background: linear-gradient(135deg, #4caf50, #2e7d32);
         color: white;
      }
      .action-buttons {
         display: flex;
         gap: 0.5rem;
      }
      .admin-btn {
         padding: 0.6rem 1rem;
         border-radius: 8px;
         margin-bottom: 1rem;
         text-decoration: none;
         font-weight: 600;
         transition: all 0.3s;
         text-align: center;
         border: 2px solid transparent;
         cursor: pointer;
         font-size: 0.9rem;
         font-family: var(--subheading);
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
      .add-admin-form {
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
         margin-bottom: 3rem;
      }
      .add-admin-form h3 {
         color: var(--text-color);
         margin-bottom: 1.5rem;
         font-size: 1.8rem;
         font-family: var(--heading);
         text-align: center;
      }
      .form-group {
         margin-bottom: 1.5rem;
      }
      .form-group label {
         display: block;
         margin-bottom: 0.5rem;
         color: var(--text-color);
         font-weight: 600;
         font-family: var(--subheading);
      }
      .form-group input,
      .form-group select {
         width: 100%;
         padding: 0.9rem;
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         font-size: 1rem;
         font-family: var(--text_font);
         background: var(--back-light);
         transition: all 0.3s;
      }
      .form-group input:focus,
      .form-group select:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
         box-shadow: 0 0 0 3px rgba(var(--primary-rgb), 0.1);
      }
      .form-buttons {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
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
      .form-card-decor {
         position: absolute;
         top: 0;
         right: 0;
         width: 80px;
         height: 80px;
         background: linear-gradient(135deg, var(--primary), transparent);
         border-radius: 0 20px 0 50px;
         opacity: 0.1;
      }
      .table-header-actions {
         display: flex;
         gap: 1rem;
         align-items: center;
         justify-content: space-between;
      }
      @media screen and (max-width: 768px) {
         body {
            padding-top: 120px;
         }
         .manage-admin-container {
            padding: 1rem;
         }
         .admin-management-header h1 {
            font-size: 2rem;
         }
         .admin-management, .add-admin-form {
            padding: 1.5rem;
         }
         .admin-table {
            display: block;
            overflow-x: auto;
         }
         .action-buttons {
            flex-direction: column;
            gap: 0.3rem;
         }
         .form-buttons {
            flex-direction: column;
         }
         .admin-btn {
            width: 100%;
            padding: 0.8rem;
         }
         /* Hide some balls on mobile for better performance */
         .auth-ball-3, .auth-ball-5 {
            display: none;
         }
      }
      /* Add to existing media query in manage_admin.php */
@media screen and (max-width: 768px) {
   .admin-table {
      display: block;
      overflow-x: auto;
      white-space: nowrap;
   }
   
   .admin-table th,
   .admin-table td {
      padding: 0.8rem;
      font-size: 0.9rem;
   }
   
   .action-buttons {
      flex-direction: column;
      gap: 0.3rem;
   }
   
   .table-header-actions {
      flex-direction: column;
      gap: 1rem;
      align-items: flex-start;
   }
   
   .table-header-actions a {
      align-self: flex-start;
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
      <section class="manage-admin-container">
         <div class="admin-management-header scroll-effect">
               <h1>Admin Management</h1>
               <p>Create and manage administrator accounts with different permission levels</p>
         </div>
         
         <!-- Add/Edit Admin Form -->
         <?php if ($action === 'add' || $action === 'edit'): ?>
         <div class="add-admin-form scroll-effect">
               <div class="form-card-decor"></div>
               <h3><?php echo $action === 'edit' ? 'Edit Administrator' : 'Add New Administrator'; ?></h3>
               <form action="./assets/process_admin_<?php echo $action === 'edit' ? 'edit' : 'add'; ?>.php" method="POST">
                  <?php if ($action === 'edit'): ?>
                     <input type="hidden" name="admin_id" value="<?php echo $editAdmin['id']; ?>">
                  <?php endif; ?>
                  
                  <div class="form-group">
                     <label for="first_name">First Name</label>
                     <input type="text" id="first_name" name="first_name" 
                           value="<?php echo $action === 'edit' ? htmlspecialchars($editAdmin['first_name']) : ''; ?>" 
                           placeholder="Enter first name" required>
                  </div>
                  
                  <div class="form-group">
                     <label for="last_name">Last Name</label>
                     <input type="text" id="last_name" name="last_name" 
                           value="<?php echo $action === 'edit' ? htmlspecialchars($editAdmin['last_name']) : ''; ?>" 
                           placeholder="Enter last name" required>
                  </div>
                  
                  <div class="form-group">
                     <label for="email">Email Address</label>
                     <input type="email" id="email" name="email" 
                           value="<?php echo $action === 'edit' ? htmlspecialchars($editAdmin['email']) : ''; ?>" 
                           placeholder="admin@codelibrary.dev" required>
                  </div>
                  
                  <div class="form-group">
                     <label for="role">Role</label>
                     <select id="role" name="role" required>
                           <option value="moderator" <?php echo ($action === 'edit' && $editAdmin['role'] === 'moderator') ? 'selected' : ''; ?>>Moderator</option>
                           <option value="admin" <?php echo ($action === 'edit' && $editAdmin['role'] === 'admin') ? 'selected' : ''; ?>>Admin</option>
                           <option value="super_admin" <?php echo ($action === 'edit' && $editAdmin['role'] === 'super_admin') ? 'selected' : ''; ?>>Super Admin</option>
                     </select>
                     <small style="color: #5c6bc0; display: block; margin-top: 0.5rem;">
                           <strong>Moderator:</strong> Basic content management<br>
                           <strong>Admin:</strong> Full system access<br>
                           <strong>Super Admin:</strong> Full access + Admin management
                     </small>
                  </div>
                  
                  <?php if ($action === 'add'): ?>
                  <div class="form-group">
                     <label for="password">Temporary Password</label>
                     <input type="password" id="password" name="password" placeholder="Enter temporary password" required>
                     <small style="color: #5c6bc0; display: block; margin-top: 0.5rem;">
                           Administrator will be required to change password on first login (minimum 8 characters)
                     </small>
                  </div>
                  <?php endif; ?>
                  
                  <div class="form-buttons">
                     <a href="manage_admin.php" class="admin-btn admin-btn-danger">Cancel</a>
                     <button type="submit" class="admin-btn admin-btn-success">
                           <?php echo $action === 'edit' ? 'Update Administrator' : 'Create Administrator'; ?>
                     </button>
                  </div>
               </form>
         </div>
         <?php endif; ?>
         
         <!-- Admin List -->
         <div class="admin-management scroll-effect">
               <div class="table-header-actions">
                  <h2>Administrator Team</h2>
                  <a href="manage_admin.php?action=add" class="admin-btn admin-btn-success">+ Add New Admin</a>
               </div>
               <p style="color: var(--text-color); opacity: 0.7; margin-bottom: 1.5rem;">
                  Manage all administrator accounts and their permission levels. Only Super Admins can manage other admins.
               </p>
               
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
                           <td>
                              <strong><?php echo htmlspecialchars($admin['first_name'] . ' ' . $admin['last_name']); ?></strong>
                           </td>
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
                                 echo '<em>System</em>';
                              }
                              ?>
                           </td>
                           <td>
                              <?php echo $admin['last_login'] ? date('M j, Y H:i', strtotime($admin['last_login'])) : '<em>Never</em>'; ?>
                           </td>
                           <td>
                              <span style="color: <?php echo $admin['is_active'] ? '#4caf50' : '#f44336'; ?>; font-weight: 600;">
                                 ● <?php echo $admin['is_active'] ? 'Active' : 'Inactive'; ?>
                              </span>
                           </td>
                           <td>
                              <div class="action-buttons">
                                 <a href="manage_admin.php?action=edit&id=<?php echo $admin['id']; ?>" 
                                    class="admin-btn admin-btn-warning">Edit</a>
                                 <?php if ($admin['id'] != $_SESSION['admin_id']): ?>
                                 <button onclick="confirmDelete(<?php echo $admin['id']; ?>)" 
                                          class="admin-btn admin-btn-danger">Remove</button>
                                 <?php endif; ?>
                              </div>
                           </td>
                     </tr>
                     <?php endforeach; ?>
                  </tbody>
               </table>
         </div>
      </section>
   </main>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      function confirmDelete(adminId) {
         if (confirm('Are you sure you want to remove this administrator? This action cannot be undone.')) {
               window.location.href = './assets/process_delete_admin.php?id=' + adminId;
         }
      }
      
      // Form validation for add admin
      document.addEventListener('DOMContentLoaded', function() {
         const addAdminForm = document.querySelector('form');
         if (addAdminForm && addAdminForm.querySelector('#password')) {
               addAdminForm.addEventListener('submit', function(e) {
                  const password = document.getElementById('password').value;
                  if (password.length < 8) {
                     e.preventDefault();
                     alert('Password must be at least 8 characters long.');
                     return false;
                  }
               });
         }
      });
   </script>
</body>
</html>