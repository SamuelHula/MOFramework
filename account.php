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
   <title>Account Info - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      #header{
         height: 10vh;
      }
      .account-container {
         min-height: 100vh;
         padding: 2.5% 15% 5%;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
      }
      .account-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .account-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .account-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .account-content {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 3rem;
         margin-bottom: 3rem;
      }
      .account-card {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      .account-card h3 {
         color: var(--primary);
         margin-bottom: 1.5rem;
         font-size: 1.5rem;
         border-bottom: 2px solid var(--back-dark);
         padding-bottom: 0.5rem;
      }
      .info-group {
         margin-bottom: 1.5rem;
      }
      .info-label {
         font-weight: 600;
         color: var(--text-color);
         display: block;
         margin-bottom: 0.5rem;
      }
      .info-value {
         color: var(--text-color);
         opacity: 0.8;
         padding: 0.75rem;
         background: var(--back-light);
         border-radius: 8px;
         border: 1px solid var(--back-dark);
      }
      .danger-zone {
         background: #fff5f5;
         border: 2px solid #fed7d7;
         border-radius: 15px;
         padding: 2rem;
         margin-top: 2rem;
      }
      .danger-zone h3 {
         color: #e53e3e;
         border-bottom: 2px solid #fed7d7;
      }
      .danger-btn {
         background: #e53e3e;
         color: white;
         border: none;
         padding: 0.75rem 1.5rem;
         border-radius: 8px;
         cursor: pointer;
         font-size: 1rem;
         transition: all 0.3s;
         margin-top: 1rem;
         width: 100%;
      }
      .danger-btn:hover {
         background: #c53030;
      }
      .form-group {
         margin-bottom: 1.5rem;
      }
      .form-group label {
         display: block;
         margin-bottom: 0.5rem;
         font-weight: 600;
         color: var(--text-color);
      }
      .form-group input {
         width: 100%;
         padding: 0.75rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
      }
      .form-group input:focus {
         outline: none;
         border-color: var(--primary);
      }
      .primary_btn {
         margin-top: 1rem;
         width: 100%;
         text-align: center;
         padding: 1rem 2rem;
      }

      /* Responsive Design */
      @media screen and (max-width: 1200px) {
         .account-container {
            padding: 2.5% 10% 5%;
         }
      }

      @media screen and (max-width: 968px) {
         .account-container {
            padding: 2.5% 5% 5%;
         }
         
         .account-content {
            grid-template-columns: 1fr;
            gap: 2rem;
         }
         
         .account-header h1 {
            font-size: 2.5rem;
         }
         
         .account-header p {
            font-size: 1.1rem;
         }
      }

      @media screen and (max-width: 768px) {
         .account-container {
            padding: 2.5% 1rem 5%;
         }
         
         .account-header {
            margin-bottom: 2rem;
         }
         
         .account-header h1 {
            font-size: 2.2rem;
         }
         
         .account-header p {
            font-size: 1rem;
         }
         
         .account-card {
            padding: 1.5rem;
         }
         
         .account-card h3 {
            font-size: 1.3rem;
         }
         
         .info-group {
            margin-bottom: 1rem;
         }
         
         .info-value {
            padding: 0.5rem;
            font-size: 0.95rem;
         }
         
         .danger-zone {
            padding: 1.5rem;
            margin-top: 1.5rem;
         }
         
         .form-group {
            margin-bottom: 1rem;
         }
         
         .form-group input {
            padding: 0.6rem;
         }
      }

      @media screen and (max-width: 480px) {
         .account-header h1 {
            font-size: 1.8rem;
         }
         
         .account-header p {
            font-size: 0.9rem;
         }
         
         .account-card {
            padding: 1rem;
         }
         
         .account-card h3 {
            font-size: 1.2rem;
            margin-bottom: 1rem;
         }
         
         .info-label {
            font-size: 0.9rem;
         }
         
         .info-value {
            font-size: 0.9rem;
         }
         
         .danger-zone {
            padding: 1rem;
         }
         
         .danger-zone h3 {
            font-size: 1.1rem;
         }
         
         .danger-zone p {
            font-size: 0.9rem;
         }
         
         .danger-btn {
            padding: 0.6rem 1.2rem;
            font-size: 0.9rem;
         }
         
         .primary_btn {
            padding: 0.8rem 1.5rem;
            font-size: 1rem;
         }
      }

      /* Floating balls responsiveness */
      @media screen and (max-width: 768px) {
         .floating-balls .ball:nth-child(n+3) {
            display: none;
         }
         
         .floating-balls .ball {
            width: 80px;
            height: 80px;
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
      <section class="account-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="account-header scroll-effect">
            <h1>Account Information</h1>
            <p>Manage your account settings and preferences</p>
         </div>
         
         <div class="account-content scroll-effect">
            <div class="account-card">
               <h3>Profile Information</h3>
               <?php
               try {
                  $stmt = $pdo->prepare("SELECT first_name, last_name, email, created_at FROM users WHERE id = ?");
                  $stmt->execute([$_SESSION['user_id']]);
                  $user = $stmt->fetch(PDO::FETCH_ASSOC);
                  
                  if ($user) {
               ?>
               <div class="info-group">
                  <span class="info-label">First Name:</span>
                  <div class="info-value"><?php echo htmlspecialchars($user['first_name']); ?></div>
               </div>
               <div class="info-group">
                  <span class="info-label">Last Name:</span>
                  <div class="info-value"><?php echo htmlspecialchars($user['last_name']); ?></div>
               </div>
               <div class="info-group">
                  <span class="info-label">Email:</span>
                  <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
               </div>
               <div class="info-group">
                  <span class="info-label">Member Since:</span>
                  <div class="info-value"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></div>
               </div>
               <?php
                  }
               } catch (PDOException $e) {
                  echo "<div class='info-value'>Error loading user data</div>";
               }
               ?>
            </div>
            
            <div class="account-card">
               <h3>Change Password</h3>
               <form action="./assets/change_password.php" method="POST">
                  <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" required>
                  </div>
                  <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" required>
                  </div>
                  <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required>
                  </div>
                  <button type="submit" class="primary_btn">Change Password</button>
               </form>
               
               <div class="danger-zone">
                  <h3>Danger Zone</h3>
                  <p>Once you delete your account, there is no going back. Please be certain.</p>
                  <button type="button" class="danger-btn" onclick="confirmDelete()">Delete Account</button>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      function confirmDelete() {
         if (confirm('Are you sure you want to delete your account? This action cannot be undone.')) {
            window.location.href = './assets/delete_account.php';
         }
      }

      // Add form validation for password change
      document.addEventListener('DOMContentLoaded', function() {
         const passwordForm = document.querySelector('form[action="./assets/change_password.php"]');
         
         if (passwordForm) {
            passwordForm.addEventListener('submit', function(e) {
               const newPassword = document.getElementById('new_password');
               const confirmPassword = document.getElementById('confirm_password');
               let valid = true;
               
               if (newPassword.value !== confirmPassword.value) {
                  valid = false;
                  newPassword.style.borderColor = 'red';
                  confirmPassword.style.borderColor = 'red';
                  alert('New passwords do not match.');
                  e.preventDefault();
               }
               
               if (newPassword.value.length < 8) {
                  valid = false;
                  newPassword.style.borderColor = 'red';
                  alert('Password must be at least 8 characters long.');
                  e.preventDefault();
               }
            });
         }
      });
   </script>
</body>
</html>