<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Portal - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <style>
      * {
         margin: 0;
         padding: 0;
         box-sizing: border-box;
      }
      body {
         background: linear-gradient(135deg, #1a237e 0%, #283593 50%, #3949ab 100%);
         min-height: 100vh;
         display: flex;
         align-items: center;
         justify-content: center;
         padding: 2rem;
         font-family: var(--text_font);
         position: relative;
         overflow-x: hidden;
      }
      .admin-auth-container {
         width: 100%;
         max-width: 450px;
         background: rgba(255, 255, 255, 0.95);
         padding: 2.5rem 3rem 3rem;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
         position: relative;
         z-index: 2;
         border: 3px solid #ff9800;
         margin-top: 2rem;
      }
      .admin-header {
         text-align: center;
         margin-bottom: 1.5rem;
      }
      .admin-icon {
         width: 80px;
         height: 80px;
         background: linear-gradient(135deg, #ff9800, #ff5722);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 1.5rem;
         color: white;
      }
      .admin-header h1 {
         font-size: 2.5rem;
         margin-bottom: 0.5rem;
         color: #1a237e;
         font-family: var(--heading);
      }
      .admin-header p {
         color: #5c6bc0;
         font-size: 1.1rem;
      }
      .admin-form {
         display: flex;
         flex-direction: column;
         margin-top: 1rem;
      }
      .form-group {
         display: flex;
         flex-direction: column;
         margin-bottom: 1.5rem;
      }
      .form-group label {
         font-weight: 600;
         color: #1a237e;
         margin-bottom: 0.5rem;
         font-size: 1rem;
         font-family: var(--subheading);
      }
      .form-group input {
         padding: 1rem;
         border: 2px solid #5c6bc0;
         border-radius: 10px;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: #e8eaf6;
         font-family: var(--text_font);
      }
      .form-group input:focus {
         outline: none;
         border-color: #ff9800;
         background: white;
      }
      .admin-submit {
         padding: 1rem 2rem;
         background: linear-gradient(135deg, #ff9800, #ff5722);
         color: white;
         border: none;
         border-radius: 10px;
         font-size: 1.1rem;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s ease;
         margin-top: 0.5rem;
         font-family: var(--subheading);
      }
      .admin-submit:hover:not(:disabled) {
         transform: translateY(-2px);
         box-shadow: 0 10px 20px rgba(255, 152, 0, 0.3);
      }
      .admin-submit:disabled {
         opacity: 0.5;
         cursor: not-allowed;
      }
      .error-message {
         background: #ffebee;
         color: #c62828;
         padding: 1rem;
         border-radius: 10px;
         margin-bottom: 1.5rem;
         border-left: 4px solid #c62828;
         font-size: 0.95rem;
         animation: slideDown 0.3s ease-out;
      }
      .warning-message {
         background: #fff3cd;
         color: #856404;
         padding: 1rem;
         border-radius: 10px;
         margin-bottom: 1.5rem;
         border-left: 4px solid #ffc107;
         font-size: 0.95rem;
         animation: slideDown 0.3s ease-out;
      }
      .success-message {
         background: #e8f5e9;
         color: #2e7d32;
         padding: 1rem;
         border-radius: 10px;
         margin-bottom: 1.5rem;
         border-left: 4px solid #2e7d32;
         font-size: 0.95rem;
         animation: slideDown 0.3s ease-out;
      }
      .login-info {
         background: #e8eaf6;
         padding: 1rem;
         border-radius: 10px;
         margin-top: 1.5rem;
         font-size: 0.9rem;
         color: #5c6bc0;
         text-align: center;
      }
      .login-info h4 {
         color: #1a237e;
         margin-bottom: 0.5rem;
      }
      .messages-container {
         position: absolute;
         top: -50px;
         left: 0;
         right: 0;
         z-index: 1000;
      }
      @keyframes slideDown {
         from {
            opacity: 0;
            transform: translateY(-10px);
         }
         to {
            opacity: 1;
            transform: translateY(0);
         }
      }
      @media screen and (max-width: 480px) {
         body {
               padding: 1rem;
         }
         .admin-auth-container {
               padding: 2rem 1.5rem;
               margin-top: 0;
         }
         .admin-header h1 {
               font-size: 2rem;
         }
      }
   </style>
</head>
<body>
   <div class="admin-auth-container">
      <!-- Messages inside the container -->
      <div style="margin-bottom: 1.5rem;">
         <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
               <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
            </div>
         <?php endif; ?>
         
         <?php if (isset($error)): ?>
            <div class="warning-message">
               <?php echo htmlspecialchars($error); ?>
            </div>
         <?php endif; ?>
         
         <?php if (isset($_GET['logout'])): ?>
            <div class="success-message">
               You have been successfully logged out.
            </div>
         <?php endif; ?>
      </div>
      
      <div class="admin-header">
         <div class="admin-icon">
               <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="35" height="35" fill="white">
                  <path d="M217.9 105.9L340.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L217.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1L32 320c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM352 416l64 0c17.7 0 32-14.3 32-32l0-256c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64 0c53 0 96 43 96 96l0 256c0 53-43 96-96 96l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/>
               </svg>
         </div>
         <h1>Admin Portal</h1>
         <p>Restricted Access - Authorized Personnel Only</p>
      </div>
      
      <?php if (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5 && (time() - $_SESSION['last_attempt']) < 900): ?>
         <div class="warning-message">
               Account locked due to too many failed attempts. Please try again in <?php echo ceil((900 - (time() - $_SESSION['last_attempt'])) / 60); ?> minutes.
         </div>
      <?php else: ?>
         <form class="admin-form" action="./assets/process_admin_signin.php" method="POST">
               <div class="form-group">
                  <label for="email">Email Address</label>
                  <input type="email" id="email" name="email" placeholder="admin@codelibrary.dev" required 
                        value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
               </div>
               
               <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" id="password" name="password" placeholder="Enter your password" required>
               </div>
               
               <button type="submit" class="admin-submit" <?php echo (isset($_SESSION['login_attempts']) && $_SESSION['login_attempts'] >= 5) ? 'disabled' : ''; ?>>
                  Sign In
               </button>
         </form>
      <?php endif; ?>
      
      <div class="login-info">
         <h4>🔒 Restricted Area</h4>
         <p>This portal is for authorized administrators only. All activities are logged and monitored.</p>
      </div>
   </div>
   
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const adminForm = document.querySelector('.admin-form');
         
         if (adminForm) {
               adminForm.addEventListener('submit', function(e) {
                  const email = document.getElementById('email');
                  const password = document.getElementById('password');
                  let valid = true;
                  
                  if (!email.value.trim()) {
                     valid = false;
                     email.style.borderColor = '#c62828';
                  } else {
                     email.style.borderColor = '';
                  }
                  
                  if (!password.value.trim()) {
                     valid = false;
                     password.style.borderColor = '#c62828';
                  } else {
                     password.style.borderColor = '';
                  }
                  
                  if (!valid) {
                     e.preventDefault();
                     const errorDiv = document.createElement('div');
                     errorDiv.className = 'error-message';
                     errorDiv.textContent = 'Please fill in all required fields.';
                     
                     // Insert error message at the top of the form
                     if (!document.querySelector('.error-message')) {
                        adminForm.insertBefore(errorDiv, adminForm.firstChild);
                     }
                  }
               });
         }
         
         // Auto-remove error messages after 5 seconds
         setTimeout(() => {
               const messages = document.querySelectorAll('.error-message, .warning-message, .success-message');
               messages.forEach(msg => {
                  msg.style.opacity = '0';
                  msg.style.transition = 'opacity 0.5s ease';
                  setTimeout(() => msg.remove(), 500);
               });
         }, 5000);
      });
   </script>
</body>
</html>