<?php
// code_formatter.php
require_once './assets/config.php';

// Check if user is logged in
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

$current_page = 'web_tools';

// Initialize variables
$input_code = '';
$formatted_code = '';
$language = 'html';
$error = '';
$success = false;
$show_input = true; // Flag to control input visibility

// Check if we're coming from a clear action
if (isset($_GET['clear']) && $_GET['clear'] == '1') {
   // Clear session data if any
   unset($_SESSION['formatter_data']);
   $input_code = '';
   $formatted_code = '';
   $show_input = true;
}

// Function to format HTML code
function formatHTML($code) {
   // Remove existing indentation
   $lines = explode("\n", $code);
   $formatted_lines = [];
   $indent_level = 0;
   
   foreach ($lines as $line) {
      $trimmed_line = trim($line);
      
      // Skip empty lines unless they're in the middle of content
      if (empty($trimmed_line)) {
         continue;
      }
      
      // Check for closing tags that should decrease indent
      if (preg_match('/^<\/(\w+)/', $trimmed_line)) {
         $indent_level = max(0, $indent_level - 1);
      }
      
      // Add indentation
      $indent = str_repeat('    ', $indent_level);
      $formatted_lines[] = $indent . $trimmed_line;
      
      // Check for opening tags that should increase indent (excluding self-closing tags)
      if (preg_match('/^<(\w+)(?![^>]*\/>)/', $trimmed_line) && !preg_match('/<\/(\w+)>/', $trimmed_line)) {
         $indent_level++;
      }
   }
   
   return implode("\n", $formatted_lines);
}

// Function to format CSS code
function formatCSS($code) {
   $formatted = '';
   $indent_level = 0;
   $in_rule = false;
   
   // Remove multiple spaces and normalize line endings
   $code = preg_replace('/\s+/', ' ', $code);
   $code = str_replace(['{', '}', ';'], [" {\n", "\n}\n", ";\n"], $code);
   
   $lines = explode("\n", $code);
   
   foreach ($lines as $line) {
      $trimmed_line = trim($line);
      
      if (empty($trimmed_line)) {
         continue;
      }
      
      // Decrease indent before closing brace
      if (strpos($trimmed_line, '}') !== false) {
         $indent_level = max(0, $indent_level - 1);
      }
      
      // Add indentation
      $indent = str_repeat('    ', $indent_level);
      $formatted .= $indent . $trimmed_line . "\n";
      
      // Increase indent after opening brace
      if (strpos($trimmed_line, '{') !== false) {
         $indent_level++;
      }
   }
   
   return trim($formatted);
}

