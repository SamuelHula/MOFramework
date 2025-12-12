<?php
// edit_user.php
require_once './assets/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

// Get user ID from URL
if (!isset($_GET['id'])) {
   header("Location: manage_users.php?error=User+ID+not+provided");
   exit;
}

$userId = intval($_GET['id']);

// Fetch user data
try {
   $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
   $stmt->execute([$userId]);
   $user = $stmt->fetch(PDO::FETCH_ASSOC);
   
   if (!$user) {
      header("Location: manage_users.php?error=User+not+found");
      exit;
   }
} catch (PDOException $e) {
   error_log("Failed to fetch user: " . $e->getMessage());
   header("Location: manage_users.php?error=Database+error");
   exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit User - Admin Dashboard</title>
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
         display: flex;
         justify-content: center;
         align-items: center;
      }
      .edit-user-container {
         width: 100%;
         max-width: 600px;
         background: white;
         padding: 3rem;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         margin: 2rem;
      }
      .page-header {
         text-align: center;
         margin-bottom: 2rem;
      }
      .page-header h1 {
         font-size: 2.5rem;
         margin-bottom: 0.5rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .page-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
      }
      .user-info {
         background: var(--back-light);
         padding: 1.5rem;
         border-radius: 10px;
         margin-bottom: 2rem;
         display: flex;
         align-items: center;
         gap: 1.5rem;
      }
      .user-avatar-large {
         width: 80px;
         height: 80px;
         border-radius: 50%;
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         color: white;
         display: flex;
         align-items: center;
         justify-content: center;
         font-weight: 600;
         font-size: 2rem;
      }
      .user-details h3 {
         font-size: 1.5rem;
         margin-bottom: 0.5rem;
         color: var(--text-color);
      }
      .user-details p {
         color: var(--text-color);
         opacity: 0.7;
      }
      .user-form {
         display: flex;
         flex-direction: column;
         gap: 1.5rem;
      }
      .form-group {
         display: flex;
         flex-direction: column;
      }
      .form-group label {
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1rem;
         font-family: var(--subheading);
      }
      .form-group input,
      .form-group select {
         padding: 0.9rem 1.2rem;
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: var(--back-light);
         font-family: var(--text_font);
      }
      .form-group input:focus,
      .form-group select:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .form-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         padding-top: 2rem;
         border-top: 1px solid var(--back-dark);
      }
      .btn-submit, .btn-cancel, .btn-delete {
         padding: 1rem 2rem;
         border-radius: 10px;
         font-size: 1.1rem;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s ease;
         border: 2px solid transparent;
         font-family: var(--subheading);
         text-decoration: none;
         text-align: center;
      }
      .btn-submit {
         flex: 1;
         background: var(--primary);
         color: white;
      }
      .btn-submit:hover {
         background: var(--secondary);
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(48, 188, 237, 0.3);
      }
      .btn-cancel {
         background: transparent;
         color: var(--text-color);
         border-color: var(--back-dark);
      }
      .btn-cancel:hover {
         background: var(--back-dark);
      }
      .btn-delete {
         background: #f44336;
         color: white;
      }
      .btn-delete:hover {
         background: #d32f2f;
      }
      .password-note {
         background: var(--back-light);
         padding: 1rem;
         border-radius: 10px;
         margin-top: 1rem;
         font-size: 0.9rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .error-message {
         background: #ffebee;
         color: #c62828;
         padding: 1rem;
         border-radius: 10px;
         margin-bottom: 1.5rem;
         border-left: 4px solid #c62828;
         font-size: 0.95rem;
      }
      .success-message {
         background: #e8f5e9;
         color: #2e7d32;
         padding: 1rem;
         border-radius: 10px;
         margin-bottom: 1.5rem;
         border-left: 4px solid #2e7d32;
         font-size: 0.95rem;
      }
      @media screen and (max-width: 768px) {
         body {
               padding-top: 120px;
         }
         .edit-user-container {
               padding: 2rem 1.5rem;
               margin: 1rem;
         }
         .user-info {
               flex-direction: column;
               text-align: center;
         }
         .form-actions {
               flex-direction: column;
         }
      }
   </style>
</head>
<body>
   <?php include_once './includes/admin_navbar.php'; ?>
   
   <div class="edit-user-container scroll-effect">
      <div class="page-header">
         <h1>Edit User</h1>
         <p>Update user account information</p>
      </div>
      
      <?php if (isset($_GET['success'])): ?>
         <div class="success-message">
               <?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
         </div>
      <?php endif; ?>
      
      <?php if (isset($_GET['error'])): ?>
         <div class="error-message">
               <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
         </div>
      <?php endif; ?>
      
      <div class="user-info">
         <div class="user-avatar-large">
               <?php echo strtoupper(substr($user['first_name'], 0, 1) . substr($user['last_name'], 0, 1)); ?>
         </div>
         <div class="user-details">
               <h3><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h3>
               <p>User ID: <?php echo $user['id']; ?> | Email: <?php echo htmlspecialchars($user['email']); ?></p>
               <p>Member since: <?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
         </div>
      </div>
      
      <form class="user-form" action="./assets/process_edit_user.php" method="POST">
         <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
         
         <div class="form-group">
               <label for="first_name">First Name *</label>
               <input type="text" id="first_name" name="first_name" required 
                     value="<?php echo htmlspecialchars($user['first_name']); ?>">
         </div>
         
         <div class="form-group">
               <label for="last_name">Last Name *</label>
               <input type="text" id="last_name" name="last_name" required 
                     value="<?php echo htmlspecialchars($user['last_name']); ?>">
         </div>
         
         <div class="form-group">
               <label for="email">Email Address *</label>
               <input type="email" id="email" name="email" required 
                     value="<?php echo htmlspecialchars($user['email']); ?>">
         </div>
         
         <div class="password-note">
               <strong>Note:</strong> Leave password fields blank to keep the current password.
         </div>
         
         <div class="form-group">
               <label for="password">New Password (Optional)</label>
               <input type="password" id="password" name="password" 
                     placeholder="Enter new password">
         </div>
         
         <div class="form-group">
               <label for="confirm_password">Confirm New Password</label>
               <input type="password" id="confirm_password" name="confirm_password" 
                     placeholder="Confirm new password">
         </div>
         
         <div class="form-group">
               <label for="role">User Role</label>
               <select id="role" name="role">
                  <option value="user" <?php echo $user['role'] == 'user' ? 'selected' : ''; ?>>User</option>
                  <option value="premium" <?php echo $user['role'] == 'premium' ? 'selected' : ''; ?>>Premium User</option>
               </select>
         </div>
         
         <div class="form-actions">
               <button type="submit" class="btn-submit">Update User</button>
               <a href="manage_users.php" class="btn-cancel">Cancel</a>
               <a href="./assets/process_delete_user.php?id=<?php echo $user['id']; ?>" 
                  class="btn-delete"
                  onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.')">Delete User</a>
         </div>
      </form>
   </div>
   
   <script src="./js/fly-in.js"></script>
   <script>
      document.querySelector('.user-form').addEventListener('submit', function(e) {
         const password = document.getElementById('password').value;
         const confirmPassword = document.getElementById('confirm_password').value;
         const firstName = document.getElementById('first_name').value;
         const lastName = document.getElementById('last_name').value;
         const email = document.getElementById('email').value;
         
         let errors = [];
         
         // Check required fields
         if (!firstName.trim()) errors.push('First name is required');
         if (!lastName.trim()) errors.push('Last name is required');
         if (!email.trim()) errors.push('Email is required');
         
         // Email validation
         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         if (email && !emailRegex.test(email)) {
               errors.push('Please enter a valid email address');
         }
         
         // Password validation if provided
         if (password.length > 0) {
               if (password.length < 8) {
                  errors.push('Password must be at least 8 characters long');
               }
               
               if (password !== confirmPassword) {
                  errors.push('Passwords do not match');
               }
         }
         
         if (errors.length > 0) {
               e.preventDefault();
               alert('Please fix the following errors:\n\n' + errors.join('\n'));
         }
      });
   </script>
</body>
</html>