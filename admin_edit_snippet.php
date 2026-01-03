<?php
// admin_edit_snippet.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once './assets/config.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_loggedin']) || $_SESSION['admin_loggedin'] !== true) {
   header("Location: admin_signin.php");
   exit;
}

// Check if snippet ID is provided
if (!isset($_GET['id'])) {
   header("Location: admin_manage_snippets.php?error=No+snippet+ID+provided");
   exit;
}

$snippet_id = intval($_GET['id']);
$active_page = 'admin_edit_snippet';

// Fetch snippet data
try {
   $stmt = $pdo->prepare("SELECT s.*, c.name as category_name 
                         FROM snippets s 
                         LEFT JOIN categories c ON s.category_id = c.id 
                         WHERE s.id = ?");
   $stmt->execute([$snippet_id]);
   $snippet = $stmt->fetch(PDO::FETCH_ASSOC);
   
   if (!$snippet) {
      header("Location: admin_manage_snippets.php?error=Snippet+not+found");
      exit;
   }
} catch (PDOException $e) {
   error_log("Failed to fetch snippet: " . $e->getMessage());
   header("Location: admin_manage_snippets.php?error=Failed+to+load+snippet");
   exit;
}

// Fetch categories for dropdown
try {
   $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name");
   $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
   error_log("Failed to fetch categories: " . $e->getMessage());
   $categories = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Edit Code Snippet - Admin Panel</title>
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
      .btn-submit, .btn-cancel, .btn-delete {
         padding: 1rem 2rem;
         border-radius: 10px;
         font-size: 1.1rem;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s ease;
         border: 2px solid transparent;
         font-family: var(--subheading);
         text-decoration: none;
         text-align: center;
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
      .btn-delete {
         background: #f44336;
         color: white;
         margin-left: auto;
      }
      .btn-delete:hover {
         background: #d32f2f;
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
      
      /* Floating Balls Background */
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
               padding-top: 70px !important;
         }
         .admin-snippet-container {
               padding: 1rem;
         }
         .admin-header {
               padding: 1.5rem;
         }
         .admin-header h1 {
               font-size: 2rem;
         }
         .snippet-form-container {
               padding: 1.5rem;
         }
         .form-row {
               grid-template-columns: 1fr;
               gap: 1rem;
         }
         .form-group input,
         .form-group select,
         .form-group textarea {
               font-size: 16px;
               max-width: 100%;
         }
         .CodeMirror {
               height: 250px;
         }
         .code-editor-header {
               flex-direction: column;
               gap: 0.5rem;
               align-items: flex-start;
         }
         .language-selector,
         .editor-actions {
               width: 100%;
               justify-content: space-between;
         }
         .form-actions {
               flex-direction: column;
         }
         .btn-submit,
         .btn-cancel,
         .btn-delete {
               width: 100%;
               text-align: center;
         }
         .tag-input-container {
               min-height: auto;
         }
         .tag-input {
               min-width: 50px;
         }
      }

      @media screen and (max-width: 480px) {
         .admin-header {
               padding: 1rem;
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
         .checkbox-group {
               flex-direction: column;
               gap: 0.5rem;
         }
         .CodeMirror {
               height: 200px;
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
         .btn-cancel,
         .btn-delete {
            width: 100%;
            padding: 0.9rem;
            margin-left: 0;
         }
      }
      /* Extra small screens below 350px */
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
         .btn-cancel,
         .btn-delete {
            padding: 0.8rem;
            font-size: 1rem;
         }
      }
      .form-group {
      display: flex;
      flex-direction: column;
      position: relative; /* For better child containment */
      }

      /* Force all inputs to respect container boundaries */
      .form-group input,
      .form-group select,
      .form-group textarea {
      width: 100%;
      max-width: 100%;
      box-sizing: border-box; /* Include padding and border in width */
      }

      /* Specifically fix select elements on mobile */
      .form-group select {
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
      background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
      background-repeat: no-repeat;
      background-position: right 1rem center;
      background-size: 1em;
      padding-right: 2.5rem; /* Space for dropdown arrow */
      }

      /* Fix tag input container */
      .tag-input-container {
      width: 100%;
      max-width: 100%;
      overflow-x: auto; /* Allow horizontal scrolling if needed */
      -webkit-overflow-scrolling: touch;
      scrollbar-width: thin;
      }

      /* Hide scrollbar for tag container but keep functionality */
      .tag-input-container::-webkit-scrollbar {
      height: 4px;
      }

      .tag-input-container::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
      }

      .tag-input-container::-webkit-scrollbar-thumb {
      background: var(--primary);
      border-radius: 10px;
      }

      /* Ensure tags wrap properly */
      .tag {
      flex-shrink: 0; /* Prevent tags from shrinking */
      max-width: calc(100% - 2rem); /* Prevent tags from being too wide */
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      }

      /* Fix CodeMirror editor overflow */
      .code-editor-container {
      width: 100%;
      max-width: 100%;
      overflow: hidden;
      }

      .CodeMirror {
      max-width: 100%;
      overflow-x: auto !important;
      overflow-y: auto !important;
      }

      .CodeMirror-scroll {
      overflow-x: auto !important;
      overflow-y: auto !important;
      max-width: 100%;
      }

      /* Fix form row grid on very small screens */
      @media screen and (max-width: 480px) {
      .form-row {
         grid-template-columns: 1fr;
         gap: 1rem;
      }
      
      .form-group input,
      .form-group select,
      .form-group textarea {
         width: 100% !important;
         max-width: 100% !important;
         -webkit-appearance: none;
         -moz-appearance: none;
         appearance: none;
      }
      
      .code-editor-header {
         flex-direction: column;
         align-items: stretch;
         gap: 0.5rem;
      }
      
      .language-selector,
      .editor-actions {
         width: 100%;
         justify-content: space-between;
      }
      
      .editor-actions {
         margin-top: 0.5rem;
      }
      
      /* Prevent textarea from being too small */
      .form-group textarea {
         min-height: 120px;
      }
      }

      /* Extra fixes for very small screens below 350px */
      @media screen and (max-width: 350px) {
      .admin-snippet-container {
         padding: 0.5rem !important;
      }
      
      .snippet-form-container {
         padding: 0.75rem !important;
      }
      
      .form-group input,
      .form-group select,
      .form-group textarea {
         padding: 0.6rem 0.8rem !important;
         font-size: 14px !important;
      }
      
      .code-editor-header {
         padding: 0.5rem !important;
      }
      
      .code-editor-header select,
      .code-editor-header .editor-btn {
         padding: 0.4rem 0.6rem !important;
         font-size: 12px !important;
      }
      
      .CodeMirror {
         font-size: 11px !important;
      }
      
      .tag {
         font-size: 0.75rem;
         padding: 0.2rem 0.5rem;
      }
      
      .tag-input {
         font-size: 14px !important;
         min-width: 60px;
      }
      }

      /* Prevent form from being too wide on large screens */
      .snippet-form {
      max-width: 100%;
      overflow: hidden;
      }

      /* Ensure buttons don't overflow */
      .btn-submit,
      .btn-cancel,
      .btn-delete {
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
      }

      @media screen and (max-width: 480px) {
      .btn-submit,
      .btn-cancel,
      .btn-delete {
         white-space: normal;
         word-wrap: break-word;
      }
      }
   </style>
</head>
<body>
   <!-- Floating Balls Background -->
   <div class="floating-balls">
      <div class="ball auth-ball-1"></div>
      <div class="ball auth-ball-2"></div>
      <div class="ball auth-ball-3"></div>
      <div class="ball auth-ball-4"></div>
      <div class="ball auth-ball-5"></div>
      <div class="ball auth-ball-6"></div>
   </div>
   
   <?php 
   // Include admin navbar
   $admin_navbar_path = './includes/admin_navbar.php';
   if (file_exists($admin_navbar_path)) {
      include_once $admin_navbar_path;
   } else {
      // Fallback navbar if admin_navbar doesn't exist
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
               <h1>Edit Code Snippet</h1>
               <p>Update your code snippet</p>
         </div>
         
         <?php if (isset($_GET['success'])): ?>
            <div class="success-message" style="background: #e8f5e9; color: #2e7d32; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border-left: 4px solid #2e7d32;">
               <?php echo htmlspecialchars(urldecode($_GET['success'])); ?>
            </div>
         <?php endif; ?>
         
         <?php if (isset($_GET['error'])): ?>
            <div class="error-message" style="background: #ffebee; color: #c62828; padding: 1rem; border-radius: 10px; margin-bottom: 2rem; border-left: 4px solid #c62828;">
               <?php echo htmlspecialchars(urldecode($_GET['error'])); ?>
            </div>
         <?php endif; ?>
         
         <div class="snippet-form-container scroll-effect">
               <form class="snippet-form" id="snippetForm" action="./assets/process_edit_snippet.php" method="POST">
                  <input type="hidden" name="snippet_id" value="<?php echo $snippet['id']; ?>">
                  
                  <div class="form-row">
                     <div class="form-group">
                           <label for="title">Snippet Title *</label>
                           <input type="text" id="title" name="title" required 
                                  value="<?php echo htmlspecialchars($snippet['title']); ?>" 
                                  placeholder="e.g., Responsive Navbar with CSS Grid">
                     </div>
                     <div class="form-group">
                           <label for="language">Programming Language *</label>
                           <select id="language" name="language" required>
                              <option value="">Select Language</option>
                              <option value="html" <?php echo $snippet['language'] == 'html' ? 'selected' : ''; ?>>HTML</option>
                              <option value="css" <?php echo $snippet['language'] == 'css' ? 'selected' : ''; ?>>CSS</option>
                              <option value="javascript" <?php echo $snippet['language'] == 'javascript' ? 'selected' : ''; ?>>JavaScript</option>
                              <option value="php" <?php echo $snippet['language'] == 'php' ? 'selected' : ''; ?>>PHP</option>
                              <option value="python" <?php echo $snippet['language'] == 'python' ? 'selected' : ''; ?>>Python</option>
                              <option value="sql" <?php echo $snippet['language'] == 'sql' ? 'selected' : ''; ?>>SQL</option>
                              <option value="java" <?php echo $snippet['language'] == 'java' ? 'selected' : ''; ?>>Java</option>
                              <option value="csharp" <?php echo $snippet['language'] == 'csharp' ? 'selected' : ''; ?>>C#</option>
                              <option value="cpp" <?php echo $snippet['language'] == 'cpp' ? 'selected' : ''; ?>>C++</option>
                              <option value="ruby" <?php echo $snippet['language'] == 'ruby' ? 'selected' : ''; ?>>Ruby</option>
                           </select>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="description">Description *</label>
                     <textarea id="description" name="description" required placeholder="Describe what this code does..."><?php echo htmlspecialchars($snippet['description']); ?></textarea>
                  </div>
                  
                  <div class="form-group">
                     <label for="category_id">Category</label>
                     <select id="category_id" name="category_id">
                           <option value="">Select Category</option>
                           <?php foreach ($categories as $category) { ?>
                              <option value="<?php echo $category['id']; ?>" 
                                    <?php echo $snippet['category_id'] == $category['id'] ? 'selected' : ''; ?>>
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
                                       <option value="html" <?php echo $snippet['language'] == 'html' ? 'selected' : ''; ?>>HTML</option>
                                       <option value="css" <?php echo $snippet['language'] == 'css' ? 'selected' : ''; ?>>CSS</option>
                                       <option value="javascript" <?php echo $snippet['language'] == 'javascript' ? 'selected' : ''; ?>>JavaScript</option>
                                       <option value="php" <?php echo $snippet['language'] == 'php' ? 'selected' : ''; ?>>PHP</option>
                                       <option value="python" <?php echo $snippet['language'] == 'python' ? 'selected' : ''; ?>>Python</option>
                                       <option value="sql" <?php echo $snippet['language'] == 'sql' ? 'selected' : ''; ?>>SQL</option>
                                 </select>
                              </div>
                              <div class="editor-actions">
                                 <button type="button" class="editor-btn" onclick="formatCode()">Format</button>
                                 <button type="button" class="editor-btn" onclick="clearCode()">Clear</button>
                              </div>
                           </div>
                           <textarea id="code" name="code" style="display: none;"><?php echo htmlspecialchars($snippet['code']); ?></textarea>
                           <div id="codeEditor"></div>
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="tags">Tags</label>
                     <div class="tag-input-container" id="tagContainer">
                        <?php 
                        // Safely handle tags - ensure it's never null
                        $snippet['tags'] = $snippet['tags'] ?? '';
                        $tags = !empty($snippet['tags']) ? 
                                 array_filter(explode(',', $snippet['tags']), function($tag) {
                                    return trim($tag) !== '';
                                 }) : 
                                 [];
                        foreach ($tags as $tag):
                              $trimmed_tag = trim($tag);
                              if (!empty($trimmed_tag)):
                        ?>
                              <div class="tag">
                                 <?php echo htmlspecialchars($trimmed_tag); ?>
                                 <span class="tag-remove" onclick="removeTag('<?php echo htmlspecialchars($trimmed_tag); ?>')">&times;</span>
                              </div>
                        <?php 
                              endif;
                        endforeach; 
                        ?>
                        <input type="text" id="tagInput" class="tag-input" placeholder="Type a tag and press Enter">
                     </div>
                     <input type="hidden" id="tags" name="tags" value="<?php echo htmlspecialchars($snippet['tags'] ?? ''); ?>">
                  </div>
                  
                  <div class="checkbox-group">
                     <div class="checkbox-item">
                           <input type="checkbox" id="is_featured" name="is_featured" value="1" <?php echo $snippet['is_featured'] ? 'checked' : ''; ?>>
                           <label for="is_featured">Featured Snippet</label>
                     </div>
                     <div class="checkbox-item">
                           <input type="checkbox" id="is_public" name="is_public" value="1" <?php echo $snippet['is_public'] ? 'checked' : ''; ?>>
                           <label for="is_public">Public (Visible to all users)</label>
                     </div>
                  </div>
                  
                  <div class="form-actions">
                     <button type="submit" class="btn-submit">Update Snippet</button>
                     <a href="admin_manage_snippets.php" class="btn-cancel">Cancel</a>
                     <a href="./assets/process_delete_snippet.php?id=<?php echo $snippet['id']; ?>" 
                        class="btn-delete" 
                        onclick="return confirm('Are you sure you want to delete this snippet? This action cannot be undone.')">
                        Delete Snippet
                     </a>
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
   <script>
      // Initialize tags array from existing tags
      let tags = <?php echo json_encode($tags); ?>;
      
      // Initialize CodeMirror editor with existing code
      const codeEditor = CodeMirror(document.getElementById('codeEditor'), {
         mode: '<?php echo $snippet['language'] == 'html' ? 'htmlmixed' : $snippet['language']; ?>',
         theme: 'monokai',
         lineNumbers: true,
         indentUnit: 4,
         matchBrackets: true,
         autoCloseBrackets: true,
         lineWrapping: true,
         value: `<?php echo str_replace('`', '\`', $snippet['code']); ?>`
      });
      
      // Sync editor with form textarea
      codeEditor.on('change', function(editor) {
         document.getElementById('code').value = editor.getValue();
      });
      
      // Update editor mode based on language selection
      document.getElementById('editorLanguage').addEventListener('change', function() {
         const mode = this.value;
         const modeMap = {
               'html': 'htmlmixed',
               'css': 'css',
               'javascript': 'javascript',
               'php': 'php',
               'python': 'python',
               'sql': 'sql'
         };
         codeEditor.setOption('mode', modeMap[mode] || 'htmlmixed');
         document.getElementById('language').value = mode;
      });
      
      // Also update editor when main language select changes
      document.getElementById('language').addEventListener('change', function() {
         const mode = this.value;
         const modeMap = {
               'html': 'htmlmixed',
               'css': 'css',
               'javascript': 'javascript',
               'php': 'php',
               'python': 'python',
               'sql': 'sql'
         };
         if (modeMap[mode]) {
               codeEditor.setOption('mode', modeMap[mode]);
               document.getElementById('editorLanguage').value = mode;
         }
      });
      
      // Tag management
      const tagContainer = document.getElementById('tagContainer');
      const tagInput = document.getElementById('tagInput');
      const tagsHidden = document.getElementById('tags');
      
      // Update hidden tags field
      function updateTags() {
         tagsHidden.value = tags.join(',');
      }
      
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
      
      // Code formatting
      function formatCode() {
         const code = codeEditor.getValue();
         // Simple formatting - in production, use a proper formatter
         const formatted = code.replace(/\n\s*\n/g, '\n').trim();
         codeEditor.setValue(formatted);
      }
      
      function clearCode() {
         if (confirm('Are you sure you want to clear the code editor?')) {
               codeEditor.setValue('');
         }
      }
      
      // Form validation
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
      
      // Add scroll effect
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
         
         // Initial check
         handleScrollAnimation();
      });
   </script>
</body>
</html>