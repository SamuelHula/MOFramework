<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sign In - Code Library</title>
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
      .auth-container {
         width: 100%;
         max-width: 450px;
         background: white;
         padding: 3rem;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         position: relative;
         z-index: 2;
      }
      .auth-header {
         text-align: center;
         margin-bottom: 2rem;
      }
      .auth-icon {
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
      .auth-header h1 {
         font-size: 2.5rem;
         margin-bottom: 0.5rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .auth-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
      }
      .auth-form {
         display: flex;
         flex-direction: column;
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
      .remember-forgot {
         display: flex;
         justify-content: space-between;
         align-items: center;
      }
      .remember-me {
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .remember-me input {
         width: 18px;
         height: 18px;
      }
      .remember-me label {
         font-size: 0.9rem;
         color: var(--text-color);
         margin: 0;
         font-family: var(--text_font);
      }
      .forgot-password {
         color: var(--primary);
         text-decoration: none;
         font-size: 0.9rem;
         transition: color 0.3s;
         font-family: var(--text_font);
      }
      .forgot-password:hover {
         color: var(--secondary);
      }
      .auth-submit {
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
      .auth-submit:hover {
         background: transparent;
         color: var(--primary);
      }
      .auth-divider {
         text-align: center;
         margin: 2rem 0;
         position: relative;
         color: var(--text-color);
         opacity: 0.7;
         font-family: var(--text_font);
      }
      .auth-divider::before {
         content: '';
         position: absolute;
         top: 50%;
         left: 0;
         right: 0;
         height: 1px;
         background: var(--back-dark);
      }
      .auth-divider span {
         background: white;
         padding: 0 1rem;
         position: relative;
         z-index: 1;
      }
      .social-auth {
         display: flex;
         gap: 1rem;
         margin-bottom: 2rem;
      }
      .social-btn {
         flex: 1;
         padding: 0.8rem;
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         background: white;
         display: flex;
         align-items: center;
         justify-content: center;
         gap: 0.5rem;
         text-decoration: none;
         color: var(--text-color);
         font-weight: 500;
         transition: all 0.3s ease;
         font-family: var(--text_font);
      }
      .social-btn:hover {
         border-color: var(--primary);
         transform: translateY(-2px);
      }
      .social-btn svg {
         width: 20px;
         height: 20px;
      }
      .auth-redirect {
         text-align: center;
         margin-top: 2rem;
         color: var(--text-color);
         opacity: 0.8;
         font-family: var(--text_font);
      }
      .auth-redirect a {
         color: var(--primary);
         text-decoration: none;
         font-weight: 600;
         transition: color 0.3s;
      }
      .auth-redirect a:hover {
         color: var(--secondary);
      }

      /* Floating Balls Styles */
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
         .auth-container {
            padding: 2rem 1.5rem;
         }
         .auth-header h1 {
            font-size: 2rem;
         }
         .social-auth {
            flex-direction: column;
         }
         .remember-forgot {
            flex-direction: column;
            gap: 1rem;
            align-items: flex-start;
         }
         /* Hide some balls on mobile for better performance */
         .auth-ball-3, .auth-ball-5 {
            display: none;
         }
      }
   </style>
</head>
<body>
   <?php if (isset($_GET['error'])): ?>
      <div class="error-message" style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border-left: 4px solid #c62828;">
         <?php 
         $errors = explode('|', $_GET['error']);
         foreach ($errors as $error) {
            echo htmlspecialchars($error) . '<br>';
         }
         ?>
      </div>
   <?php endif; ?>
   <!-- Floating Balls Background -->
   <div class="floating-balls">
      <div class="ball auth-ball-1"></div>
      <div class="ball auth-ball-2"></div>
      <div class="ball auth-ball-3"></div>
      <div class="ball auth-ball-4"></div>
      <div class="ball auth-ball-5"></div>
      <div class="ball auth-ball-6"></div>
   </div>

   <div class="auth-container">
      <div class="auth-header">
         <div class="auth-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="35" height="35" fill="white">
               <path d="M217.9 105.9L340.7 228.7c7.2 7.2 11.3 17.1 11.3 27.3s-4.1 20.1-11.3 27.3L217.9 406.1c-6.4 6.4-15 9.9-24 9.9c-18.7 0-33.9-15.2-33.9-33.9l0-62.1L32 320c-17.7 0-32-14.3-32-32l0-64c0-17.7 14.3-32 32-32l128 0 0-62.1c0-18.7 15.2-33.9 33.9-33.9c9 0 17.6 3.6 24 9.9zM352 416l64 0c17.7 0 32-14.3 32-32l0-256c0-17.7-14.3-32-32-32l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32l64 0c53 0 96 43 96 96l0 256c0 53-43 96-96 96l-64 0c-17.7 0-32-14.3-32-32s14.3-32 32-32z"/>
            </svg>
         </div>
         <h1>Welcome Back</h1>
         <p>Sign in to your account to continue</p>
      </div>
      
      <form class="auth-form" action="./assets/process_signin.php" method="POST">
         <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
         </div>
         
         <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
         </div>
         
         <div class="remember-forgot">
            <div class="remember-me">
               <input type="checkbox" id="remember" name="remember">
               <label for="remember">Remember me</label>
            </div>
            <a href="forgot_password.php" class="forgot-password">Forgot password?</a>
         </div>
         
         <button type="submit" class="auth-submit">Sign In</button>
      </form>
      
      <div class="auth-divider">
         <span>Or continue with</span>
      </div>
      
      <div class="social-auth">
         <a href="#" class="social-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512">
               <path fill="#1877F2" d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/>
            </svg>
            Facebook
         </a>
         <a href="#" class="social-btn">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 488 512">
               <path fill="#4285F4" d="M488 261.8C488 403.3 391.1 504 248 504 110.8 504 0 393.2 0 256S110.8 8 248 8c66.8 0 123 24.5 166.3 64.9l-67.5 64.9C258.5 52.6 94.3 116.6 94.3 256c0 86.5 69.1 156.6 153.7 156.6 98.2 0 135-70.4 140.8-106.9H248v-85.3h236.1c2.3 12.7 3.9 24.9 3.9 41.4z"/>
            </svg>
            Google
         </a>
      </div>
      
      <div class="auth-redirect">
         Don't have an account? <a href="signup.php">Sign up here</a>
      </div>
   </div>
   
   <script>
      // Form validation
      document.addEventListener('DOMContentLoaded', function() {
         const authForm = document.querySelector('.auth-form');
         
         if (authForm) {
            authForm.addEventListener('submit', function(e) {
               const email = document.getElementById('email');
               const password = document.getElementById('password');
               let valid = true;
               
               if (!email.value.trim()) {
                  valid = false;
                  email.style.borderColor = 'red';
               } else {
                  email.style.borderColor = '';
               }
               
               if (!password.value.trim()) {
                  valid = false;
                  password.style.borderColor = 'red';
               } else {
                  password.style.borderColor = '';
               }
               
               if (!valid) {
                  e.preventDefault();
                  alert('Please fill in all required fields.');
               }
            });
         }
      });
   </script>
</body>
</html>