// Function to format JavaScript code
function formatJS($code) {
   $formatted = '';
   $indent_level = 0;
   $in_string = false;
   $string_char = '';
   
   // Add spaces around operators for better parsing
   $code = preg_replace('/([=+\-*\/%&|^<>!]+)/', ' $1 ', $code);
   
   $chars = str_split($code);
   
   for ($i = 0; $i < count($chars); $i++) {
      $char = $chars[$i];
      
      // Handle strings
      if (($char === '"' || $char === "'") && ($i === 0 || $chars[$i-1] !== '\\')) {
         if (!$in_string) {
               $in_string = true;
               $string_char = $char;
         } elseif ($char === $string_char) {
               $in_string = false;
         }
      }
      
      if (!$in_string) {
         // Handle braces and brackets
         if ($char === '{' || $char === '[') {
               $formatted .= $char . "\n" . str_repeat('    ', ++$indent_level);
               continue;
         }
         
         if ($char === '}' || $char === ']') {
               $formatted .= "\n" . str_repeat('    ', --$indent_level) . $char;
               continue;
         }
         
         // Handle semicolons
         if ($char === ';') {
               $formatted .= $char . "\n" . str_repeat('    ', $indent_level);
               continue;
         }
         
         // Handle commas
         if ($char === ',') {
               $formatted .= $char . ' ';
               continue;
         }
      }
      
      $formatted .= $char;
   }
   
   // Clean up multiple newlines
   $formatted = preg_replace("/\n\s*\n/", "\n", $formatted);
   
   return trim($formatted);
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (isset($_POST['code']) && isset($_POST['language'])) {
      $input_code = trim($_POST['code']);
      $language = $_POST['language'];
      
      if (!empty($input_code)) {
         try {
               switch ($language) {
                  case 'html':
                     $formatted_code = formatHTML($input_code);
                     break;
                  case 'css':
                     $formatted_code = formatCSS($input_code);
                     break;
                  case 'javascript':
                     $formatted_code = formatJS($input_code);
                     break;
                  default:
                     $formatted_code = $input_code;
               }
               $success = true;
               $show_input = false; // Hide input after successful formatting
               
               // Store in session for persistence if needed
               $_SESSION['formatter_data'] = [
                  'input' => $input_code,
                  'output' => $formatted_code,
                  'language' => $language
               ];
         } catch (Exception $e) {
               $error = 'Error formatting code: ' . $e->getMessage();
               $show_input = true; // Keep input visible on error
         }
      } else {
         $error = 'Please enter some code to format.';
         $show_input = true; // Keep input visible on error
      }
   }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Code Formatter - Code Library</title>
   <link rel="stylesheet" href="./css/general.css">
   <link rel="stylesheet" href="./css/home.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.8.0/styles/github.min.css">
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
      .formatter-container {
         min-height: 100vh;
         padding: 2% 5% 5%;
         position: relative;
         z-index: 2;
      }
      .formatter-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .formatter-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .formatter-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .formatter-card {
         background: white;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         padding: 2.5rem;
         margin-bottom: 2rem;
      }
      .formatter-form {
         display: flex;
         flex-direction: column;
         gap: 2rem;
      }
      .form-group {
         display: flex;
         flex-direction: column;
         gap: 0.8rem;
      }
      .form-group label {
         font-weight: 600;
         color: var(--text-color);
         font-size: 1.1rem;
      }
      .language-selector {
         display: flex;
         gap: 1rem;
         flex-wrap: wrap;
      }
      .language-option {
         padding: 0.8rem 1.5rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         background: white;
         cursor: pointer;
         transition: all 0.3s;
         font-weight: 500;
      }
      .language-option:hover {
         border-color: var(--primary);
         background: var(--back-light);
      }
      .language-option.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .code-input,
      .code-output {
         width: 100%;
         min-height: 300px;
         padding: 1.5rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
         font-size: 0.95rem;
         line-height: 1.6;
         resize: vertical;
         background: #f8f9fa;
      }
      .code-input:focus,
      .code-output:focus {
         outline: none;
         border-color: var(--primary);
      }
      .form-actions {
         display: flex;
         gap: 1rem;
         flex-wrap: wrap;
      }
      .form-btn {
         padding: 0.9rem 2rem;
         border-radius: 8px;
         font-weight: 600;
         font-size: 1rem;
         cursor: pointer;
         transition: all 0.3s;
         border: 2px solid transparent;
         display: flex;
         align-items: center;
         gap: 0.5rem;
         text-decoration: none;
         text-align: center;
      }
      .btn-primary {
         background: var(--primary);
         color: white;
      }
      .btn-primary:hover {
         background: var(--secondary);
         transform: translateY(-2px);
      }
      .btn-secondary {
         background: transparent;
         color: var(--text-color);
         border-color: var(--back-dark);
      }
      .btn-secondary:hover {
         background: var(--back-dark);
      }
      .btn-success {
         background: #4CAF50;
         color: white;
      }
      .btn-success:hover {
         background: #45a049;
         transform: translateY(-2px);
      }
      .error-message {
         background: #ffebee;
         color: #c62828;
         padding: 1rem;
         border-radius: 8px;
         margin-bottom: 1rem;
         border-left: 4px solid #c62828;
      }
      .success-message {
         background: #e8f5e9;
         color: #2e7d32;
         padding: 1rem;
         border-radius: 8px;
         margin-bottom: 1rem;
         border-left: 4px solid #2e7d32;
      }
      .code-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 0.8rem;
      }
      .code-header h3 {
         color: var(--text-color);
         font-size: 1.2rem;
      }
      .copy-btn {
         background: var(--primary);
         color: white;
         border: none;
         padding: 0.5rem 1rem;
         border-radius: 5px;
         cursor: pointer;
         font-size: 0.9rem;
         display: flex;
         align-items: center;
         gap: 0.5rem;
         transition: background 0.3s;
      }
      .copy-btn:hover {
         background: var(--secondary);
      }
      .copy-btn.copied {
         background: #4CAF50;
      }
      .tool-info {
         background: white;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         padding: 2.5rem;
         margin-top: 2rem;
      }
      .tool-info h2 {
         font-size: 1.8rem;
         margin-bottom: 1.5rem;
         color: var(--text-color);
      }
      .features-list {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
         gap: 1.5rem;
         margin-top: 1.5rem;
      }
      .feature-item {
         background: var(--back-light);
         padding: 1.5rem;
         border-radius: 10px;
         border-left: 4px solid var(--primary);
      }
      .feature-item h4 {
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1.1rem;
      }
      .feature-item p {
         color: var(--text-color);
         opacity: 0.8;
         font-size: 0.95rem;
         line-height: 1.5;
      }
      .output-section {
         animation: fadeIn 0.5s ease;
      }
      
      @keyframes fadeIn {
         from { opacity: 0; transform: translateY(10px); }
         to { opacity: 1; transform: translateY(0); }
      }
      
      @media screen and (max-width: 768px) {
         .formatter-container {
               padding: 2% 1rem 5%;
         }
         .formatter-header h1 {
               font-size: 2.2rem;
         }
         .formatter-card,
         .tool-info {
               padding: 1.5rem;
         }
         .form-actions {
               flex-direction: column;
         }
         .form-btn {
               width: 100%;
               justify-content: center;
         }
         .language-selector {
               justify-content: center;
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
      <section class="formatter-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="formatter-header scroll-effect">
               <h1>Code Formatter</h1>
               <p>Format and beautify your HTML, CSS, and JavaScript code</p>
         </div>
         
         <?php if ($error): ?>
         <div class="error-message scroll-effect">
               <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
         </div>
         <?php endif; ?>
         
         <?php if ($success): ?>
         <div class="success-message ">
               <i class="fas fa-check-circle"></i> Code formatted successfully!
         </div>
         <?php endif; ?>
         
         <div class="formatter-card ">
               <form method="POST" class="formatter-form" id="codeFormatter">
                  <div class="form-group">
                     <label>Select Language:</label>
                     <div class="language-selector">
                           <input type="radio" id="lang-html" name="language" value="html" class="language-radio" hidden 
                                 <?php echo $language === 'html' ? 'checked' : ''; ?>>
                           <label for="lang-html" class="language-option <?php echo $language === 'html' ? 'active' : ''; ?>">
                              <i class="fab fa-html5"></i> HTML
                           </label>
                           
                           <input type="radio" id="lang-css" name="language" value="css" class="language-radio" hidden
                                 <?php echo $language === 'css' ? 'checked' : ''; ?>>
                           <label for="lang-css" class="language-option <?php echo $language === 'css' ? 'active' : ''; ?>">
                              <i class="fab fa-css3-alt"></i> CSS
                           </label>
                           
                           <input type="radio" id="lang-js" name="language" value="javascript" class="language-radio" hidden
                                 <?php echo $language === 'javascript' ? 'checked' : ''; ?>>
                           <label for="lang-js" class="language-option <?php echo $language === 'javascript' ? 'active' : ''; ?>">
                              <i class="fab fa-js"></i> JavaScript
                           </label>
                     </div>
                  </div>
                  
                  <?php if ($show_input): ?>
                  <div class="form-group" id="inputSection">
                     <div class="code-header">
                           <h3>Input Code:</h3>
                     </div>
                     <textarea name="code" id="inputCode" class="code-input" placeholder="Paste your code here..." required><?php echo htmlspecialchars($input_code); ?></textarea>
                  </div>
                  
                  <div class="form-actions">
                     <button type="submit" class="form-btn btn-primary">
                           <i class="fas fa-magic"></i> Format Code
                     </button>
                     
                     <a href="code_formatter.php?clear=1" class="form-btn btn-secondary">
                           <i class="fas fa-redo"></i> Clear
                     </a>
                     
                     <a href="web_tools.php" class="form-btn btn-secondary">
                           <i class="fas fa-arrow-left"></i> Back to Tools
                     </a>
                  </div>
                  <?php endif; ?>
                  
                  <?php if (!empty($formatted_code) && !$show_input): ?>
                  <div class="form-group output-section" id="outputSection">
                     <div class="code-header">
                     <h3>Formatted Code:</h3>
                           <button type="button" class="copy-btn" onclick="copyOutputCode()">
                              <i class="fas fa-copy"></i> Copy
                           </button>
                     </div>
                     <textarea id="outputCode" class="code-output" readonly><?php echo htmlspecialchars($formatted_code); ?></textarea>
                     
                     <div class="code-header" style="margin-top: 1rem; justify-content: space-between;">
                           <div style="font-size: 0.9rem; color: #666;">
                              <i class="fas fa-info-circle"></i>
                              Language: <strong><?php echo strtoupper($language); ?></strong>
                           </div>
                           <div style="font-size: 0.9rem; color: #666;">
                              <i class="fas fa-code"></i>
                              Lines: <strong><?php echo count(explode("\n", $formatted_code)); ?></strong>
                           </div>
                     </div>
                  </div>
                  
                  <div class="form-actions output-section">
                     <button type="button" class="form-btn btn-success" onclick="downloadCode()">
                           <i class="fas fa-download"></i> Download Code
                     </button>
                     
                     <button type="button" class="form-btn btn-primary" onclick="newCode()">
                           <i class="fas fa-plus"></i> Format New Code
                     </button>
                     
                     <a href="code_formatter.php?clear=1" class="form-btn btn-secondary">
                           <i class="fas fa-redo"></i> Clear All
                     </a>
                  </div>
                  <?php endif; ?>
               </form>
         </div>
         
         <div class="tool-info ">
               <h2>About Code Formatter</h2>
               <p>This tool helps you format and beautify your code with proper indentation and structure. It supports HTML, CSS, and JavaScript code formatting.</p>
               
               <div class="features-list">
                  <div class="feature-item">
                     <h4>HTML Formatting</h4>
                     <p>Properly indents HTML tags, handles nested elements, and formats attributes for better readability.</p>
                  </div>
                  
                  <div class="feature-item">
                     <h4>CSS Formatting</h4>
                     <p>Organizes CSS rules with consistent indentation, proper spacing, and aligned properties.</p>
                  </div>
                  
                  <div class="feature-item">
                     <h4>JavaScript Formatting</h4>
                     <p>Formats JavaScript code with proper indentation, handles braces, brackets, and semicolons.</p>
                  </div>
                  
                  <div class="feature-item">
                     <h4>Copy & Download</h4>
                     <p>Easily copy formatted code to clipboard or download it as a text file for later use.</p>
                  </div>
               </div>
               
               <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary);">
                  <h4 style="color: var(--text-color); margin-bottom: 0.5rem;">How to Use:</h4>
                  <ol style="color: var(--text-color); opacity: 0.8; line-height: 1.6; padding-left: 1.2rem;">
                     <li>Select the language (HTML, CSS, or JavaScript)</li>
                     <li>Paste your code into the input box</li>
                     <li>Click "Format Code" to beautify your code</li>
                     <li>Copy or download the formatted result</li>
                  </ol>
               </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      // Language selection
      document.querySelectorAll('.language-option').forEach(option => {
         option.addEventListener('click', function() {
               document.querySelectorAll('.language-option').forEach(opt => {
                  opt.classList.remove('active');
               });
               this.classList.add('active');
               this.querySelector('input[type="radio"]').checked = true;
         });
      });
      
      function copyOutputCode() {
         const outputCode = document.getElementById('outputCode');
         if (outputCode) {
               copyToClipboard(outputCode.value, 'Formatted code copied to clipboard!');
         }
      }
      
      function copyToClipboard(text, message) {
         navigator.clipboard.writeText(text).then(() => {
               showNotification(message || 'Copied to clipboard!');
               
               // Update button state temporarily
               const event = window.event;
               if (event && event.target) {
                  const originalHTML = event.target.innerHTML;
                  event.target.innerHTML = '<i class="fas fa-check"></i> Copied!';
                  event.target.classList.add('copied');
                  
                  setTimeout(() => {
                     event.target.innerHTML = originalHTML;
                     event.target.classList.remove('copied');
                  }, 2000);
               }
         }).catch(err => {
               console.error('Failed to copy: ', err);
               showNotification('Failed to copy code', 'error');
         });
      }
      
      function downloadCode() {
         const outputCode = document.getElementById('outputCode');
         if (!outputCode || !outputCode.value.trim()) {
               showNotification('No code to download', 'error');
               return;
         }
         
         const language = document.querySelector('input[name="language"]:checked').value;
         const filename = `formatted-code.${language === 'html' ? 'html' : language === 'css' ? 'css' : 'js'}`;
         const blob = new Blob([outputCode.value], { type: 'text/plain' });
         const url = URL.createObjectURL(blob);
         const a = document.createElement('a');
         a.href = url;
         a.download = filename;
         document.body.appendChild(a);
         a.click();
         document.body.removeChild(a);
         URL.revokeObjectURL(url);
         
         showNotification('Code downloaded successfully!');
      }
      
      function newCode() {
         // Redirect to fresh form
         window.location.href = 'code_formatter.php?clear=1';
      }
      
      function showNotification(message, type = 'success') {
         const notification = document.createElement('div');
         notification.style.cssText = `
               position: fixed;
               top: 20px;
               right: 20px;
               background: ${type === 'error' ? '#ff6b6b' : 'var(--primary)'};
               color: white;
               padding: 1rem 1.5rem;
               border-radius: 8px;
               box-shadow: 0 5px 15px rgba(0,0,0,0.2);
               z-index: 10000;
               animation: slideIn 0.3s ease;
               display: flex;
               align-items: center;
               gap: 0.5rem;
         `;
         notification.innerHTML = `
               <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'check-circle'}"></i>
               ${message}
         `;
         document.body.appendChild(notification);
         
         setTimeout(() => {
               notification.style.animation = 'slideOut 0.3s ease';
               setTimeout(() => notification.remove(), 300);
         }, 3000);
      }
      
      // Add CSS for animation
      const style = document.createElement('style');
      style.textContent = `
         @keyframes slideIn {
               from { transform: translateX(100%); opacity: 0; }
               to { transform: translateX(0); opacity: 1; }
         }
         @keyframes slideOut {
               from { transform: translateX(0); opacity: 1; }
               to { transform: translateX(100%); opacity: 0; }
         }
      `;
      document.head.appendChild(style);
      
      // Auto-resize textareas
      function autoResize(textarea) {
         textarea.style.height = 'auto';
         textarea.style.height = (textarea.scrollHeight) + 'px';
      }
      
      const inputTextarea = document.getElementById('inputCode');
      if (inputTextarea) {
         inputTextarea.addEventListener('input', function() {
               autoResize(this);
         });
         // Initial resize
         autoResize(inputTextarea);
      }
      
      const outputTextarea = document.getElementById('outputCode');
      if (outputTextarea) {
         outputTextarea.addEventListener('input', function() {
               autoResize(this);
         });
         // Initial resize
         autoResize(outputTextarea);
      }
   </script>
</body>
</html>