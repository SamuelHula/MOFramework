<?php
require_once '../assets/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

$current_page = 'web_tools';
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Password Generator - Code Library</title>
   <link rel="stylesheet" href="../css/general.css">
   <link rel="stylesheet" href="../css/home.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
      }
      #header{
         height: 10vh;
      }
      .password-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .password-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .password-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .password-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .password-generator {
         display: grid;
         grid-template-columns: 1fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .settings-section, .output-section {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      .section-title {
         font-size: 1.8rem;
         margin-bottom: 1.5rem;
         color: var(--text-color);
         padding-bottom: 0.5rem;
         border-bottom: 2px solid var(--primary);
      }
      .form-group {
         margin-bottom: 1.5rem;
      }
      .form-group label {
         display: block;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1rem;
      }
      .length-slider {
         width: 100%;
         margin: 1rem 0;
      }
      .length-value {
         font-size: 1.2rem;
         font-weight: 600;
         color: var(--primary);
         text-align: center;
         margin-top: 0.5rem;
      }
      .checkbox-group {
         display: grid;
         grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
         gap: 1rem;
         margin-top: 1rem;
      }
      .checkbox-item {
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .checkbox-item input {
         width: 18px;
         height: 18px;
      }
      .checkbox-item label {
         margin: 0;
         font-weight: 500;
         color: var(--text-color);
      }
      .password-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .password-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
         min-width: 150px;
      }
      .password-btn.generate {
         background: var(--primary);
         color: white;
      }
      .password-btn.generate:hover {
         background: var(--secondary);
      }
      .password-btn.copy {
         background: #4CAF50;
         color: white;
      }
      .password-btn.copy:hover {
         background: #45a049;
      }
      .password-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .password-btn.reset:hover {
         background: var(--back-dark);
      }
      .password-display {
         background: #1e1e1e;
         color: #4CAF50;
         padding: 1.5rem;
         border-radius: 8px;
         font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
         font-size: 1.5rem;
         line-height: 1.5;
         text-align: center;
         word-wrap: break-word;
         min-height: 80px;
         margin-bottom: 1rem;
         letter-spacing: 1px;
         font-weight: 600;
         display: flex;
         align-items: center;
         justify-content: center;
      }
      .strength-meter {
         margin-top: 1.5rem;
      }
      .strength-label {
         display: flex;
         justify-content: space-between;
         margin-bottom: 0.5rem;
      }
      .strength-bar {
         height: 10px;
         background: #e0e0e0;
         border-radius: 5px;
         overflow: hidden;
      }
      .strength-fill {
         height: 100%;
         width: 0%;
         transition: all 0.3s ease;
         border-radius: 5px;
      }
      .strength-weak { background: #ff4444; }
      .strength-medium { background: #ffa700; }
      .strength-strong { background: #00C851; }
      .strength-very-strong { background: #007E33; }
      .copy-success {
         background: #4CAF50;
         color: white;
         padding: 0.5rem 1rem;
         border-radius: 5px;
         margin-top: 1rem;
         text-align: center;
         display: none;
         animation: fadeInOut 3s ease;
      }
      @keyframes fadeInOut {
         0%, 100% { opacity: 0; }
         10%, 90% { opacity: 1; }
      }
      @media screen and (max-width: 1200px) {
         .password-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .password-container {
            padding: 2% 1rem 5%;
         }
         .password-header h1 {
            font-size: 2.2rem;
         }
         .password-actions {
            flex-direction: column;
         }
         .password-btn {
            width: 100%;
         }
         .checkbox-group {
            grid-template-columns: 1fr;
         }
      }
   </style>
</head>
<body>
   <div class="progress-container">
      <div id="scrollProgress"></div>
   </div>
   <header id="header">
      <?php include '../assets/nav_bar.php' ?>
   </header>
   
   <main id="main">
      <section class="password-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="password-header scroll-effect">
            <h1>Password Generator</h1>
            <p>Generate strong, secure passwords with customizable options</p>
         </div>
         
         <div class="password-generator scroll-effect">
            <div class="settings-section">
               <h2 class="section-title">Settings</h2>
               
               <div class="form-group">
                  <label for="passwordLength">Password Length: <span id="lengthValue">12</span> characters</label>
                  <input type="range" id="passwordLength" class="length-slider" min="6" max="32" value="12">
               </div>
               
               <div class="form-group">
                  <label>Character Types:</label>
                  <div class="checkbox-group">
                     <div class="checkbox-item">
                        <input type="checkbox" id="uppercase" checked>
                        <label for="uppercase">Uppercase Letters (A-Z)</label>
                     </div>
                     <div class="checkbox-item">
                        <input type="checkbox" id="lowercase" checked>
                        <label for="lowercase">Lowercase Letters (a-z)</label>
                     </div>
                     <div class="checkbox-item">
                        <input type="checkbox" id="numbers" checked>
                        <label for="numbers">Numbers (0-9)</label>
                     </div>
                     <div class="checkbox-item">
                        <input type="checkbox" id="symbols" checked>
                        <label for="symbols">Symbols (!@#$%^&*)</label>
                     </div>
                  </div>
               </div>
               
               <div class="password-actions">
                  <button class="password-btn generate" onclick="generatePassword()">
                     <i class="fas fa-key"></i> Generate Password
                  </button>
                  <button class="password-btn reset" onclick="resetSettings()">
                     <i class="fas fa-redo"></i> Reset Settings
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated Password</h2>
               
               <div class="password-display" id="passwordDisplay">
                  Click "Generate Password" to create a secure password
               </div>
               
               <div class="strength-meter">
                  <div class="strength-label">
                     <span>Password Strength:</span>
                     <span id="strengthText">None</span>
                  </div>
                  <div class="strength-bar">
                     <div class="strength-fill" id="strengthFill"></div>
                  </div>
               </div>
               
               <div class="password-actions">
                  <button class="password-btn copy" onclick="copyPassword()">
                     <i class="fas fa-copy"></i> Copy Password
                  </button>
                  <button class="password-btn generate" onclick="generatePassword()">
                     <i class="fas fa-sync-alt"></i> Generate New
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Password copied to clipboard!
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         const lengthSlider = document.getElementById('passwordLength');
         const lengthValue = document.getElementById('lengthValue');
         
         lengthSlider.addEventListener('input', function() {
            lengthValue.textContent = this.value;
         });
         
         // Generate initial password
         generatePassword();
      });
      
      function generatePassword() {
         const length = parseInt(document.getElementById('passwordLength').value);
         const uppercase = document.getElementById('uppercase').checked;
         const lowercase = document.getElementById('lowercase').checked;
         const numbers = document.getElementById('numbers').checked;
         const symbols = document.getElementById('symbols').checked;
         
         // Check if at least one character type is selected
         if (!uppercase && !lowercase && !numbers && !symbols) {
            alert('Please select at least one character type.');
            return;
         }
         
         // Define character sets
         const uppercaseChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
         const lowercaseChars = 'abcdefghijklmnopqrstuvwxyz';
         const numberChars = '0123456789';
         const symbolChars = '!@#$%^&*()_+-=[]{}|;:,.<>?';
         
         // Build available characters based on selections
         let availableChars = '';
         if (uppercase) availableChars += uppercaseChars;
         if (lowercase) availableChars += lowercaseChars;
         if (numbers) availableChars += numberChars;
         if (symbols) availableChars += symbolChars;
         
         // Generate password
         let password = '';
         for (let i = 0; i < length; i++) {
            const randomIndex = Math.floor(Math.random() * availableChars.length);
            password += availableChars[randomIndex];
         }
         
         // Ensure at least one of each selected character type is included
         let finalPassword = password;
         if (uppercase && !/[A-Z]/.test(finalPassword)) {
            const index = Math.floor(Math.random() * finalPassword.length);
            finalPassword = finalPassword.substring(0, index) + 
                           uppercaseChars[Math.floor(Math.random() * uppercaseChars.length)] + 
                           finalPassword.substring(index + 1);
         }
         if (lowercase && !/[a-z]/.test(finalPassword)) {
            const index = Math.floor(Math.random() * finalPassword.length);
            finalPassword = finalPassword.substring(0, index) + 
                           lowercaseChars[Math.floor(Math.random() * lowercaseChars.length)] + 
                           finalPassword.substring(index + 1);
         }
         if (numbers && !/[0-9]/.test(finalPassword)) {
            const index = Math.floor(Math.random() * finalPassword.length);
            finalPassword = finalPassword.substring(0, index) + 
                           numberChars[Math.floor(Math.random() * numberChars.length)] + 
                           finalPassword.substring(index + 1);
         }
         if (symbols && !/[!@#$%^&*()_+\-=\[\]{}|;:,.<>?]/.test(finalPassword)) {
            const index = Math.floor(Math.random() * finalPassword.length);
            finalPassword = finalPassword.substring(0, index) + 
                           symbolChars[Math.floor(Math.random() * symbolChars.length)] + 
                           finalPassword.substring(index + 1);
         }
         
         // Display password
         document.getElementById('passwordDisplay').textContent = finalPassword;
         
         // Calculate and display strength
         updatePasswordStrength(finalPassword);
      }
      
      function updatePasswordStrength(password) {
         let score = 0;
         
         // Length score
         if (password.length >= 8) score += 1;
         if (password.length >= 12) score += 1;
         if (password.length >= 16) score += 1;
         
         // Character variety score
         if (/[A-Z]/.test(password)) score += 1;
         if (/[a-z]/.test(password)) score += 1;
         if (/[0-9]/.test(password)) score += 1;
         if (/[^A-Za-z0-9]/.test(password)) score += 1;
         
         // Determine strength level
         let strengthText = 'Very Weak';
         let strengthClass = 'strength-weak';
         let strengthPercent = 25;
         
         if (score >= 6) {
            strengthText = 'Very Strong';
            strengthClass = 'strength-very-strong';
            strengthPercent = 100;
         } else if (score >= 5) {
            strengthText = 'Strong';
            strengthClass = 'strength-strong';
            strengthPercent = 75;
         } else if (score >= 4) {
            strengthText = 'Medium';
            strengthClass = 'strength-medium';
            strengthPercent = 50;
         } else if (score >= 2) {
            strengthText = 'Weak';
            strengthPercent = 25;
         }
         
         // Update UI
         document.getElementById('strengthText').textContent = strengthText;
         const strengthFill = document.getElementById('strengthFill');
         strengthFill.className = 'strength-fill ' + strengthClass;
         strengthFill.style.width = strengthPercent + '%';
      }
      
      function copyPassword() {
         const password = document.getElementById('passwordDisplay').textContent;
         
         if (password.includes('Click')) {
            alert('Please generate a password first.');
            return;
         }
         
         const textarea = document.createElement('textarea');
         textarea.value = password;
         document.body.appendChild(textarea);
         textarea.select();
         document.execCommand('copy');
         document.body.removeChild(textarea);
         
         const successMsg = document.getElementById('copySuccess');
         successMsg.style.display = 'block';
         
         setTimeout(() => {
            successMsg.style.display = 'none';
         }, 3000);
      }
      
      function resetSettings() {
         document.getElementById('passwordLength').value = 12;
         document.getElementById('lengthValue').textContent = '12';
         document.getElementById('uppercase').checked = true;
         document.getElementById('lowercase').checked = true;
         document.getElementById('numbers').checked = true;
         document.getElementById('symbols').checked = true;
         generatePassword();
      }
   </script>
</body>
</html>