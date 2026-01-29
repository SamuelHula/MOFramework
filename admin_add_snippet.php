<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './assets/config.php';

if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

$active_page = 'admin_add_snippet';

try {
   $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
   $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   error_log("Failed to fetch categories: " . $e->getMessage());
   $categories = [];
}

$languages = getAllLanguages($pdo);
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Add Code Snippet - Admin Panel</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/theme/monokai.min.css">
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
         padding-top: 70px;
      }
      .admin-snippet-container {
         padding: 2rem;
         max-width: 1400px;
         margin: 0 auto;
      }
      .admin-header {
         text-align: center;
         margin-bottom: 3rem;
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      }
      .admin-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
         font-family: var(--heading);
      }
      .admin-header p {
         color: var(--text-color);
         opacity: 0.7;
         font-size: 1.1rem;
      }
      .snippet-form-container {
         background: white;
         padding: 2.5rem;
         border-radius: 20px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
      }
      .snippet-form {
         display: grid;
         gap: 1.5rem;
      }
      .form-group {
         display: flex;
         flex-direction: column;
      }
      .form-row {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 1.5rem;
      }
      .form-group label {
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1rem;
         font-family: var(--subheading);
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .form-group input,
      .form-group select,
      .form-group textarea {
         padding: 0.9rem 1.2rem;
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         font-size: 1rem;
         transition: all 0.3s ease;
         background: var(--back-light);
         font-family: var(--text_font);
      }
      .form-group input:focus,
      .form-group select:focus,
      .form-group textarea:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .form-group textarea {
         resize: vertical;
         min-height: 100px;
      }
      .code-editor-container {
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         overflow: hidden;
      }
      .code-editor-header {
         background: #2d2d2d;
         padding: 0.8rem 1rem;
         display: flex;
         justify-content: space-between;
         align-items: center;
         border-bottom: 1px solid #444;
      }
      .language-selector {
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .language-selector select {
         background: #1a1a1a;
         color: #fff;
         border: 1px solid #444;
         padding: 0.3rem 0.5rem;
         border-radius: 4px;
         font-size: 0.9rem;
      }
      .editor-actions {
         display: flex;
         gap: 0.5rem;
      }
      .editor-btn {
         background: #444;
         color: #fff;
         border: none;
         padding: 0.3rem 0.8rem;
         border-radius: 4px;
         cursor: pointer;
         font-size: 0.9rem;
         transition: background 0.3s;
      }
      .editor-btn:hover {
         background: #555;
      }
      .CodeMirror {
         height: 300px;
         font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
         font-size: 14px;
      }
      .tag-input-container {
         display: flex;
         flex-wrap: wrap;
         gap: 0.5rem;
         padding: 0.5rem;
         border: 2px solid var(--back-dark);
         border-radius: 10px;
         min-height: 50px;
         background: var(--back-light);
      }
      .tag {
         background: var(--primary);
         color: white;
         padding: 0.3rem 0.8rem;
         border-radius: 20px;
         display: flex;
         align-items: center;
         gap: 0.5rem;
         font-size: 0.9rem;
      }
      .tag-remove {
         cursor: pointer;
         font-size: 1.2rem;
         line-height: 1;
      }
      .tag-input {
         border: none;
         outline: none;
         background: transparent;
         flex: 1;
         min-width: 100px;
         padding: 0.5rem;
      }
      .form-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         padding-top: 2rem;
         border-top: 1px solid var(--back-dark);
      }
      .btn-submit, .btn-cancel {
         padding: 1rem 2rem;
         border-radius: 10px;
         font-size: 1.1rem;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s ease;
         border: 2px solid transparent;
         font-family: var(--subheading);
      }
      .btn-submit {
         background: var(--primary);
         color: white;
      }
      .btn-submit:hover {
         background: var(--secondary);
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(48, 188, 237, 0.3);
      }
      .btn-cancel {
         background: transparent;
         color: var(--text-color);
         border-color: var(--back-dark);
      }
      .btn-cancel:hover {
         background: var(--back-dark);
      }
      .checkbox-group {
         display: flex;
         gap: 1rem;
         flex-wrap: wrap;
      }
      .checkbox-item {
         display: flex;
         align-items: center;
         gap: 0.5rem;
      }
      .checkbox-item input[type="checkbox"] {
         width: 18px;
         height: 18px;
      }
      .checkbox-item label {
         margin: 0;
         font-weight: normal;
      }
      
      .floating-balls {
         position: fixed;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         pointer-events: none;
         z-index: 0;
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

      @media screen and (max-width: 768px) {
         body {
               padding-top: 120px;
         }
         .admin-snippet-container {
               padding: 1rem;
         }
         .snippet-form-container {
               padding: 1.5rem;
         }
         .form-row {
               grid-template-columns: 1fr;
               gap: 1rem;
         }
         .CodeMirror {
               height: 200px;
         }
      }
      @media screen and (max-width: 768px) {
         .snippet-form-container {
            padding: 1.5rem;
         }
         
         .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
         }
         
         .CodeMirror {
            height: 200px;
         }
         
         .form-actions {
            flex-direction: column;
         }
         
         .btn-submit,
         .btn-cancel {
            width: 100%;
            text-align: center;
         }
      }
      @media screen and (max-width: 480px) {
         .admin-snippet-container {
            padding: 0.5rem;
         }
         
         .admin-header {
            padding: 1.5rem;
         }
         
         .admin-header h1 {
            font-size: 1.8rem;
         }
         
         .admin-header p {
            font-size: 1rem;
         }
         
         .snippet-form-container {
            padding: 1rem;
         }
         
         .form-group input,
         .form-group select,
         .form-group textarea {
            width: 100%;
            max-width: 100%;
            font-size: 16px; 
            padding: 0.8rem;
         }
         
         .form-row {
            grid-template-columns: 1fr;
            gap: 0.8rem;
         }
         
         .code-editor-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 0.5rem;
         }
         
         .language-selector,
         .editor-actions {
            width: 100%;
            justify-content: space-between;
         }
         
         .CodeMirror {
            height: 200px;
            font-size: 12px;
         }
         
         .tag-input-container {
            min-height: auto;
            padding: 0.5rem;
         }
         
         .tag-input {
            min-width: 50px;
            font-size: 16px;
         }
         
         .checkbox-group {
            flex-direction: column;
            gap: 0.5rem;
         }
         
         .form-actions {
            flex-direction: column;
            gap: 0.8rem;
         }
         
         .btn-submit,
         .btn-cancel {
            width: 100%;
            padding: 0.9rem;
         }
      }

      @media screen and (max-width: 350px) {
         body {
            padding-top: 60px;
         }
         
         .admin-header {
            padding: 1rem;
         }
         
         .admin-header h1 {
            font-size: 1.5rem;
         }
         
         .admin-header p {
            font-size: 0.9rem;
         }
         
         .snippet-form-container {
            padding: 0.8rem;
         }
         
         .form-group input,
         .form-group select,
         .form-group textarea {
            font-size: 14px;
            padding: 0.7rem;
         }
         
         .form-group label {
            font-size: 0.9rem;
         }
         
         .code-editor-header {
            padding: 0.6rem;
         }
         
         .CodeMirror {
            height: 180px;
         }
         
         .tag {
            font-size: 0.8rem;
            padding: 0.2rem 0.6rem;
         }
         
         .btn-submit,
         .btn-cancel {
            padding: 0.8rem;
            font-size: 1rem;
         }
      }
   </style>
