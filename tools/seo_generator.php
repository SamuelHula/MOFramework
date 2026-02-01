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
   <title>SEO Meta Tags Generator - Code Library</title>
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
      .seo-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .seo-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .seo-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .seo-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .seo-generator {
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
      .form-group input, .form-group textarea, .form-group select {
         width: 100%;
         padding: 0.8rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: var(--back-light);
         font-family: var(--text_font);
      }
      .form-group input:focus, .form-group textarea:focus, .form-group select:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .form-group textarea {
         min-height: 120px;
         resize: vertical;
      }
      .form-group small {
         display: block;
         color: #666;
         font-size: 0.85rem;
         margin-top: 0.3rem;
      }
      .seo-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .seo-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .seo-btn.generate {
         background: var(--primary);
         color: white;
      }
      .seo-btn.generate:hover {
         background: var(--secondary);
      }
      .seo-btn.copy {
         background: var(--secondary);
         color: white;
      }
      .seo-btn.copy:hover {
         background: var(--primary);
      }
      .seo-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .seo-btn.reset:hover {
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
      .output-code::-webkit-scrollbar-thumb:hover {
         background: #666;
      }
      .code-tag {
         color: #569cd6;
      }
      .code-attr {
         color: #9cdcfe;
      }
      .code-value {
         color: #ce9178;
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
      .seo-preview {
         background: white;
         padding: 1.5rem;
         border-radius: 10px;
         border: 1px solid var(--back-dark);
         margin-bottom: 2rem;
      }
      .preview-title {
         font-size: 1.3rem;
         color: #1a0dab;
         margin-bottom: 0.5rem;
         text-decoration: none;
         display: block;
      }
      .preview-title:hover {
         text-decoration: underline;
      }
      .preview-url {
         color: #006621;
         font-size: 0.9rem;
         margin-bottom: 0.5rem;
      }
      .preview-desc {
         color: #545454;
         line-height: 1.4;
      }
      @media screen and (max-width: 1200px) {
         .seo-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 992px) {
         .seo-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .seo-container {
            padding: 2% 1rem 5%;
         }
         .seo-header h1 {
            font-size: 2.2rem;
         }
         .seo-actions {
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
      <?php include '../assets/nav_bar.php' ?>
   </header>
   
   <main id="main">
      <section class="seo-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="seo-header scroll-effect">
            <h1>SEO Meta Tags Generator</h1>
            <p>Generate comprehensive meta tags for better SEO and social media sharing</p>
         </div>
         
         <div class="seo-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Input Details</h2>
               
               <div class="form-group">
                  <label for="pageTitle">Page Title *</label>
                  <input type="text" id="pageTitle" placeholder="e.g., My Awesome Web Page">
                  <small>Optimally 50-60 characters</small>
               </div>
               
               <div class="form-group">
                  <label for="pageDescription">Meta Description *</label>
                  <textarea id="pageDescription" placeholder="A brief description of your page content"></textarea>
                  <small>Optimally 150-160 characters</small>
               </div>
               
               <div class="form-group">
                  <label for="keywords">Keywords</label>
                  <input type="text" id="keywords" placeholder="e.g., web development, code snippets, programming">
                  <small>Comma separated keywords</small>
               </div>
               
               <div class="form-group">
                  <label for="canonicalUrl">Canonical URL *</label>
                  <input type="url" id="canonicalUrl" placeholder="https://example.com/page-url">
               </div>
               
               <div class="form-group">
                  <label for="ogImage">Open Graph Image URL</label>
                  <input type="url" id="ogImage" placeholder="https://example.com/image.jpg">
                  <small>Recommended size: 1200x630 pixels</small>
               </div>
               
               <div class="form-group">
                  <label for="ogType">Open Graph Type</label>
                  <select id="ogType">
                     <option value="website">Website</option>
                     <option value="article">Article</option>
                     <option value="blog">Blog</option>
                     <option value="product">Product</option>
                     <option value="profile">Profile</option>
                  </select>
               </div>
               
               <div class="form-group">
                  <label for="ogSiteName">Site Name</label>
                  <input type="text" id="ogSiteName" placeholder="Your Site Name">
               </div>
               
               <div class="form-group">
                  <label for="twitterSite">Twitter Site Handle</label>
                  <input type="text" id="twitterSite" placeholder="@username">
               </div>
               
               <div class="form-group">
                  <label for="twitterCreator">Twitter Creator Handle</label>
                  <input type="text" id="twitterCreator" placeholder="@username">
               </div>
               
               <div class="form-group">
                  <label for="twitterCard">Twitter Card Type</label>
                  <select id="twitterCard">
                     <option value="summary">Summary</option>
                     <option value="summary_large_image">Summary Large Image</option>
                     <option value="app">App</option>
                     <option value="player">Player</option>
                  </select>
               </div>
               
               <div class="seo-actions">
                  <button class="seo-btn generate" onclick="generateSEOTags()">
                     <i class="fas fa-code"></i> Generate Tags
                  </button>
                  <button class="seo-btn reset" onclick="resetForm()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated Meta Tags</h2>
               
               <div class="seo-preview" id="seoPreview" style="display: none;">
                  <a href="#" class="preview-title" id="previewTitle"></a>
                  <div class="preview-url" id="previewUrl"></div>
                  <div class="preview-desc" id="previewDesc"></div>
               </div>
               
               <div class="output-code" id="outputCode">
                  // Generated meta tags will appear here
               </div>
               
               <div class="seo-actions">
                  <button class="seo-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy Code
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Code copied to clipboard!
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      function escapeHTML(text) {
         return text.replace(/[&<>"']/g, function(m) {
            return {
               '&': '&amp;',
               '<': '&lt;',
               '>': '&gt;',
               '"': '&quot;',
               "'": '&#039;'
            }[m];
         });
      }
      
      function generateSEOTags() {
         const title = document.getElementById('pageTitle').value.trim();
         const description = document.getElementById('pageDescription').value.trim();
         const keywords = document.getElementById('keywords').value.trim();
         const canonicalUrl = document.getElementById('canonicalUrl').value.trim();
         const ogImage = document.getElementById('ogImage').value.trim();
         const ogType = document.getElementById('ogType').value;
         const ogSiteName = document.getElementById('ogSiteName').value.trim();
         const twitterSite = document.getElementById('twitterSite').value.trim();
         const twitterCreator = document.getElementById('twitterCreator').value.trim();
         const twitterCard = document.getElementById('twitterCard').value;
         
         if (!title || !description || !canonicalUrl) {
            alert('Please fill in all required fields (Page Title, Meta Description, and Canonical URL)');
            return;
         }
         
         let metaTags = '';
         
         metaTags += `<span class="code-tag">&lt;title&gt;</span>${escapeHTML(title)}<span class="code-tag">&lt;/title&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"description"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(description)}"</span> <span class="code-tag">/&gt;</span>\n`;
         
         if (keywords) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"keywords"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(keywords)}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         metaTags += `<span class="code-tag">&lt;link</span> <span class="code-attr">rel=</span><span class="code-value">"canonical"</span> <span class="code-attr">href=</span><span class="code-value">"${escapeHTML(canonicalUrl)}"</span> <span class="code-tag">/&gt;</span>\n`;
         
         metaTags += `<span class="code-tag">&lt;!-- Open Graph --&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">property=</span><span class="code-value">"og:title"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(title)}"</span> <span class="code-tag">/&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">property=</span><span class="code-value">"og:description"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(description)}"</span> <span class="code-tag">/&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">property=</span><span class="code-value">"og:url"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(canonicalUrl)}"</span> <span class="code-tag">/&gt;</span>\n`;
         
         if (ogType) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">property=</span><span class="code-value">"og:type"</span> <span class="code-attr">content=</span><span class="code-value">"${ogType}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         if (ogImage) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">property=</span><span class="code-value">"og:image"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(ogImage)}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         if (ogSiteName) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">property=</span><span class="code-value">"og:site_name"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(ogSiteName)}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         metaTags += `<span class="code-tag">&lt;!-- Twitter Card --&gt;</span>\n`;
         if (twitterCard) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"twitter:card"</span> <span class="code-attr">content=</span><span class="code-value">"${twitterCard}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"twitter:title"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(title)}"</span> <span class="code-tag">/&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"twitter:description"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(description)}"</span> <span class="code-tag">/&gt;</span>\n`;
         
         if (ogImage) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"twitter:image"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(ogImage)}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         if (twitterSite) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"twitter:site"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(twitterSite)}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         if (twitterCreator) {
            metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"twitter:creator"</span> <span class="code-attr">content=</span><span class="code-value">"${escapeHTML(twitterCreator)}"</span> <span class="code-tag">/&gt;</span>\n`;
         }
         
         metaTags += `<span class="code-tag">&lt;!-- Additional Meta Tags --&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"robots"</span> <span class="code-attr">content=</span><span class="code-value">"index, follow"</span> <span class="code-tag">/&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">charset=</span><span class="code-value">"UTF-8"</span> <span class="code-tag">/&gt;</span>\n`;
         metaTags += `<span class="code-tag">&lt;meta</span> <span class="code-attr">name=</span><span class="code-value">"viewport"</span> <span class="code-attr">content=</span><span class="code-value">"width=device-width, initial-scale=1.0"</span> <span class="code-tag">/&gt;</span>`;
         
         document.getElementById('outputCode').innerHTML = metaTags;
         
         updateSEOPreview(title, canonicalUrl, description);
      }
      
      function updateSEOPreview(title, url, description) {
         const preview = document.getElementById('seoPreview');
         preview.style.display = 'block';
         
         document.getElementById('previewTitle').textContent = title;
         document.getElementById('previewTitle').href = url;
         document.getElementById('previewUrl').textContent = url.replace(/^https?:\/\//, '');
         document.getElementById('previewDesc').textContent = description;
      }
      
      function resetForm() {
         document.getElementById('pageTitle').value = '';
         document.getElementById('pageDescription').value = '';
         document.getElementById('keywords').value = '';
         document.getElementById('canonicalUrl').value = '';
         document.getElementById('ogImage').value = '';
         document.getElementById('ogType').value = 'website';
         document.getElementById('ogSiteName').value = '';
         document.getElementById('twitterSite').value = '';
         document.getElementById('twitterCreator').value = '';
         document.getElementById('twitterCard').value = 'summary';
         document.getElementById('outputCode').innerHTML = '// Generated meta tags will appear here';
         document.getElementById('seoPreview').style.display = 'none';
      }
      
      function copyToClipboard() {
         const outputCode = document.getElementById('outputCode');
         const textToCopy = outputCode.textContent;
         
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
         const formGroup = document.querySelector('.input-section .form-group:first-child');
         if (formGroup) {
            const sampleBtn = document.createElement('button');
            sampleBtn.type = 'button';
            sampleBtn.className = 'seo-btn generate';
            sampleBtn.style.marginTop = '1rem';
            sampleBtn.style.padding = '0.5rem 1rem';
            sampleBtn.style.fontSize = '0.9rem';
            sampleBtn.innerHTML = '<i class="fas fa-magic"></i> Load Sample Data';
            sampleBtn.onclick = loadSampleData;
            
            const seoActions = document.querySelector('.seo-actions');
            if (seoActions) {
               seoActions.parentNode.insertBefore(sampleBtn, seoActions);
            }
         }
      });
      
      function loadSampleData() {
         document.getElementById('pageTitle').value = 'Code Library - Ultimate Web Development Resource Hub';
         document.getElementById('pageDescription').value = 'Discover thousands of code snippets, web development tools, and programming resources. Boost your coding skills with our comprehensive library.';
         document.getElementById('keywords').value = 'code snippets, web development, programming, HTML, CSS, JavaScript, PHP, tutorials';
         document.getElementById('canonicalUrl').value = 'https://codelibrary.example.com';
         document.getElementById('ogImage').value = 'https://codelibrary.example.com/og-image.jpg';
         document.getElementById('ogSiteName').value = 'Code Library';
         document.getElementById('twitterSite').value = '@CodeLibrary';
         document.getElementById('twitterCreator').value = '@CodeLibrary';
      }
   </script>
</body>
</html>