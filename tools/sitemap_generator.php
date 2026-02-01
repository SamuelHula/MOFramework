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
   <title>Sitemap Generator - Code Library</title>
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
      .sitemap-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .sitemap-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .sitemap-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .sitemap-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .sitemap-generator {
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
      .url-group {
         background: #f9f9f9;
         padding: 1.5rem;
         border-radius: 8px;
         margin-bottom: 1.5rem;
         border: 1px solid #eee;
         position: relative;
      }
      .url-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1rem;
      }
      .url-header h4 {
         color: var(--text-color);
         font-size: 1.1rem;
      }
      .remove-url {
         background: #ff6b6b;
         color: white;
         border: none;
         padding: 0.3rem 0.8rem;
         border-radius: 4px;
         cursor: pointer;
         font-size: 0.9rem;
      }
      .url-row {
         display: grid;
         grid-template-columns: 2fr 1fr 1fr 1fr;
         gap: 1rem;
         margin-bottom: 1rem;
      }
      .url-row:last-child {
         margin-bottom: 0;
      }
      .add-url-btn {
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
      .add-url-btn:hover {
         background: var(--back-dark);
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
         max-height: 500px;
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
      .sitemap-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .sitemap-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .sitemap-btn.generate {
         background: var(--primary);
         color: white;
      }
      .sitemap-btn.generate:hover {
         background: var(--secondary);
      }
      .sitemap-btn.copy {
         background: var(--secondary);
         color: white;
      }
      .sitemap-btn.copy:hover {
         background: var(--primary);
      }
      .sitemap-btn.download {
         background: #4CAF50;
         color: white;
      }
      .sitemap-btn.download:hover {
         background: #45a049;
      }
      .sitemap-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .sitemap-btn.reset:hover {
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
      @media screen and (max-width: 1200px) {
         .sitemap-container {
            padding: 2% 5% 5%;
         }
         .url-row {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 992px) {
         .sitemap-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .sitemap-container {
            padding: 2% 1rem 5%;
         }
         .sitemap-header h1 {
            font-size: 2.2rem;
         }
         .sitemap-actions {
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
      <section class="sitemap-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="sitemap-header scroll-effect">
            <h1>Sitemap.xml Generator</h1>
            <p>Generate XML sitemaps for search engines</p>
         </div>
         
         <div class="sitemap-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Configuration</h2>
               
               <div class="form-group">
                  <label for="baseUrl">Base URL</label>
                  <input type="url" id="baseUrl" placeholder="https://example.com" value="https://example.com">
                  <small>The root URL of your website</small>
               </div>
               
               <div class="preset-selector">
                  <label>Quick Presets:</label>
                  <div class="preset-buttons">
                     <button type="button" class="preset-btn active" data-preset="website">Basic Website</button>
                     <button type="button" class="preset-btn" data-preset="blog">Blog</button>
                     <button type="button" class="preset-btn" data-preset="ecommerce">E-commerce</button>
                     <button type="button" class="preset-btn" data-preset="custom">Custom</button>
                  </div>
               </div>
               
               <h3 style="margin: 2rem 0 1rem 0; color: var(--text-color);">URL Entries</h3>
               
               <div id="urlsContainer">
                  <!-- URL groups will be added here -->
               </div>
               
               <button type="button" class="add-url-btn" onclick="addUrlGroup()">
                  <i class="fas fa-plus"></i> Add URL Entry
               </button>
               
               <div class="sitemap-actions">
                  <button type="button" class="sitemap-btn generate" onclick="generateSitemap()">
                     <i class="fas fa-code"></i> Generate Sitemap
                  </button>
                  <button type="button" class="sitemap-btn reset" onclick="resetForm()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated Sitemap.xml</h2>
               
               <div class="output-code" id="outputCode">
<!-- Generated sitemap will appear here -->
               </div>
               
               <div class="sitemap-actions">
                  <button type="button" class="sitemap-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy XML
                  </button>
                  <button type="button" class="sitemap-btn download" onclick="downloadSitemap()">
                     <i class="fas fa-download"></i> Download XML
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Sitemap.xml copied to clipboard!
               </div>
               
               <div class="sitemap-description" style="margin-top: 2rem; padding: 1rem; background: #f0f8ff; border-radius: 8px;">
                  <p><strong>How to use:</strong> Place the generated sitemap.xml file in the root directory of your website and submit it to Google Search Console and Bing Webmaster Tools.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
   let urlCount = 0;
   let currentRawXml = '';
   
   document.addEventListener('DOMContentLoaded', function() {
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
      applyPreset('website');
      generateSitemap();
      
      // Add base URL input listener
      document.getElementById('baseUrl').addEventListener('input', generateSitemap);
   });
   
   function addUrlGroup() {
      urlCount++;
      const urlsContainer = document.getElementById('urlsContainer');
      const today = new Date().toISOString().split('T')[0];
      
      const urlHtml = `
         <div class="url-group" id="urlGroup${urlCount}">
            <div class="url-header">
               <h4>URL Entry #${urlCount}</h4>
               <button type="button" class="remove-url" onclick="removeUrlGroup(${urlCount})">Remove</button>
            </div>
            <div class="url-row">
               <div class="form-group">
                  <label>URL Path</label>
                  <input type="text" class="url-path" placeholder="/about" value="/about">
               </div>
               <div class="form-group">
                  <label>Last Modified</label>
                  <input type="date" class="lastmod" value="${today}">
               </div>
               <div class="form-group">
                  <label>Change Frequency</label>
                  <select class="changefreq">
                     <option value="always">always</option>
                     <option value="hourly">hourly</option>
                     <option value="daily" selected>daily</option>
                     <option value="weekly">weekly</option>
                     <option value="monthly">monthly</option>
                     <option value="yearly">yearly</option>
                     <option value="never">never</option>
                  </select>
               </div>
               <div class="form-group">
                  <label>Priority</label>
                  <select class="priority">
                     <option value="1.0">1.0</option>
                     <option value="0.9">0.9</option>
                     <option value="0.8" selected>0.8</option>
                     <option value="0.7">0.7</option>
                     <option value="0.6">0.6</option>
                     <option value="0.5">0.5</option>
                     <option value="0.4">0.4</option>
                     <option value="0.3">0.3</option>
                     <option value="0.2">0.2</option>
                     <option value="0.1">0.1</option>
                  </select>
               </div>
            </div>
         </div>
      `;
      
      urlsContainer.insertAdjacentHTML('beforeend', urlHtml);
      
      // Add event listeners to new inputs
      const newGroup = document.getElementById('urlGroup' + urlCount);
      newGroup.querySelector('.url-path').addEventListener('input', generateSitemap);
      newGroup.querySelector('.lastmod').addEventListener('input', generateSitemap);
      newGroup.querySelector('.changefreq').addEventListener('change', generateSitemap);
      newGroup.querySelector('.priority').addEventListener('change', generateSitemap);
      
      generateSitemap();
   }
   
   function removeUrlGroup(id) {
      const urlGroup = document.getElementById('urlGroup' + id);
      if (urlGroup) {
         urlGroup.remove();
         generateSitemap();
      }
   }
   
   function applyPreset(preset) {
      const urlsContainer = document.getElementById('urlsContainer');
      const baseUrlInput = document.getElementById('baseUrl');
      urlsContainer.innerHTML = '';
      urlCount = 0;
      
      switch(preset) {
         case 'website':
            baseUrlInput.value = 'https://example.com';
            // Add 3 default URL entries
            setTimeout(() => {
               addUrlGroup();
               const urlGroup1 = document.getElementById('urlGroup1');
               if (urlGroup1) {
                  urlGroup1.querySelector('.url-path').value = '/';
                  urlGroup1.querySelector('.priority').value = '1.0';
               }
               
               addUrlGroup();
               const urlGroup2 = document.getElementById('urlGroup2');
               if (urlGroup2) {
                  urlGroup2.querySelector('.url-path').value = '/about';
                  urlGroup2.querySelector('.changefreq').value = 'weekly';
               }
               
               addUrlGroup();
               const urlGroup3 = document.getElementById('urlGroup3');
               if (urlGroup3) {
                  urlGroup3.querySelector('.url-path').value = '/contact';
                  urlGroup3.querySelector('.changefreq').value = 'monthly';
                  urlGroup3.querySelector('.priority').value = '0.7';
               }
            }, 100);
            break;
            
         case 'blog':
            baseUrlInput.value = 'https://example.com';
            setTimeout(() => {
               addUrlGroup();
               const urlGroup1 = document.getElementById('urlGroup1');
               if (urlGroup1) {
                  urlGroup1.querySelector('.url-path').value = '/';
                  urlGroup1.querySelector('.priority').value = '1.0';
               }
               
               addUrlGroup();
               const urlGroup2 = document.getElementById('urlGroup2');
               if (urlGroup2) {
                  urlGroup2.querySelector('.url-path').value = '/blog';
                  urlGroup2.querySelector('.changefreq').value = 'hourly';
                  urlGroup2.querySelector('.priority').value = '0.9';
               }
               
               addUrlGroup();
               const urlGroup3 = document.getElementById('urlGroup3');
               if (urlGroup3) {
                  urlGroup3.querySelector('.url-path').value = '/categories';
                  urlGroup3.querySelector('.changefreq').value = 'daily';
                  urlGroup3.querySelector('.priority').value = '0.8';
               }
            }, 100);
            break;
            
         case 'ecommerce':
            baseUrlInput.value = 'https://example.com';
            setTimeout(() => {
               addUrlGroup();
               const urlGroup1 = document.getElementById('urlGroup1');
               if (urlGroup1) {
                  urlGroup1.querySelector('.url-path').value = '/';
                  urlGroup1.querySelector('.changefreq').value = 'hourly';
                  urlGroup1.querySelector('.priority').value = '1.0';
               }
               
               addUrlGroup();
               const urlGroup2 = document.getElementById('urlGroup2');
               if (urlGroup2) {
                  urlGroup2.querySelector('.url-path').value = '/products';
                  urlGroup2.querySelector('.changefreq').value = 'hourly';
                  urlGroup2.querySelector('.priority').value = '0.9';
               }
               
               addUrlGroup();
               const urlGroup3 = document.getElementById('urlGroup3');
               if (urlGroup3) {
                  urlGroup3.querySelector('.url-path').value = '/categories';
                  urlGroup3.querySelector('.changefreq').value = 'daily';
                  urlGroup3.querySelector('.priority').value = '0.8';
               }
            }, 100);
            break;
            
         case 'custom':
            baseUrlInput.value = 'https://example.com';
            setTimeout(() => {
               addUrlGroup();
            }, 100);
            break;
      }
   }
   
   function generateSitemap() {
      const baseUrl = document.getElementById('baseUrl').value.trim();
      if (!baseUrl) {
         document.getElementById('outputCode').textContent = 'Please enter a valid base URL.';
         return;
      }
      
      const cleanBaseUrl = baseUrl.replace(/\/+$/, '');
      const urlGroups = document.querySelectorAll('.url-group');
      
      // FIX: Break up the XML declaration to avoid PHP parsing
      let xml = '<' + '?xml version="1.0" encoding="UTF-8"?>\n';
      xml += '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">\n\n';
      xml += '  <!-- Generated by Code Library Sitemap Generator -->\n';
      xml += '  <!-- Created: ' + new Date().toISOString().split('T')[0] + ' -->\n\n';
      
      urlGroups.forEach(group => {
         const urlPath = group.querySelector('.url-path').value.trim();
         const lastmod = group.querySelector('.lastmod').value;
         const changefreq = group.querySelector('.changefreq').value;
         const priority = group.querySelector('.priority').value;
         
         if (urlPath) {
            const fullUrl = cleanBaseUrl + urlPath;
            xml += '  <url>\n';
            xml += '    <loc>' + escapeXml(fullUrl) + '</loc>\n';
            if (lastmod) {
               xml += '    <lastmod>' + lastmod + '</lastmod>\n';
            }
            if (changefreq) {
               xml += '    <changefreq>' + changefreq + '</changefreq>\n';
            }
            if (priority) {
               xml += '    <priority>' + priority + '</priority>\n';
            }
            xml += '  </url>\n\n';
         }
      });
      
      xml += '</urlset>';
      
      currentRawXml = xml;
      
      // Display with basic highlighting
      document.getElementById('outputCode').innerHTML = xml
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;');
   }
   
   function escapeXml(text) {
      return text
         .replace(/&/g, '&amp;')
         .replace(/</g, '&lt;')
         .replace(/>/g, '&gt;')
         .replace(/"/g, '&quot;')
         .replace(/'/g, '&#039;');
   }
   
   function resetForm() {
      const presetButtons = document.querySelectorAll('.preset-btn');
      presetButtons.forEach(b => b.classList.remove('active'));
      presetButtons[0].classList.add('active');
      
      applyPreset('website');
   }
   
   function copyToClipboard() {
      if (currentRawXml) {
         const textarea = document.createElement('textarea');
         textarea.value = currentRawXml;
         document.body.appendChild(textarea);
         textarea.select();
         
         try {
            document.execCommand('copy');
            const successMsg = document.getElementById('copySuccess');
            successMsg.style.display = 'block';
            
            setTimeout(() => {
               successMsg.style.display = 'none';
            }, 3000);
         } catch (err) {
            console.error('Failed to copy:', err);
            alert('Failed to copy to clipboard. Please copy manually.');
         }
         
         document.body.removeChild(textarea);
      }
   }
   
   function downloadSitemap() {
      if (currentRawXml) {
         try {
            const blob = new Blob([currentRawXml], { type: 'application/xml;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'sitemap.xml';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
         } catch (err) {
            console.error('Failed to download:', err);
            alert('Failed to download file. Please try again.');
         }
      }
   }
</script>
</body>
</html>