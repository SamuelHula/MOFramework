<?php
require_once './assets/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add New User - Admin Dashboard</title>
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
      .add-user-container {
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
      .btn-submit, .btn-cancel {
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
      .password-strength {
         margin-top: 0.5rem;
         height: 4px;
         background: var(--back-dark);
         border-radius: 2px;
         overflow: hidden;
      }
      .strength-meter {
         height: 100%;
         width: 0%;
         background: #f44336;
         transition: all 0.3s;
      }
      .strength-meter.good {
         background: #ff9800;
      }
      .strength-meter.strong {
         background: #4caf50;
      }
      .form-note {
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
      @media screen and (max-width: 768px) {
         body {
               padding-top: 120px;
         }
         .add-user-container {
               padding: 2rem 1.5rem;
               margin: 1rem;
         }
         .form-actions {
               flex-direction: column;
         }
      }
      @media screen and (max-width: 768px) {
         .add-user-container,
         .edit-user-container {
            padding: 1.5rem;
            margin: 1rem;
            width: calc(100% - 2rem);
         }
         
         .page-header h1 {
            font-size: 2rem;
         }
         
         .user-form {
            gap: 1rem;
         }
         
         .form-actions {
            flex-direction: column;
         }
         
         .form-actions a,
         .form-actions button {
            width: 100%;
            text-align: center;
         }
      }
   </style>
</head>
<body>
   <?php include_once './includes/admin_navbar.php'; ?>
   
   <div class="add-user-container scroll-effect">
      <div class="page-header">
         <h1>Add New User</h1>
         <p>Create a new user account</p>
      </div>
      
      <?php if (isset($_GET['error'])): ?>
         <div class="error-message">
               <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
         </div>
      <?php endif; ?>
      
      <form class="user-form" action="./assets/process_add_user.php" method="POST">
         <div class="form-group">
               <label for="first_name">First Name *</label>
               <input type="text" id="first_name" name="first_name" required 
                     placeholder="Enter first name">
         </div>
         
         <div class="form-group">
               <label for="last_name">Last Name *</label>
               <input type="text" id="last_name" name="last_name" required 
                     placeholder="Enter last name">
         </div>
         
         <div class="form-group">
               <label for="email">Email Address *</label>
               <input type="email" id="email" name="email" required 
                     placeholder="user@example.com">
         </div>
         
         <div class="form-group">
               <label for="password">Password *</label>
               <input type="password" id="password" name="password" required 
                     placeholder="Enter password" onkeyup="checkPasswordStrength()">
               <div class="password-strength">
                  <div class="strength-meter" id="passwordStrength"></div>
               </div>
         </div>
         
         <div class="form-group">
               <label for="confirm_password">Confirm Password *</label>
               <input type="password" id="confirm_password" name="confirm_password" required 
                     placeholder="Confirm password" onkeyup="checkPasswordMatch()">
               <div id="passwordMatch" style="margin-top: 0.5rem; font-size: 0.9rem;"></div>
         </div>
         
         <div class="form-group">
               <label for="role">User Role</label>
               <select id="role" name="role">
                  <option value="user">User</option>
                  <option value="premium">Premium User</option>
               </select>
         </div>
         
         <div class="form-note">
               <strong>Note:</strong> Passwords must be at least 8 characters long.
         </div>
         
         <div class="form-actions">
               <button type="submit" class="btn-submit">Create User Account</button>
               <a href="manage_users.php" class="btn-cancel">Cancel</a>
         </div>
      </form>
   </div>
   
   <script src="./js/fly-in.js"></script>
   <script>
      function checkPasswordStrength() {
         const password = document.getElementById('password').value;
         const meter = document.getElementById('passwordStrength');
         
         if (password.length === 0) {
               meter.style.width = '0%';
               meter.className = 'strength-meter';
               return;
         }
         
         let strength = 0;
         
         if (password.length >= 8) strength += 25;
         if (password.length >= 12) strength += 15;
         
         if (/[A-Z]/.test(password)) strength += 20;
         if (/[a-z]/.test(password)) strength += 20;
         if (/[0-9]/.test(password)) strength += 20;
         if (/[^A-Za-z0-9]/.test(password)) strength += 20;
         
         strength = Math.min(strength, 100);
         
         meter.style.width = strength + '%';
         
         if (strength < 40) {
               meter.className = 'strength-meter';
         } else if (strength < 70) {
               meter.className = 'strength-meter good';
         } else {
               meter.className = 'strength-meter strong';
         }
      }
      
      function checkPasswordMatch() {
         const password = document.getElementById('password').value;
         const confirmPassword = document.getElementById('confirm_password').value;
         const matchDiv = document.getElementById('passwordMatch');
         
         if (confirmPassword.length === 0) {
               matchDiv.innerHTML = '';
               return;
         }
         
         if (password === confirmPassword) {
               matchDiv.innerHTML = '<span style="color: #4caf50;">✓ Passwords match</span>';
         } else {
               matchDiv.innerHTML = '<span style="color: #f44336;">✗ Passwords do not match</span>';
         }
      }
      
      document.querySelector('.user-form').addEventListener('submit', function(e) {
         const password = document.getElementById('password').value;
         const confirmPassword = document.getElementById('confirm_password').value;
         const firstName = document.getElementById('first_name').value;
         const lastName = document.getElementById('last_name').value;
         const email = document.getElementById('email').value;
         
         let errors = [];
         
         if (!firstName.trim()) errors.push('First name is required');
         if (!lastName.trim()) errors.push('Last name is required');
         if (!email.trim()) errors.push('Email is required');
         
         const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
         if (email && !emailRegex.test(email)) {
               errors.push('Please enter a valid email address');
         }
         
         if (password.length < 8) {
               errors.push('Password must be at least 8 characters long');
         }
         
         if (password !== confirmPassword) {
               errors.push('Passwords do not match');
         }
         
         if (errors.length > 0) {
               e.preventDefault();
               alert('Please fix the following errors:\n\n' + errors.join('\n'));
         }
      });
   </script>
</body>
</html>