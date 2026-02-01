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
   <title>Schema Markup Generator - Code Library</title>
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
      .schema-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .schema-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .schema-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .schema-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .schema-generator {
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
      .schema-type-selector {
         margin-bottom: 2rem;
      }
      .schema-type-grid {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 1rem;
         margin-top: 1rem;
      }
      .schema-type-btn {
         padding: 1rem;
         background: var(--back-light);
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         text-align: center;
         cursor: pointer;
         transition: all 0.3s;
         font-weight: 600;
         font-size: 0.9rem;
      }
      .schema-type-btn:hover {
         background: var(--back-dark);
      }
      .schema-type-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .schema-type-btn i {
         display: block;
         font-size: 1.5rem;
         margin-bottom: 0.5rem;
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
         min-height: 100px;
         resize: vertical;
      }
      .form-group small {
         display: block;
         color: #666;
         font-size: 0.85rem;
         margin-top: 0.3rem;
      }
      .field-group {
         background: #f9f9f9;
         padding: 1rem;
         border-radius: 8px;
         margin-bottom: 1rem;
         border: 1px solid #eee;
      }
      .field-group h4 {
         margin-bottom: 0.5rem;
         color: var(--text-color);
         font-size: 1rem;
      }
      .schema-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .schema-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .schema-btn.generate {
         background: var(--primary);
         color: white;
      }
      .schema-btn.generate:hover {
         background: var(--secondary);
      }
      .schema-btn.copy {
         background: var(--secondary);
         color: white;
      }
      .schema-btn.copy:hover {
         background: var(--primary);
      }
      .schema-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .schema-btn.reset:hover {
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
      .code-tag {
         color: #569cd6;
      }
      .code-key {
         color: #9cdcfe;
      }
      .code-string {
         color: #ce9178;
      }
      .code-number {
         color: #b5cea8;
      }
      .code-boolean {
         color: #569cd6;
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
      .schema-description {
         background: #f0f8ff;
         padding: 1rem;
         border-radius: 8px;
         margin-bottom: 1.5rem;
         border-left: 4px solid var(--primary);
      }
      .schema-description p {
         color: var(--text-color);
         font-size: 0.95rem;
         margin: 0;
      }
      .hidden {
         display: none;
      }
      @media screen and (max-width: 1200px) {
         .schema-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 992px) {
         .schema-generator {
            grid-template-columns: 1fr;
         }
         .schema-type-grid {
            grid-template-columns: repeat(2, 1fr);
         }
      }
      @media screen and (max-width: 768px) {
         .schema-container {
            padding: 2% 1rem 5%;
         }
         .schema-header h1 {
            font-size: 2.2rem;
         }
         .schema-actions {
            flex-direction: column;
         }
         .schema-type-grid {
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
      <section class="schema-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="schema-header scroll-effect">
            <h1>Schema Markup Generator</h1>
            <p>Generate structured data for better SEO and rich results</p>
         </div>
         
         <div class="schema-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Schema Configuration</h2>
               
               <div class="schema-type-selector">
                  <label>Select Schema Type:</label>
                  <div class="schema-type-grid">
                     <div class="schema-type-btn active" data-type="article">
                        <i class="fas fa-newspaper"></i>
                        Article
                     </div>
                     <div class="schema-type-btn" data-type="organization">
                        <i class="fas fa-building"></i>
                        Organization
                     </div>
                     <div class="schema-type-btn" data-type="product">
                        <i class="fas fa-shopping-bag"></i>
                        Product
                     </div>
                     <div class="schema-type-btn" data-type="event">
                        <i class="fas fa-calendar-alt"></i>
                        Event
                     </div>
                     <div class="schema-type-btn" data-type="person">
                        <i class="fas fa-user"></i>
                        Person
                     </div>
                     <div class="schema-type-btn" data-type="website">
                        <i class="fas fa-globe"></i>
                        Website
                     </div>
                  </div>
               </div>
               
               <div class="schema-description" id="schemaDescription">
                  <p><strong>Article Schema:</strong> For news articles, blog posts, and other written content. Helps search engines understand your content better and display rich snippets.</p>
               </div>
               
               <!-- Article Fields -->
               <div id="articleFields" class="schema-fields">
                  <div class="form-group">
                     <label for="articleHeadline">Headline *</label>
                     <input type="text" id="articleHeadline" placeholder="Article title">
                  </div>
                  
                  <div class="form-group">
                     <label for="articleDescription">Description *</label>
                     <textarea id="articleDescription" placeholder="Brief description of the article"></textarea>
                  </div>
                  
                  <div class="form-group">
                     <label for="articleAuthorName">Author Name *</label>
                     <input type="text" id="articleAuthorName" placeholder="Author's name">
                  </div>
                  
                  <div class="form-group">
                     <label for="articleDatePublished">Date Published *</label>
                     <input type="date" id="articleDatePublished">
                  </div>
                  
                  <div class="form-group">
                     <label for="articleDateModified">Date Modified</label>
                     <input type="date" id="articleDateModified">
                  </div>
                  
                  <div class="form-group">
                     <label for="articleImage">Image URL</label>
                     <input type="url" id="articleImage" placeholder="https://example.com/image.jpg">
                  </div>
               </div>
               
               <!-- Organization Fields -->
               <div id="organizationFields" class="schema-fields hidden">
                  <div class="form-group">
                     <label for="orgName">Organization Name *</label>
                     <input type="text" id="orgName" placeholder="Company name">
                  </div>
                  
                  <div class="form-group">
                     <label for="orgUrl">Website URL *</label>
                     <input type="url" id="orgUrl" placeholder="https://example.com">
                  </div>
                  
                  <div class="form-group">
                     <label for="orgLogo">Logo URL</label>
                     <input type="url" id="orgLogo" placeholder="https://example.com/logo.png">
                  </div>
                  
                  <div class="field-group">
                     <h4>Contact Information</h4>
                     <div class="form-group">
                        <label for="orgTelephone">Telephone</label>
                        <input type="tel" id="orgTelephone" placeholder="+1-234-567-8900">
                     </div>
                     
                     <div class="form-group">
                        <label for="orgEmail">Email</label>
                        <input type="email" id="orgEmail" placeholder="contact@example.com">
                     </div>
                  </div>
               </div>
               
               <!-- Product Fields -->
               <div id="productFields" class="schema-fields hidden">
                  <div class="form-group">
                     <label for="productName">Product Name *</label>
                     <input type="text" id="productName" placeholder="Product name">
                  </div>
                  
                  <div class="form-group">
                     <label for="productDescription">Description *</label>
                     <textarea id="productDescription" placeholder="Product description"></textarea>
                  </div>
                  
                  <div class="form-group">
                     <label for="productImage">Image URL</label>
                     <input type="url" id="productImage" placeholder="https://example.com/product.jpg">
                  </div>
                  
                  <div class="dimension-controls" style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                     <div class="form-group">
                        <label for="productPrice">Price *</label>
                        <input type="number" id="productPrice" placeholder="99.99" step="0.01">
                     </div>
                     
                     <div class="form-group">
                        <label for="productCurrency">Currency *</label>
                        <select id="productCurrency">
                           <option value="USD">USD ($)</option>
                           <option value="EUR">EUR (€)</option>
                           <option value="GBP">GBP (£)</option>
                           <option value="CAD">CAD ($)</option>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="productAvailability">Availability</label>
                     <select id="productAvailability">
                        <option value="InStock">In Stock</option>
                        <option value="OutOfStock">Out of Stock</option>
                        <option value="PreOrder">Pre-Order</option>
                        <option value="Discontinued">Discontinued</option>
                     </select>
                  </div>
               </div>
               
               <div class="schema-actions">
                  <button class="schema-btn generate" onclick="generateSchema()">
                     <i class="fas fa-code"></i> Generate Schema
                  </button>
                  <button class="schema-btn reset" onclick="resetForm()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated Schema Markup</h2>
               
               <div class="output-code" id="outputCode">
// Generated schema markup will appear here
               </div>
               
               <div class="schema-actions">
                  <button class="schema-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy JSON-LD
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Schema markup copied to clipboard!
               </div>
               
               <div class="schema-description" style="margin-top: 2rem;">
                  <p><strong>How to use:</strong> Copy the generated JSON-LD script and paste it into the &lt;head&gt; section of your HTML page. This helps search engines understand your content better.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      // Schema descriptions
      const schemaDescriptions = {
         article: '<strong>Article Schema:</strong> For news articles, blog posts, and other written content. Helps search engines understand your content better and display rich snippets.',
         organization: '<strong>Organization Schema:</strong> For businesses, companies, and organizations. Helps establish your brand identity in search results.',
         product: '<strong>Product Schema:</strong> For physical products, digital downloads, or services. Enables rich product snippets with price, availability, and ratings.',
         event: '<strong>Event Schema:</strong> For concerts, conferences, workshops, and other events. Displays event details directly in search results.',
         person: '<strong>Person Schema:</strong> For individuals, authors, or public figures. Helps establish authority and identity.',
         website: '<strong>Website Schema:</strong> For entire websites. Helps search engines understand your site structure and purpose.'
      };
      
      document.addEventListener('DOMContentLoaded', function() {
         // Set current date as default for date fields
         const today = new Date().toISOString().split('T')[0];
         document.getElementById('articleDatePublished').value = today;
         document.getElementById('articleDateModified').value = today;
         
         // Schema type selector
         const schemaTypeBtns = document.querySelectorAll('.schema-type-btn');
         schemaTypeBtns.forEach(btn => {
            btn.addEventListener('click', function() {
               const type = this.getAttribute('data-type');
               
               // Update active button
               schemaTypeBtns.forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               
               // Update description
               document.getElementById('schemaDescription').innerHTML = `<p>${schemaDescriptions[type]}</p>`;
               
               // Show/hide fields
               document.querySelectorAll('.schema-fields').forEach(field => {
                  field.classList.add('hidden');
               });
               document.getElementById(type + 'Fields').classList.remove('hidden');
            });
         });
      });
      
      function generateSchema() {
         const activeBtn = document.querySelector('.schema-type-btn.active');
         const schemaType = activeBtn.getAttribute('data-type');
         
         let schema = {};
         
         switch(schemaType) {
            case 'article':
               schema = generateArticleSchema();
               break;
            case 'organization':
               schema = generateOrganizationSchema();
               break;
            case 'product':
               schema = generateProductSchema();
               break;
            case 'event':
               schema = generateEventSchema();
               break;
            case 'person':
               schema = generatePersonSchema();
               break;
            case 'website':
               schema = generateWebsiteSchema();
               break;
         }
         
         displaySchema(schema, schemaType);
      }
      
      function generateArticleSchema() {
         const headline = document.getElementById('articleHeadline').value || 'Article Title';
         const description = document.getElementById('articleDescription').value || 'Article description';
         const authorName = document.getElementById('articleAuthorName').value || 'Author Name';
         const datePublished = document.getElementById('articleDatePublished').value || new Date().toISOString().split('T')[0];
         const dateModified = document.getElementById('articleDateModified').value || new Date().toISOString().split('T')[0];
         const image = document.getElementById('articleImage').value || '';
         
         return {
            "@context": "https://schema.org",
            "@type": "Article",
            "headline": headline,
            "description": description,
            "author": {
               "@type": "Person",
               "name": authorName
            },
            "datePublished": datePublished,
            "dateModified": dateModified,
            "image": image ? image : undefined,
            "publisher": {
               "@type": "Organization",
               "name": "Your Organization",
               "logo": {
                  "@type": "ImageObject",
                  "url": "https://example.com/logo.png"
               }
            }
         };
      }
      
      function generateOrganizationSchema() {
         const name = document.getElementById('orgName').value || 'Company Name';
         const url = document.getElementById('orgUrl').value || 'https://example.com';
         const logo = document.getElementById('orgLogo').value || '';
         const telephone = document.getElementById('orgTelephone').value || '';
         const email = document.getElementById('orgEmail').value || '';
         
         const schema = {
            "@context": "https://schema.org",
            "@type": "Organization",
            "name": name,
            "url": url
         };
         
         if (logo) schema.logo = logo;
         if (telephone || email) {
            schema.contactPoint = {
               "@type": "ContactPoint",
               "telephone": telephone,
               "email": email,
               "contactType": "customer service"
            };
         }
         
         return schema;
      }
      
      function generateProductSchema() {
         const name = document.getElementById('productName').value || 'Product Name';
         const description = document.getElementById('productDescription').value || 'Product description';
         const image = document.getElementById('productImage').value || '';
         const price = document.getElementById('productPrice').value || '0';
         const currency = document.getElementById('productCurrency').value || 'USD';
         const availability = document.getElementById('productAvailability').value || 'InStock';
         
         const schema = {
            "@context": "https://schema.org",
            "@type": "Product",
            "name": name,
            "description": description,
            "offers": {
               "@type": "Offer",
               "price": parseFloat(price),
               "priceCurrency": currency,
               "availability": `https://schema.org/${availability}`
            }
         };
         
         if (image) schema.image = image;
         
         return schema;
      }
      
      function generateEventSchema() {
         // Simplified event schema for example
         return {
            "@context": "https://schema.org",
            "@type": "Event",
            "name": "Event Name",
            "startDate": new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            "endDate": new Date(Date.now() + 8 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
            "eventAttendanceMode": "https://schema.org/OfflineEventAttendanceMode",
            "eventStatus": "https://schema.org/EventScheduled",
            "location": {
               "@type": "Place",
               "name": "Venue Name",
               "address": {
                  "@type": "PostalAddress",
                  "streetAddress": "123 Main St",
                  "addressLocality": "City",
                  "addressRegion": "State",
                  "postalCode": "12345",
                  "addressCountry": "US"
               }
            },
            "organizer": {
               "@type": "Organization",
               "name": "Organizer Name",
               "url": "https://example.com"
            }
         };
      }
      
      function generatePersonSchema() {
         return {
            "@context": "https://schema.org",
            "@type": "Person",
            "name": "John Doe",
            "jobTitle": "Software Developer",
            "url": "https://example.com",
            "sameAs": [
               "https://twitter.com/johndoe",
               "https://linkedin.com/in/johndoe"
            ]
         };
      }
      
      function generateWebsiteSchema() {
         return {
            "@context": "https://schema.org",
            "@type": "WebSite",
            "name": "Website Name",
            "url": "https://example.com",
            "description": "Website description",
            "publisher": {
               "@type": "Organization",
               "name": "Publisher Name"
            }
         };
      }
      
      function displaySchema(schema, type) {
         // Remove undefined properties
         const cleanSchema = JSON.parse(JSON.stringify(schema));
         
         const jsonString = JSON.stringify(cleanSchema, null, 2);
         const htmlString = `<script type="application/ld+json">\n${jsonString}\n<\/script>`;
         
         // Syntax highlighting
         const highlighted = jsonString
            .replace(/(".*?"):/g, '<span class="code-key">$1</span>:')
            .replace(/"([^"]*?)"/g, '<span class="code-string">"$1"</span>')
            .replace(/\b(true|false|null)\b/g, '<span class="code-boolean">$1</span>')
            .replace(/\b(\d+\.?\d*)\b/g, '<span class="code-number">$1</span>');
         
         const finalHTML = `<span class="code-tag">&lt;script type="application/ld+json"&gt;</span>\n${highlighted}\n<span class="code-tag">&lt;/script&gt;</span>`;
         
         document.getElementById('outputCode').innerHTML = finalHTML;
      }
      
      function resetForm() {
         // Reset all form fields
         document.querySelectorAll('input, textarea').forEach(input => {
            if (input.type === 'date') {
               const today = new Date().toISOString().split('T')[0];
               input.value = today;
            } else {
               input.value = '';
            }
         });
         
         // Reset to article type
         const schemaTypeBtns = document.querySelectorAll('.schema-type-btn');
         schemaTypeBtns.forEach(b => b.classList.remove('active'));
         schemaTypeBtns[0].classList.add('active');
         
         document.querySelectorAll('.schema-fields').forEach(field => {
            field.classList.add('hidden');
         });
         document.getElementById('articleFields').classList.remove('hidden');
         
         document.getElementById('outputCode').innerHTML = '// Generated schema markup will appear here';
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