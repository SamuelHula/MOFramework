<?php
require_once './assets/config.php';

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
   <title>Robots.txt Generator - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
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
      .robots-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .robots-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .robots-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .robots-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .robots-generator {
         display: grid;
         grid-template-columns: 1fr 1fr;
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
      .form-group input, .form-group select, .form-group textarea {
         width: 100%;
         padding: 0.8rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: var(--back-light);
         font-family: var(--text_font);
      }
      .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .form-group small {
         display: block;
         color: #666;
         font-size: 0.85rem;
         margin-top: 0.3rem;
      }
      .rule-group {
         background: #f9f9f9;
         padding: 1.5rem;
         border-radius: 8px;
         margin-bottom: 1.5rem;
         border: 1px solid #eee;
      }
      .rule-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1rem;
      }
      .rule-header h4 {
         color: var(--text-color);
         font-size: 1.1rem;
      }
      .remove-rule {
         background: #ff6b6b;
         color: white;
         border: none;
         padding: 0.3rem 0.8rem;
         border-radius: 4px;
         cursor: pointer;
         font-size: 0.9rem;
      }
      .add-rule-btn {
         display: flex;
         align-items: center;
         gap: 0.5rem;
         background: var(--back-light);
         border: 2px dashed var(--back-dark);
         padding: 1rem;
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s;
         width: 100%;
         font-size: 1rem;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 1.5rem;
      }
      .add-rule-btn:hover {
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
         max-height: 400px;
         overflow-y: auto;
         margin-bottom: 1rem;
      }
      .output-code::-webkit-scrollbar {
         width: 8px;
      }
      .output-code::-webkit-scrollbar-track {
         background: #2d2d2d;
      }
      .output-code::-webkit-scrollbar-thumb {
         background: #555;
         border-radius: 4px;
      }
      .robots-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .robots-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .robots-btn.generate {
         background: var(--primary);
         color: white;
      }
      .robots-btn.generate:hover {
         background: var(--secondary);
      }
      .robots-btn.copy {
         background: var(--secondary);
         color: white;
      }
      .robots-btn.copy:hover {
         background: var(--primary);
      }
      .robots-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .robots-btn.reset:hover {
         background: var(--back-dark);
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
      .preset-selector {
         margin-bottom: 2rem;
      }
      .preset-buttons {
         display: flex;
         gap: 1rem;
         margin-top: 1rem;
         flex-wrap: wrap;
      }
      .preset-btn {
         padding: 0.8rem 1.5rem;
         background: var(--back-light);
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s;
         font-weight: 600;
         font-size: 0.9rem;
      }
      .preset-btn:hover {
         background: var(--back-dark);
      }
      .preset-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      @media screen and (max-width: 1200px) {
         .robots-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 992px) {
         .robots-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .robots-container {
            padding: 2% 1rem 5%;
         }
         .robots-header h1 {
            font-size: 2.2rem;
         }
         .robots-actions {
            flex-direction: column;
         }
         .preset-buttons {
            flex-direction: column;
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
      <section class="robots-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="robots-header scroll-effect">
            <h1>Robots.txt Generator</h1>
            <p>Generate robots.txt files to control search engine crawlers</p>
         </div>
         
         <div class="robots-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Configuration</h2>
               
               <div class="preset-selector">
                  <label>Quick Presets:</label>
                  <div class="preset-buttons">
                     <button class="preset-btn active" data-preset="allow-all">Allow All</button>
                     <button class="preset-btn" data-preset="disallow-all">Disallow All</button>
                     <button class="preset-btn" data-preset="wordpress">WordPress</button>
                     <button class="preset-btn" data-preset="shopify">Shopify</button>
                     <button class="preset-btn" data-preset="custom">Custom</button>
                  </div>
               </div>
               
               <div class="form-group">
                  <label for="sitemapUrl">Sitemap URL (optional)</label>
                  <input type="url" id="sitemapUrl" placeholder="https://example.com/sitemap.xml">
                  <small>Add your sitemap location for search engines</small>
               </div>
               
               <div class="form-group">
                  <label for="crawlDelay">Crawl Delay (seconds)</label>
                  <input type="number" id="crawlDelay" min="0" max="10" step="0.1" placeholder="1.0">
                  <small>Time in seconds to wait between requests (0-10)</small>
               </div>
               
               <h3 style="margin: 2rem 0 1rem 0; color: var(--text-color);">User Agent Rules</h3>
               
               <div id="rulesContainer">
                  <div class="rule-group">
                     <div class="rule-header">
                        <h4>Rule #1</h4>
                        <button class="remove-rule" onclick="removeRule(this)">Remove</button>
                     </div>
                     <div class="form-group">
                        <label for="userAgent1">User Agent</label>
                        <select id="userAgent1" class="user-agent-select">
                           <option value="*">All User Agents (*)</option>
                           <option value="Googlebot">Googlebot</option>
                           <option value="Googlebot-Image">Googlebot-Image</option>
                           <option value="Googlebot-News">Googlebot-News</option>
                           <option value="Googlebot-Video">Googlebot-Video</option>
                           <option value="Bingbot">Bingbot</option>
                           <option value="Slurp">Slurp (Yahoo)</option>
                           <option value="DuckDuckBot">DuckDuckBot</option>
                           <option value="Baiduspider">Baiduspider</option>
                           <option value="YandexBot">YandexBot</option>
                           <option value="custom">Custom User Agent</option>
                        </select>
                     </div>
                     <div class="form-group custom-agent-group" style="display: none;">
                        <label for="customAgent1">Custom User Agent</label>
                        <input type="text" id="customAgent1" placeholder="Enter custom user agent">
                     </div>
                     <div class="form-group">
                        <label>Rules for this User Agent</label>
                        <div class="rules-list">
                           <div class="rule-line">
                              <select class="rule-type">
                                 <option value="Allow">Allow</option>
                                 <option value="Disallow">Disallow</option>
                              </select>
                              <input type="text" class="rule-path" placeholder="/path/">
                              <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                           </div>
                        </div>
                        <button class="add-line-btn" onclick="addRuleLine(this)" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--back-light); border: 1px solid var(--back-dark); border-radius: 4px; cursor: pointer;">Add Rule Line</button>
                     </div>
                  </div>
               </div>
               
               <button class="add-rule-btn" onclick="addRule()">
                  <i class="fas fa-plus"></i> Add User Agent Rule
               </button>
               
               <div class="robots-actions">
                  <button class="robots-btn generate" onclick="generateRobots()">
                     <i class="fas fa-code"></i> Generate Robots.txt
                  </button>
                  <button class="robots-btn reset" onclick="resetForm()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated Robots.txt</h2>
               
               <div class="output-code" id="outputCode">
# Generated robots.txt will appear here
# User-agent: *
# Disallow:
               </div>
               
               <div class="robots-actions">
                  <button class="robots-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy Robots.txt
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Robots.txt copied to clipboard!
               </div>
               
               <div class="robots-description" style="margin-top: 2rem; padding: 1rem; background: #f0f8ff; border-radius: 8px;">
                  <p><strong>How to use:</strong> Place the generated robots.txt file in the root directory of your website (e.g., https://example.com/robots.txt)</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      let ruleCount = 1;
      
      document.addEventListener('DOMContentLoaded', function() {
         // User agent select change
         document.addEventListener('change', function(e) {
            if (e.target.classList.contains('user-agent-select')) {
               const parent = e.target.closest('.rule-group');
               const customGroup = parent.querySelector('.custom-agent-group');
               if (e.target.value === 'custom') {
                  customGroup.style.display = 'block';
               } else {
                  customGroup.style.display = 'none';
               }
            }
         });
         
         // Preset buttons
         const presetButtons = document.querySelectorAll('.preset-btn');
         presetButtons.forEach(btn => {
            btn.addEventListener('click', function() {
               presetButtons.forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               applyPreset(this.getAttribute('data-preset'));
            });
         });
         
         // Apply default preset
         applyPreset('allow-all');
      });
      
      function addRule() {
         ruleCount++;
         const rulesContainer = document.getElementById('rulesContainer');
         
         const ruleHtml = `
            <div class="rule-group">
               <div class="rule-header">
                  <h4>Rule #${ruleCount}</h4>
                  <button class="remove-rule" onclick="removeRule(this)">Remove</button>
               </div>
               <div class="form-group">
                  <label for="userAgent${ruleCount}">User Agent</label>
                  <select id="userAgent${ruleCount}" class="user-agent-select">
                     <option value="*">All User Agents (*)</option>
                     <option value="Googlebot">Googlebot</option>
                     <option value="Googlebot-Image">Googlebot-Image</option>
                     <option value="Googlebot-News">Googlebot-News</option>
                     <option value="Googlebot-Video">Googlebot-Video</option>
                     <option value="Bingbot">Bingbot</option>
                     <option value="Slurp">Slurp (Yahoo)</option>
                     <option value="DuckDuckBot">DuckDuckBot</option>
                     <option value="Baiduspider">Baiduspider</option>
                     <option value="YandexBot">YandexBot</option>
                     <option value="custom">Custom User Agent</option>
                  </select>
               </div>
               <div class="form-group custom-agent-group" style="display: none;">
                  <label for="customAgent${ruleCount}">Custom User Agent</label>
                  <input type="text" id="customAgent${ruleCount}" placeholder="Enter custom user agent">
               </div>
               <div class="form-group">
                  <label>Rules for this User Agent</label>
                  <div class="rules-list">
                     <div class="rule-line">
                        <select class="rule-type">
                           <option value="Allow">Allow</option>
                           <option value="Disallow">Disallow</option>
                        </select>
                        <input type="text" class="rule-path" placeholder="/path/">
                        <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                     </div>
                  </div>
                  <button class="add-line-btn" onclick="addRuleLine(this)" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--back-light); border: 1px solid var(--back-dark); border-radius: 4px; cursor: pointer;">Add Rule Line</button>
               </div>
            </div>
         `;
         
         rulesContainer.insertAdjacentHTML('beforeend', ruleHtml);
      }
      
      function removeRule(button) {
         const ruleGroup = button.closest('.rule-group');
         if (document.querySelectorAll('.rule-group').length > 1) {
            ruleGroup.remove();
            // Update rule numbers
            updateRuleNumbers();
         } else {
            alert('You must have at least one user agent rule.');
         }
      }
      
      function updateRuleNumbers() {
         const ruleGroups = document.querySelectorAll('.rule-group');
         ruleGroups.forEach((group, index) => {
            group.querySelector('h4').textContent = `Rule #${index + 1}`;
         });
      }
      
      function addRuleLine(button) {
         const rulesList = button.previousElementSibling;
         const ruleLineHtml = `
            <div class="rule-line" style="display: flex; gap: 0.5rem; margin-bottom: 0.5rem; align-items: center;">
               <select class="rule-type" style="flex: 1; padding: 0.5rem; border: 1px solid var(--back-dark); border-radius: 4px;">
                  <option value="Allow">Allow</option>
                  <option value="Disallow">Disallow</option>
               </select>
               <input type="text" class="rule-path" placeholder="/path/" style="flex: 3; padding: 0.5rem; border: 1px solid var(--back-dark); border-radius: 4px;">
               <button class="remove-line" onclick="removeRuleLine(this)" style="padding: 0.5rem 1rem; background: #ff6b6b; color: white; border: none; border-radius: 4px; cursor: pointer;">×</button>
            </div>
         `;
         rulesList.insertAdjacentHTML('beforeend', ruleLineHtml);
      }
      
      function removeRuleLine(button) {
         const ruleLine = button.closest('.rule-line');
         const ruleLines = ruleLine.parentElement.querySelectorAll('.rule-line');
         if (ruleLines.length > 1) {
            ruleLine.remove();
         } else {
            alert('You must have at least one rule line.');
         }
      }
      
      function applyPreset(preset) {
         const rulesContainer = document.getElementById('rulesContainer');
         
         switch(preset) {
            case 'allow-all':
               rulesContainer.innerHTML = `
                  <div class="rule-group">
                     <div class="rule-header">
                        <h4>Rule #1</h4>
                        <button class="remove-rule" onclick="removeRule(this)">Remove</button>
                     </div>
                     <div class="form-group">
                        <label for="userAgent1">User Agent</label>
                        <select id="userAgent1" class="user-agent-select">
                           <option value="*" selected>All User Agents (*)</option>
                           <option value="Googlebot">Googlebot</option>
                           <option value="custom">Custom User Agent</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Rules for this User Agent</label>
                        <div class="rules-list">
                           <div class="rule-line">
                              <select class="rule-type">
                                 <option value="Allow" selected>Allow</option>
                                 <option value="Disallow">Disallow</option>
                              </select>
                              <input type="text" class="rule-path" placeholder="/" value="/">
                              <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                           </div>
                        </div>
                     </div>
                  </div>
               `;
               document.getElementById('sitemapUrl').value = '';
               document.getElementById('crawlDelay').value = '';
               break;
               
            case 'disallow-all':
               rulesContainer.innerHTML = `
                  <div class="rule-group">
                     <div class="rule-header">
                        <h4>Rule #1</h4>
                        <button class="remove-rule" onclick="removeRule(this)">Remove</button>
                     </div>
                     <div class="form-group">
                        <label for="userAgent1">User Agent</label>
                        <select id="userAgent1" class="user-agent-select">
                           <option value="*" selected>All User Agents (*)</option>
                           <option value="Googlebot">Googlebot</option>
                           <option value="custom">Custom User Agent</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Rules for this User Agent</label>
                        <div class="rules-list">
                           <div class="rule-line">
                              <select class="rule-type">
                                 <option value="Allow">Allow</option>
                                 <option value="Disallow" selected>Disallow</option>
                              </select>
                              <input type="text" class="rule-path" placeholder="/" value="/">
                              <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                           </div>
                        </div>
                     </div>
                  </div>
               `;
               document.getElementById('sitemapUrl').value = '';
               document.getElementById('crawlDelay').value = '';
               break;
               
            case 'wordpress':
               rulesContainer.innerHTML = `
                  <div class="rule-group">
                     <div class="rule-header">
                        <h4>Rule #1</h4>
                        <button class="remove-rule" onclick="removeRule(this)">Remove</button>
                     </div>
                     <div class="form-group">
                        <label for="userAgent1">User Agent</label>
                        <select id="userAgent1" class="user-agent-select">
                           <option value="*" selected>All User Agents (*)</option>
                        </select>
                     </div>
                     <div class="form-group">
                        <label>Rules for this User Agent</label>
                        <div class="rules-list">
                           <div class="rule-line">
                              <select class="rule-type">
                                 <option value="Allow">Allow</option>
                                 <option value="Disallow" selected>Disallow</option>
                              </select>
                              <input type="text" class="rule-path" placeholder="/" value="/wp-admin/">
                              <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                           </div>
                           <div class="rule-line">
                              <select class="rule-type">
                                 <option value="Allow">Allow</option>
                                 <option value="Disallow" selected>Disallow</option>
                              </select>
                              <input type="text" class="rule-path" placeholder="/" value="/wp-includes/">
                              <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                           </div>
                           <div class="rule-line">
                              <select class="rule-type">
                                 <option value="Allow" selected>Allow</option>
                                 <option value="Disallow">Disallow</option>
                              </select>
                              <input type="text" class="rule-path" placeholder="/" value="/wp-content/uploads/">
                              <button class="remove-line" onclick="removeRuleLine(this)">×</button>
                           </div>
                        </div>
                        <button class="add-line-btn" onclick="addRuleLine(this)" style="margin-top: 0.5rem; padding: 0.5rem 1rem; background: var(--back-light); border: 1px solid var(--back-dark); border-radius: 4px; cursor: pointer;">Add Rule Line</button>
                     </div>
                  </div>
               `;
               document.getElementById('sitemapUrl').value = 'https://example.com/sitemap_index.xml';
               document.getElementById('crawlDelay').value = '1.0';
               break;
               
            case 'custom':
               // Keep current configuration
               break;
         }
         
         ruleCount = document.querySelectorAll('.rule-group').length;
      }
      
      function generateRobots() {
         let robotsContent = '# Generated by Code Library Robots.txt Generator\n';
         robotsContent += '# Created: ' + new Date().toISOString().split('T')[0] + '\n\n';
         
         // Add sitemap if provided
         const sitemapUrl = document.getElementById('sitemapUrl').value;
         if (sitemapUrl) {
            robotsContent += `Sitemap: ${sitemapUrl}\n\n`;
         }
         
         // Add crawl delay if provided
         const crawlDelay = document.getElementById('crawlDelay').value;
         if (crawlDelay) {
            robotsContent += `# Crawl-delay: ${crawlDelay}\n\n`;
         }
         
         // Add user agent rules
         const ruleGroups = document.querySelectorAll('.rule-group');
         ruleGroups.forEach((group, index) => {
            const userAgentSelect = group.querySelector('.user-agent-select');
            let userAgent = userAgentSelect.value;
            
            if (userAgent === 'custom') {
               const customAgent = group.querySelector('input[type="text"]').value;
               userAgent = customAgent || '*';
            }
            
            robotsContent += `User-agent: ${userAgent}\n`;
            
            const ruleLines = group.querySelectorAll('.rule-line');
            ruleLines.forEach(line => {
               const ruleType = line.querySelector('.rule-type').value;
               const rulePath = line.querySelector('.rule-path').value;
               if (rulePath) {
                  robotsContent += `${ruleType}: ${rulePath}\n`;
               }
            });
            
            robotsContent += '\n';
         });
         
         document.getElementById('outputCode').textContent = robotsContent;
      }
      
      function resetForm() {
         const presetButtons = document.querySelectorAll('.preset-btn');
         presetButtons.forEach(b => b.classList.remove('active'));
         presetButtons[0].classList.add('active');
         
         applyPreset('allow-all');
         generateRobots();
      }
      
      function copyToClipboard() {
         const outputCode = document.getElementById('outputCode');
         const textToCopy = outputCode.textContent;
         
         navigator.clipboard.writeText(textToCopy).then(() => {
            const successMsg = document.getElementById('copySuccess');
            successMsg.style.display = 'block';
            
            setTimeout(() => {
               successMsg.style.display = 'none';
            }, 3000);
         });
      }
   </script>
</body>
</html>