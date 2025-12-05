<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Sign Up - Code Library</title>
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
         max-width: 550px;
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
      .name-fields {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 1rem;
      }
      .terms {
         display: flex;
         align-items: flex-start;
         gap: 0.5rem;
         margin: 1rem 0;
      }
      .terms input {
         margin-top: 0.2rem;
         width: 18px;
         height: 18px;
      }
      .terms label {
         font-size: 0.9rem;
         color: var(--text-color);
         line-height: 1.4;
         font-family: var(--text_font);
      }
      .terms a {
         color: var(--primary);
         text-decoration: none;
      }
      .terms a:hover {
         color: var(--secondary);
      }
      .auth-submit {
         padding: 1rem 2rem;
         background: var(--primary);
         color: var(--secondary);
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
         background: var(--secondary);
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
      .password-strength {
         margin-top: 0.5rem;
         font-size: 0.8rem;
         font-family: var(--text_font);
      }
      .strength-bar {
         height: 4px;
         background: var(--back-dark);
         border-radius: 2px;
         margin-top: 0.2rem;
         overflow: hidden;
      }
      .strength-fill {
         height: 100%;
         width: 0%;
         background: #ff4444;
         transition: all 0.3s ease;
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
         .name-fields {
            grid-template-columns: 1fr;
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
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512" width="35" height="35" fill="white">
               <path d="M96 128a128 128 0 1 1 256 0A128 128 0 1 1 96 128zM0 482.3C0 383.8 79.8 304 178.3 304h91.4C368.2 304 448 383.8 448 482.3c0 16.4-13.3 29.7-29.7 29.7H29.7C13.3 512 0 498.7 0 482.3zM504 312V248H440c-13.3 0-24-10.7-24-24s10.7-24 24-24h64V136c0-13.3 10.7-24 24-24s24 10.7 24 24v64h64c13.3 0 24 10.7 24 24s-10.7 24-24 24H552v64c0 13.3-10.7 24-24 24s-24-10.7-24-24z"/>
            </svg>
         </div>
         <h1>Join Us</h1>
         <p>Create your account to get started</p>
      </div>
      
      <form class="auth-form" action="./assets/process_signup.php" method="POST">
         <div class="name-fields">
            <div class="form-group">
               <label for="firstName">First Name</label>
               <input type="text" id="firstName" name="firstName" placeholder="First name" required>
            </div>
            <div class="form-group">
               <label for="lastName">Last Name</label>
               <input type="text" id="lastName" name="lastName" placeholder="Last name" required>
            </div>
         </div>
         
         <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" placeholder="Enter your email" required>
         </div>
         
         <div class="form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" placeholder="Create a password" required>
            <div class="password-strength">
               <div>Password strength: <span id="strength-text">Weak</span></div>
               <div class="strength-bar">
                  <div class="strength-fill" id="strength-fill"></div>
               </div>
            </div>
         </div>
         
         <div class="form-group">
            <label for="confirmPassword">Confirm Password</label>
            <input type="password" id="confirmPassword" name="confirmPassword" placeholder="Confirm your password" required>
         </div>
         
         <div class="terms">
            <input type="checkbox" id="agreeTerms" name="agreeTerms" required>
            <label for="agreeTerms">I agree to the <a href="terms.php">Terms of Service</a> and <a href="privacy.php">Privacy Policy</a></label>
         </div>
         
         <button type="submit" class="auth-submit">Create Account</button>
      </form>
      
      <div class="auth-divider">
         <span>Or sign up with</span>
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
         Already have an account? <a href="signin.php">Sign in here</a>
      </div>
   </div>
   
   <script>
      // Form validation and password strength
      document.addEventListener('DOMContentLoaded', function() {
         const authForm = document.querySelector('.auth-form');
         const password = document.getElementById('password');
         const confirmPassword = document.getElementById('confirmPassword');
         const strengthText = document.getElementById('strength-text');
         const strengthFill = document.getElementById('strength-fill');
         
         // Password strength checker
         password.addEventListener('input', function() {
            const value = password.value;
            let strength = 0;
            
            if (value.length >= 8) strength += 25;
            if (/[A-Z]/.test(value)) strength += 25;
            if (/[0-9]/.test(value)) strength += 25;
            if (/[^A-Za-z0-9]/.test(value)) strength += 25;
            
            strengthFill.style.width = strength + '%';
            
            if (strength < 50) {
               strengthText.textContent = 'Weak';
               strengthFill.style.background = '#ff4444';
            } else if (strength < 75) {
               strengthText.textContent = 'Medium';
               strengthFill.style.background = '#ffa700';
            } else {
               strengthText.textContent = 'Strong';
               strengthFill.style.background = '#00C851';
            }
         });
         
         if (authForm) {
            authForm.addEventListener('submit', function(e) {
               const firstName = document.getElementById('firstName');
               const lastName = document.getElementById('lastName');
               const email = document.getElementById('email');
               const agreeTerms = document.getElementById('agreeTerms');
               let valid = true;
               
               // Check required fields
               const requiredFields = [firstName, lastName, email, password, confirmPassword];
               requiredFields.forEach(field => {
                  if (!field.value.trim()) {
                     valid = false;
                     field.style.borderColor = 'red';
                  } else {
                     field.style.borderColor = '';
                  }
               });
               
               // Check password match
               if (password.value !== confirmPassword.value) {
                  valid = false;
                  password.style.borderColor = 'red';
                  confirmPassword.style.borderColor = 'red';
                  alert('Passwords do not match.');
               }
               
               // Check terms agreement
               if (!agreeTerms.checked) {
                  valid = false;
                  alert('Please agree to the Terms of Service and Privacy Policy.');
               }
               
               if (!valid) {
                  e.preventDefault();
               }
            });
         }
      });
   </script>
</body>
</html>