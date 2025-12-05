<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Reset Password - Code Library</title>
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
      .auth-submit {
         padding: 1rem 2rem;
         background: var(--primary);
         color: white;
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
   
   <?php if (isset($_GET['success'])): ?>
      <div class="success-message" style="background: #e8f5e8; color: #2e7d32; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border-left: 4px solid #2e7d32;">
         <?php echo htmlspecialchars($_GET['success']); ?>
      </div>
   <?php endif; ?>

   <!-- Floating Balls Background -->
   <div class="floating-balls">
      <div class="ball auth-ball-1"></div>
      <div class="ball auth-ball-2"></div>
      <div class="ball auth-ball-3"></div>
      <div class="ball auth-ball-4"></div>
   </div>

   <div class="auth-container">
      <div class="auth-header">
         <div class="auth-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="35" height="35" fill="white">
               <path d="M336 352c97.2 0 176-78.8 176-176S433.2 0 336 0S160 78.8 160 176c0 18.7 2.9 36.8 8.3 53.7L7 391c-4.5 4.5-7 10.6-7 17v80c0 13.3 10.7 24 24 24h80c13.3 0 24-10.7 24-24V448h40c13.3 0 24-10.7 24-24V384h40c6.4 0 12.5-2.5 17-7l33.3-33.3c16.9 5.4 35 8.3 53.7 8.3zM376 96a40 40 0 1 1 0 80 40 40 0 1 1 0-80z"/>
            </svg>
         </div>
         <h1>Reset Password</h1>
         <p>Enter your email to receive a reset link</p>
      </div>
      
      <form class="auth-form" action="./assets/reset_password.php" method="POST">
         <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
         </div>
         
         <button type="submit" class="auth-submit">Send Reset Link</button>
      </form>
      
      <div class="auth-redirect">
         Remember your password? <a href="signin.php">Sign in here</a>
      </div>
   </div>
   
   <script>
      // Form validation
      document.addEventListener('DOMContentLoaded', function() {
         const authForm = document.querySelector('.auth-form');
         
         if (authForm) {
            authForm.addEventListener('submit', function(e) {
               const email = document.getElementById('email');
               let valid = true;
               
               if (!email.value.trim()) {
                  valid = false;
                  email.style.borderColor = 'red';
               } else {
                  email.style.borderColor = '';
               }
               
               if (!valid) {
                  e.preventDefault();
                  alert('Please fill in your email address.');
               }
            });
         }
      });
   </script>
</body>
</html>