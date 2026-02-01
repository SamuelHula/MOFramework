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
   <title>Base64 Encoder/Decoder - Code Library</title>
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
      .base64-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .base64-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .base64-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .base64-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .base64-generator {
         display: grid;
         grid-template-columns: 1fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .input-section, .output-section {
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
      .form-group textarea {
         width: 100%;
         padding: 1rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: var(--back-light);
         font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
         min-height: 150px;
         resize: vertical;
      }
      .form-group textarea:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .mode-selection {
         display: flex;
         gap: 1rem;
         margin-bottom: 1.5rem;
         flex-wrap: wrap;
      }
      .mode-btn {
         padding: 0.8rem 1.5rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         background: white;
         cursor: pointer;
         transition: all 0.3s;
         font-weight: 500;
         flex: 1;
         min-width: 150px;
         text-align: center;
      }
      .mode-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .mode-btn:hover:not(.active) {
         border-color: var(--primary);
      }
      .base64-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .base64-btn {
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
      .base64-btn.encode {
         background: var(--primary);
         color: white;
      }
      .base64-btn.encode:hover {
         background: var(--secondary);
      }
      .base64-btn.decode {
         background: var(--secondary);
         color: white;
      }
      .base64-btn.decode:hover {
         background: var(--primary);
      }
      .base64-btn.copy {
         background: #4CAF50;
         color: white;
      }
      .base64-btn.copy:hover {
         background: #45a049;
      }
      .base64-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .base64-btn.reset:hover {
         background: var(--back-dark);
      }
      .output-code {
         background: #1e1e1e;
         color: #d4d4d4;
         padding: 1.5rem;
         border-radius: 8px;
         font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
         font-size: 0.9rem;
         line-height: 1.5;
         white-space: pre-wrap;
         word-wrap: break-word;
         min-height: 150px;
         overflow-y: auto;
         margin-bottom: 1rem;
      }
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
         .base64-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .base64-container {
            padding: 2% 1rem 5%;
         }
         .base64-header h1 {
            font-size: 2.2rem;
         }
         .mode-selection {
            flex-direction: column;
         }
         .base64-actions {
            flex-direction: column;
         }
         .mode-btn, .base64-btn {
            width: 100%;
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
      <section class="base64-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="base64-header scroll-effect">
            <h1>Base64 Encoder/Decoder</h1>
            <p>Encode text to Base64 or decode Base64 back to plain text</p>
         </div>
         
         <div class="base64-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Input</h2>
               
               <div class="mode-selection">
                  <button class="mode-btn active" data-mode="encode">Encode to Base64</button>
                  <button class="mode-btn" data-mode="decode">Decode from Base64</button>
               </div>
               
               <div class="form-group">
                  <label for="inputText">Enter text to encode or Base64 to decode:</label>
                  <textarea id="inputText" placeholder="Enter your text here..."></textarea>
               </div>
               
               <div class="base64-actions">
                  <button class="base64-btn encode" id="encodeBtn">Encode</button>
                  <button class="base64-btn decode" id="decodeBtn">Decode</button>
                  <button class="base64-btn reset" onclick="resetForm()">Reset</button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Result</h2>
               
               <div class="output-code" id="outputCode">
                  // Encoded/decoded result will appear here
               </div>
               
               <div class="base64-actions">
                  <button class="base64-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy Result
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Result copied to clipboard!
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      let currentMode = 'encode';
      
      document.addEventListener('DOMContentLoaded', function() {
         const modeButtons = document.querySelectorAll('.mode-btn');
         const encodeBtn = document.getElementById('encodeBtn');
         const decodeBtn = document.getElementById('decodeBtn');
         
         modeButtons.forEach(button => {
            button.addEventListener('click', function() {
               modeButtons.forEach(btn => btn.classList.remove('active'));
               this.classList.add('active');
               currentMode = this.dataset.mode;
               
               const textarea = document.getElementById('inputText');
               if (currentMode === 'encode') {
                  textarea.placeholder = "Enter text to encode to Base64...";
                  encodeBtn.style.display = 'block';
                  decodeBtn.style.display = 'none';
               } else {
                  textarea.placeholder = "Enter Base64 to decode to text...";
                  encodeBtn.style.display = 'none';
                  decodeBtn.style.display = 'block';
               }
            });
         });
         
         encodeBtn.addEventListener('click', function() {
            encodeToBase64();
         });
         
         decodeBtn.addEventListener('click', function() {
            decodeFromBase64();
         });
         
         // Load sample data
         const sampleBtn = document.createElement('button');
         sampleBtn.type = 'button';
         sampleBtn.className = 'base64-btn reset';
         sampleBtn.style.marginTop = '1rem';
         sampleBtn.style.width = '100%';
         sampleBtn.innerHTML = '<i class="fas fa-magic"></i> Load Sample Text';
         sampleBtn.onclick = loadSampleData;
         
         const actions = document.querySelector('.base64-actions');
         if (actions) {
            actions.parentNode.insertBefore(sampleBtn, actions);
         }
      });
      
      function encodeToBase64() {
         const input = document.getElementById('inputText').value.trim();
         if (!input) {
            alert('Please enter some text to encode.');
            return;
         }
         
         try {
            const encoded = btoa(unescape(encodeURIComponent(input)));
            document.getElementById('outputCode').textContent = encoded;
         } catch (e) {
            alert('Error encoding text. Please check your input.');
            console.error(e);
         }
      }
      
      function decodeFromBase64() {
         const input = document.getElementById('inputText').value.trim();
         if (!input) {
            alert('Please enter Base64 to decode.');
            return;
         }
         
         try {
            const decoded = decodeURIComponent(escape(atob(input)));
            document.getElementById('outputCode').textContent = decoded;
         } catch (e) {
            alert('Error decoding Base64. Please ensure it\'s valid Base64.');
            console.error(e);
         }
      }
      
      function resetForm() {
         document.getElementById('inputText').value = '';
         document.getElementById('outputCode').textContent = '// Encoded/decoded result will appear here';
      }
      
      function copyToClipboard() {
         const outputCode = document.getElementById('outputCode');
         const textToCopy = outputCode.textContent;
         
         if (textToCopy.startsWith('//')) {
            alert('No result to copy.');
            return;
         }
         
         const textarea = document.createElement('textarea');
         textarea.value = textToCopy;
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
      
      function loadSampleData() {
         if (currentMode === 'encode') {
            document.getElementById('inputText').value = 'Hello World! This is a sample text for Base64 encoding.';
         } else {
            document.getElementById('inputText').value = 'SGVsbG8gV29ybGQhIFRoaXMgaXMgYSBzYW1wbGUgdGV4dCBmb3IgQmFzZTY0IGVuY29kaW5nLg==';
         }
      }
   </script>
</body>
</html>