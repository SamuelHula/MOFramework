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
      background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
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
      background: white;
      padding: 3rem;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      position: relative;
      z-index: 2;
   }
   .admin-header {
      text-align: center;
      margin-bottom: 2rem;
   }
   .admin-icon {
      width: 80px;
      height: 80px;
      background: linear-gradient(135deg, var(--primary), var(--secondary));
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
      color: var(--text-color);
      font-family: var(--heading);
   }
   .admin-header p {
      color: var(--text-color);
      opacity: 0.7;
      font-size: 1.1rem;
   }
   .admin-form {
      display: flex;
      flex-direction: column;
   }
   .form-group {
      display: flex;
      flex-direction: column;
      margin-bottom: 1.5rem;
   }
   .form-group label {
      font-weight: 600;
      color: var(--text-color);
      margin-bottom: 0.5rem;
      font-size: 1rem;
      font-family: var(--subheading);
   }
   .form-group input {
      padding: 1rem;
      border: 2px solid var(--back-dark);
      border-radius: 10px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: var(--back-light);
      font-family: var(--text_font);
   }
   .form-group input:focus {
      outline: none;
      border-color: var(--primary);
      background: white;
   }
   .admin-submit {
      padding: 1rem 2rem;
      background: var(--primary);
      color: var(--back-light);
      border: 2px solid var(--primary);
      border-radius: 10px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 1rem;
      font-family: var(--subheading);
   }
   .admin-submit:hover:not(:disabled) {
      background: transparent;
      color: var(--primary);
   }
   .admin-submit:disabled {
      opacity: 0.5;
      cursor: not-allowed;
      background: #cccccc;
      border-color: #cccccc;
      color: #666666;
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
   .warning-message {
      background: #fff3cd;
      color: #856404;
      padding: 1rem;
      border-radius: 10px;
      margin-bottom: 1.5rem;
      border-left: 4px solid #ffc107;
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
   .login-info {
      background: var(--back-light);
      padding: 1rem;
      border-radius: 10px;
      margin-top: 2rem;
      font-size: 0.9rem;
      color: var(--text-color);
      opacity: 0.8;
      text-align: center;
   }
   .login-info h4 {
      color: var(--text-color);
      margin-bottom: 0.5rem;
      opacity: 1;
   }
   .floating-balls {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
      z-index: 1;
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

   @media screen and (max-width: 480px) {
      body {
         padding: 1rem;
      }
      .admin-auth-container {
         padding: 2rem 1.5rem;
      }
      .admin-header h1 {
         font-size: 2rem;
      }
      .auth-ball-3, .auth-ball-5 {
         display: none;
      }
      .form-group{
            margin-bottom: 1rem;
         }
         .form-group input{
            padding: .5rem;
            border-radius: 5px;
         }
         .auth-submit{
            padding: 0.5rem;
         }
         .social-btn{
            padding: 0.5rem;
         }
         .login-info h4{
            font-size: 1rem;
         }
         .admin-submit{
            padding: .5rem;
         }
   }
</style>
</head>
<body>

<div class="floating-balls">
   <div class="ball auth-ball-1"></div>
   <div class="ball auth-ball-2"></div>
   <div class="ball auth-ball-3"></div>
   <div class="ball auth-ball-4"></div>
   <div class="ball auth-ball-5"></div>
   <div class="ball auth-ball-6"></div>
</div>

<div class="admin-auth-container">
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
                  
                  if (!document.querySelector('.error-message')) {
                     adminForm.insertBefore(errorDiv, adminForm.firstChild);
                  }
               }
            });
      }
      
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