</head>
<body>
   <div class="floating-balls">
      <div class="ball auth-ball-1"></div>
      <div class="ball auth-ball-2"></div>
      <div class="ball auth-ball-3"></div>
      <div class="ball auth-ball-4"></div>
      <div class="ball auth-ball-5"></div>
      <div class="ball auth-ball-6"></div>
   </div>
   
   <?php 
   $admin_navbar_path = './includes/admin_navbar.php';
   if (file_exists($admin_navbar_path)) {
      include_once $admin_navbar_path;
   } else {
      echo '<nav class="admin-nav-bar">
               <a href="admin_dashboard.php" class="admin-nav-brand">Admin Panel</a>
               <div class="admin-nav-menu">
                  <a href="admin_dashboard.php" class="admin-nav-link">Dashboard</a>
                  <a href="admin_manage_snippets.php" class="admin-nav-link">Snippets</a>
                  <a href="manage_users.php" class="admin-nav-link">Users</a>
                  <a href="manage_admin.php" class="admin-nav-link">Admins</a>
                  <a href="./assets/logout.php" class="admin-signout-btn">Logout</a>
               </div>
            </nav>';
   }
   ?>
   
   <main id="main">
      <section class="admin-snippet-container">
         <div class="admin-header scroll-effect">
               <h1>Add Code Snippet</h1>
               <p>Share your code with the community</p>
         </div>
         
         <div class="snippet-form-container scroll-effect">
               <form class="snippet-form" id="snippetForm" action="./assets/process_add_snippet.php" method="POST">
                  <div class="form-row">
                     <div class="form-group">
                           <label for="title">Snippet Title *</label>
                           <input type="text" id="title" name="title" required placeholder="e.g., Responsive Navbar with CSS Grid">
                     </div>
                     <div class="form-group">
                        <label for="language">Programming Language *</label>
                        <select id="language" name="language" required>
                           <option value="">Select Language</option>
                           <?php foreach ($languages as $lang): ?>
                                 <option value="<?php echo htmlspecialchars(strtolower($lang)); ?>">
                                    <?php echo htmlspecialchars($lang); ?>
                                 </option>
                           <?php endforeach; ?>
                        </select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="description">Description *</label>
                     <textarea id="description" name="description" required placeholder="Describe what this code does..."></textarea>
                  </div>
                  
                  <div class="form-group">
                     <label for="category_id">Category</label>
                     <select id="category_id" name="category_id">
                           <option value="">Select Category</option>
                           <?php foreach ($categories as $category) { ?>
                              <option value="<?php echo $category['id']; ?>">
                                 <?php echo htmlspecialchars($category['name']); ?>
                              </option>
                           <?php } ?>
                     </select>
                  </div>
                  
                  <div class="form-group">
                     <label for="code">Code *</label>
                     <div class="code-editor-container">
                           <div class="code-editor-header">
                              <div class="language-selector">
                                 <span style="color: #fff;">Language:</span>
                                 <select id="editorLanguage">
                                       <?php foreach ($languages as $lang): ?>
                                          <option value="<?php echo htmlspecialchars(strtolower($lang)); ?>">
                                             <?php echo htmlspecialchars($lang); ?>
                                          </option>
                                       <?php endforeach; ?>
                                 </select>
                              </div>
                              <div class="editor-actions">
                                 <button type="button" class="editor-btn" onclick="formatCode()">Format</button>
                                 <button type="button" class="editor-btn" onclick="clearCode()">Clear</button>
                              </div>
                           </div>
                           <textarea id="code" name="code" style="display: none;"></textarea>
                           <div id="codeEditor"></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="tags">Tags</label>
                     <div class="tag-input-container" id="tagContainer">
                           <input type="text" id="tagInput" class="tag-input" placeholder="Type a tag and press Enter">
                     </div>
                     <input type="hidden" id="tags" name="tags">
                  </div>
                  
                  <div class="checkbox-group">
                     <div class="checkbox-item">
                           <input type="checkbox" id="is_featured" name="is_featured" value="1">
                           <label for="is_featured">Featured Snippet</label>
                     </div>
                     <div class="checkbox-item">
                           <input type="checkbox" id="is_public" name="is_public" value="1" checked>
                           <label for="is_public">Public (Visible to all users)</label>
                     </div>
                  </div>
                  
                  <div class="form-actions">
                     <button type="submit" class="btn-submit">Add Snippet</button>
                     <a href="admin_dashboard.php" class="btn-cancel">Cancel</a>
                  </div>
               </form>
         </div>
      </section>
   </main>
   
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/javascript/javascript.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/htmlmixed/htmlmixed.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/css/css.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/php/php.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/python/python.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/sql/sql.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/clike/clike.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/ruby/ruby.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/jsx/jsx.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/xml/xml.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/markdown/markdown.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/shell/shell.min.js"></script>
   <script>
      const modeMap = {
         'html': 'htmlmixed',
         'css': 'css',
         'javascript': 'javascript',
         'php': 'php',
         'python': 'python',
         'sql': 'sql',
         'java': 'clike',
         'csharp': 'clike',
         'cpp': 'clike',
         'ruby': 'ruby',
         'typescript': 'javascript',
         'jsx': 'jsx',
         'tsx': 'jsx',
         'json': 'javascript',
         'xml': 'xml',
         'markdown': 'markdown',
         'bash': 'shell',
         'shell': 'shell'
      };
      
      const codeEditor = CodeMirror(document.getElementById('codeEditor'), {
         mode: 'htmlmixed',
         theme: 'monokai',
         lineNumbers: true,
         indentUnit: 4,
         matchBrackets: true,
         autoCloseBrackets: true,
         lineWrapping: true,
         value: '<!-- Enter your code here -->\n'
      });
      
      codeEditor.on('change', function(editor) {
         document.getElementById('code').value = editor.getValue();
      });
      
      document.getElementById('editorLanguage').addEventListener('change', function() {
         const language = this.value;
         const mode = modeMap[language] || 'htmlmixed';
         codeEditor.setOption('mode', mode);
         document.getElementById('language').value = language;
      });
      
      document.getElementById('language').addEventListener('change', function() {
         const language = this.value;
         const mode = modeMap[language] || 'htmlmixed';
         codeEditor.setOption('mode', mode);
         document.getElementById('editorLanguage').value = language;
      });
      
      const tagContainer = document.getElementById('tagContainer');
      const tagInput = document.getElementById('tagInput');
      const tagsHidden = document.getElementById('tags');
      let tags = [];
      
      tagInput.addEventListener('keydown', function(e) {
         if (e.key === 'Enter' || e.key === ',') {
               e.preventDefault();
               const tag = tagInput.value.trim();
               if (tag && !tags.includes(tag)) {
                  addTag(tag);
                  tagInput.value = '';
               }
         }
      });
      
      function addTag(tag) {
         tags.push(tag);
         updateTags();
         
         const tagElement = document.createElement('div');
         tagElement.className = 'tag';
         tagElement.innerHTML = `
               ${tag}
               <span class="tag-remove" onclick="removeTag('${tag}')">&times;</span>
         `;
         tagContainer.insertBefore(tagElement, tagInput);
      }
      
      function removeTag(tag) {
         tags = tags.filter(t => t !== tag);
         updateTags();
         const tagElements = tagContainer.querySelectorAll('.tag');
         tagElements.forEach(el => {
               if (el.textContent.trim().replace('×', '') === tag) {
                  el.remove();
               }
         });
      }
      
      function updateTags() {
         tagsHidden.value = tags.join(',');
      }
      
      function formatCode() {
         const code = codeEditor.getValue();
         const formatted = code.replace(/\n\s*\n/g, '\n').trim();
         codeEditor.setValue(formatted);
      }
      
      function clearCode() {
         if (confirm('Are you sure you want to clear the code editor?')) {
               codeEditor.setValue('');
         }
      }
      
      document.getElementById('snippetForm').addEventListener('submit', function(e) {
         const title = document.getElementById('title').value.trim();
         const description = document.getElementById('description').value.trim();
         const code = codeEditor.getValue().trim();
         const language = document.getElementById('language').value;
         
         let errors = [];
         
         if (!title) errors.push('Title is required');
         if (!description) errors.push('Description is required');
         if (!code) errors.push('Code is required');
         if (!language) errors.push('Language is required');
         
         if (errors.length > 0) {
               e.preventDefault();
               alert('Please fix the following errors:\n\n' + errors.join('\n'));
         }
      });
      
      document.addEventListener('DOMContentLoaded', function() {
         const scrollElements = document.querySelectorAll('.scroll-effect');
         
         const elementInView = (el, percentageScroll = 100) => {
               const elementTop = el.getBoundingClientRect().top;
               return (
                  elementTop <= 
                  ((window.innerHeight || document.documentElement.clientHeight) * (percentageScroll/100))
               );
         };
         
         const displayScrollElement = (element) => {
               element.classList.add('visible');
         };
         
         const handleScrollAnimation = () => {
               scrollElements.forEach((el) => {
                  if (elementInView(el, 100)) {
                     displayScrollElement(el);
                  }
               });
         };
         
         window.addEventListener('scroll', () => {
               handleScrollAnimation();
         });
         
         handleScrollAnimation();
      });
   </script>
</body>
</html>