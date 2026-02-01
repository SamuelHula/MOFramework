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
   <title>URL Encoder/Decoder - Code Library</title>
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
      .url-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .url-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .url-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .url-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .url-generator {
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
         min-height: 120px;
         resize: vertical;
      }
      .form-group textarea:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .url-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .url-btn {
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
      .url-btn.encode {
         background: var(--primary);
         color: white;
      }
      .url-btn.encode:hover {
         background: var(--secondary);
      }
      .url-btn.decode {
         background: var(--secondary);
         color: white;
      }
      .url-btn.decode:hover {
         background: var(--primary);
      }
      .url-btn.copy {
         background: #4CAF50;
         color: white;
      }
      .url-btn.copy:hover {
         background: #45a049;
      }
      .url-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .url-btn.reset:hover {
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
         min-height: 120px;
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
      .info-box {
         background: #e8f4f8;
         padding: 1.5rem;
         border-radius: 8px;
         margin-top: 2rem;
         border-left: 4px solid var(--primary);
      }
      .info-box h3 {
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1.2rem;
      }
      .info-box p {
         color: var(--text-color);
         opacity: 0.8;
         line-height: 1.5;
         font-size: 0.95rem;
      }
      @media screen and (max-width: 1200px) {
         .url-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .url-container {
            padding: 2% 1rem 5%;
         }
         .url-header h1 {
            font-size: 2.2rem;
         }
         .url-actions {
            flex-direction: column;
         }
         .url-btn {
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
      <section class="url-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="url-header scroll-effect">
            <h1>URL Encoder/Decoder</h1>
            <p>Encode or decode URLs and special characters</p>
         </div>
         
         <div class="url-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Input</h2>
               
               <div class="form-group">
                  <label for="inputText">Enter URL or text to encode/decode:</label>
                  <textarea id="inputText" placeholder="Enter your URL or text here..."></textarea>
               </div>
               
               <div class="url-actions">
                  <button class="url-btn encode" onclick="encodeURL()">Encode URL</button>
                  <button class="url-btn decode" onclick="decodeURL()">Decode URL</button>
                  <button class="url-btn reset" onclick="resetForm()">Reset</button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Result</h2>
               
               <div class="output-code" id="outputCode">
                  // Encoded/decoded result will appear here
               </div>
               
               <div class="url-actions">
                  <button class="url-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy Result
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Result copied to clipboard!
               </div>
            </div>
            
            <div class="info-box">
               <h3>About URL Encoding</h3>
               <p>URL encoding converts characters into a format that can be transmitted over the internet. Special characters like spaces, quotes, and non-ASCII characters are replaced with "%" followed by two hexadecimal digits. This is essential for creating valid URLs that work across different browsers and servers.</p>
            </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      function encodeURL() {
         const input = document.getElementById('inputText').value.trim();
         if (!input) {
            alert('Please enter some text to encode.');
            return;
         }
         
         try {
            const encoded = encodeURIComponent(input);
            document.getElementById('outputCode').textContent = encoded;
         } catch (e) {
            alert('Error encoding text.');
            console.error(e);
         }
      }
      
      function decodeURL() {
         const input = document.getElementById('inputText').value.trim();
         if (!input) {
            alert('Please enter encoded text to decode.');
            return;
         }
         
         try {
            const decoded = decodeURIComponent(input);
            document.getElementById('outputCode').textContent = decoded;
         } catch (e) {
            alert('Error decoding text. Please ensure it\'s properly encoded.');
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
      
      document.addEventListener('DOMContentLoaded', function() {
         // Load sample data button
         const sampleBtn = document.createElement('button');
         sampleBtn.type = 'button';
         sampleBtn.className = 'url-btn reset';
         sampleBtn.style.marginTop = '1rem';
         sampleBtn.style.width = '100%';
         sampleBtn.innerHTML = '<i class="fas fa-magic"></i> Load Sample URL';
         sampleBtn.onclick = loadSampleData;
         
         const actions = document.querySelector('.url-actions');
         if (actions) {
            actions.parentNode.insertBefore(sampleBtn, actions);
         }
      });
      
      function loadSampleData() {
         document.getElementById('inputText').value = 'https://example.com/search?q=hello world&category=tools';
      }
   </script>
</body>
</html>