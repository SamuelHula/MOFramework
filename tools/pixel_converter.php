<?php
require_once '../assets/config.php';

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
   header("Location: signin.php");
   exit;
}

$current_page = 'web_tools';

// Default values
$base_font_size = 16;
$pixel_value = '';
$rem_value = '';
$em_value = '';
$conversion_type = 'px_to_rem';
$conversion_result = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $base_font_size = floatval($_POST['base_font_size']);
   $conversion_type = $_POST['conversion_type'];
   
   // Validate base font size
   if ($base_font_size <= 0) {
      $base_font_size = 16;
   }
   
   if ($conversion_type === 'px_to_rem') {
      $pixel_value = floatval($_POST['pixel_value']);
      if ($pixel_value > 0) {
         $rem_value = $pixel_value / $base_font_size;
         $em_value = $rem_value; // EM equals REM when parent font-size is same as root
         $conversion_result = [
               'type' => 'px_to_rem',
               'pixels' => $pixel_value,
               'rem' => round($rem_value, 4),
               'em' => round($em_value, 4),
               'base' => $base_font_size
         ];
      }
   } elseif ($conversion_type === 'rem_to_px') {
      $rem_value = floatval($_POST['rem_value']);
      if ($rem_value > 0) {
         $pixel_value = $rem_value * $base_font_size;
         $em_value = $rem_value; // EM equals REM when parent font-size is same as root
         $conversion_result = [
               'type' => 'rem_to_px',
               'rem' => $rem_value,
               'pixels' => round($pixel_value, 2),
               'em' => round($em_value, 4),
               'base' => $base_font_size
         ];
      }
   } elseif ($conversion_type === 'em_to_px') {
      $em_value = floatval($_POST['em_value']);
      if ($em_value > 0) {
         $pixel_value = $em_value * $base_font_size;
         $rem_value = $em_value; // REM equals EM when root font-size is same as parent
         $conversion_result = [
               'type' => 'em_to_px',
               'em' => $em_value,
               'pixels' => round($pixel_value, 2),
               'rem' => round($rem_value, 4),
               'base' => $base_font_size
         ];
      }
   }
}

// Common conversions for quick reference
$common_conversions = [
   ['px' => 1, 'rem' => 0.0625, 'em' => 0.0625],
   ['px' => 2, 'rem' => 0.125, 'em' => 0.125],
   ['px' => 4, 'rem' => 0.25, 'em' => 0.25],
   ['px' => 8, 'rem' => 0.5, 'em' => 0.5],
   ['px' => 12, 'rem' => 0.75, 'em' => 0.75],
   ['px' => 14, 'rem' => 0.875, 'em' => 0.875],
   ['px' => 16, 'rem' => 1, 'em' => 1],
   ['px' => 18, 'rem' => 1.125, 'em' => 1.125],
   ['px' => 20, 'rem' => 1.25, 'em' => 1.25],
   ['px' => 24, 'rem' => 1.5, 'em' => 1.5],
   ['px' => 32, 'rem' => 2, 'em' => 2],
   ['px' => 48, 'rem' => 3, 'em' => 3],
   ['px' => 64, 'rem' => 4, 'em' => 4],
   ['px' => 96, 'rem' => 6, 'em' => 6],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Pixel to REM/EM Converter - Code Library</title>
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
      #header {
         height: 10vh;
      }
      .converter-container {
         min-height: 100vh;
         padding: 2% 5% 5%;
         position: relative;
         z-index: 2;
      }
      .converter-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .converter-header h1 {
         font-size: 2.8rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .converter-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .converter-card {
         background: white;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         padding: 2.5rem;
         margin-bottom: 2rem;
      }
      .converter-form {
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
      .form-row {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 1.5rem;
      }
      .number-input, select {
         padding: 0.8rem 1rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
         font-family: var(--text_font);
      }
      .number-input:focus, select:focus {
         outline: none;
         border-color: var(--primary);
      }
      .conversion-type {
         display: flex;
         gap: 1.5rem;
         flex-wrap: wrap;
         margin: 1rem 0;
      }
      .conversion-option {
         display: flex;
         align-items: center;
         gap: 0.5rem;
         cursor: pointer;
      }
      .conversion-option input {
         width: 18px;
         height: 18px;
      }
      .result-card {
         background: #f8f9fa;
         border-radius: 10px;
         padding: 2rem;
         margin-top: 2rem;
         border-left: 4px solid var(--primary);
         animation: fadeIn 0.5s ease;
      }
      .result-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1.5rem;
      }
      .result-header h3 {
         color: var(--text-color);
         font-size: 1.3rem;
      }
      .result-values {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
         gap: 1.5rem;
         margin-top: 1.5rem;
      }
      .result-value {
         background: white;
         padding: 1.5rem;
         border-radius: 8px;
         text-align: center;
         box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      }
      .result-label {
         font-size: 0.9rem;
         color: #666;
         margin-bottom: 0.5rem;
         text-transform: uppercase;
         letter-spacing: 1px;
      }
      .result-number {
         font-size: 2rem;
         font-weight: 700;
         color: var(--primary);
         font-family: 'Monaco', 'Menlo', monospace;
      }
      .result-unit {
         font-size: 1rem;
         color: #666;
         margin-left: 0.5rem;
         font-weight: 400;
      }
      .form-actions {
         display: flex;
         gap: 1rem;
         flex-wrap: wrap;
         margin-top: 2rem;
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
      .common-conversions {
         margin-top: 3rem;
      }
      .common-conversions h3 {
         color: var(--text-color);
         margin-bottom: 1.5rem;
         font-size: 1.3rem;
      }
      .conversions-table {
         width: 100%;
         border-collapse: collapse;
         background: white;
         border-radius: 8px;
         overflow: hidden;
         box-shadow: 0 2px 8px rgba(0,0,0,0.05);
      }
      .conversions-table th {
         background: var(--primary);
         color: white;
         padding: 1rem;
         text-align: center;
         font-weight: 600;
      }
      .conversions-table td {
         padding: 0.8rem;
         text-align: center;
         border-bottom: 1px solid var(--back-dark);
      }
      .conversions-table tr:hover {
         background: var(--back-light);
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
      @keyframes fadeIn {
         from { opacity: 0; transform: translateY(10px); }
         to { opacity: 1; transform: translateY(0); }
      }
      @media screen and (max-width: 768px) {
         .converter-container {
               padding: 2% 1rem 5%;
         }
         .converter-header h1 {
               font-size: 2.2rem;
         }
         .converter-card, .tool-info {
               padding: 1.5rem;
         }
         .form-actions {
               flex-direction: column;
         }
         .form-btn {
               width: 100%;
               justify-content: center;
         }
         .form-row {
               grid-template-columns: 1fr;
         }
         .result-values {
               grid-template-columns: 1fr;
         }
         .conversions-table {
               font-size: 0.9rem;
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
      <section class="converter-container">
         <div class="floating-balls">
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
               <div class="ball"></div>
         </div>
         
         <div class="converter-header scroll-effect">
               <h1>Pixel to REM/EM Converter</h1>
               <p>Convert between pixels, REM, and EM units for responsive web design</p>
         </div>
         
         <div class="converter-card scroll-effect">
               <form method="POST" class="converter-form" id="converterForm">
                  <div class="form-group">
                     <label>Base Font Size (px):</label>
                     <input type="number" id="base_font_size" name="base_font_size" 
                              class="number-input" min="1" max="100" step="0.5" 
                              value="<?php echo $base_font_size; ?>" required>
                     <small style="color: #666; font-size: 0.9rem;">
                           Standard base font size is 16px. This is the root font-size used for REM calculations.
                     </small>
                  </div>
                  
                  <div class="form-group">
                     <label>Conversion Type:</label>
                     <div class="conversion-type">
                           <label class="conversion-option">
                              <input type="radio" name="conversion_type" value="px_to_rem" 
                                    <?php echo ($conversion_type === 'px_to_rem') ? 'checked' : ''; ?>>
                              Pixels to REM/EM
                           </label>
                           <label class="conversion-option">
                              <input type="radio" name="conversion_type" value="rem_to_px"
                                    <?php echo ($conversion_type === 'rem_to_px') ? 'checked' : ''; ?>>
                              REM to Pixels
                           </label>
                           <label class="conversion-option">
                              <input type="radio" name="conversion_type" value="em_to_px"
                                    <?php echo ($conversion_type === 'em_to_px') ? 'checked' : ''; ?>>
                              EM to Pixels
                           </label>
                     </div>
                  </div>
                  
                  <div id="inputSection">
                     <?php if ($conversion_type === 'px_to_rem'): ?>
                     <div class="form-group">
                           <label for="pixel_value">Pixels (px):</label>
                           <input type="number" id="pixel_value" name="pixel_value" 
                                 class="number-input" min="0" step="0.5" 
                                 value="<?php echo $pixel_value; ?>" required>
                     </div>
                     <?php elseif ($conversion_type === 'rem_to_px'): ?>
                     <div class="form-group">
                           <label for="rem_value">REM:</label>
                           <input type="number" id="rem_value" name="rem_value" 
                                 class="number-input" min="0" step="0.01" 
                                 value="<?php echo $rem_value; ?>" required>
                     </div>
                     <?php elseif ($conversion_type === 'em_to_px'): ?>
                     <div class="form-group">
                           <label for="em_value">EM:</label>
                           <input type="number" id="em_value" name="em_value" 
                                 class="number-input" min="0" step="0.01" 
                                 value="<?php echo $em_value; ?>" required>
                     </div>
                     <?php endif; ?>
                  </div>
                  
                  <div class="form-actions">
                     <button type="submit" class="form-btn btn-primary">
                           <i class="fas fa-calculator"></i> Convert
                     </button>
                     
                     <a href="pixel_converter.php" class="form-btn btn-secondary">
                           <i class="fas fa-redo"></i> Reset
                     </a>
                     
                     <a href="web_tools.php" class="form-btn btn-secondary">
                           <i class="fas fa-arrow-left"></i> Back to Tools
                     </a>
                  </div>
                  
                  <?php if ($conversion_result): ?>
                  <div class="result-card">
                     <div class="result-header">
                           <h3>Conversion Result</h3>
                           <button type="button" class="copy-btn" onclick="copyResult()">
                              <i class="fas fa-copy"></i> Copy CSS
                           </button>
                     </div>
                     
                     <div class="result-values">
                           <?php if ($conversion_result['type'] === 'px_to_rem'): ?>
                           <div class="result-value">
                              <div class="result-label">Pixels</div>
                              <div class="result-number"><?php echo $conversion_result['pixels']; ?><span class="result-unit">px</span></div>
                           </div>
                           <div class="result-value">
                              <div class="result-label">REM</div>
                              <div class="result-number"><?php echo $conversion_result['rem']; ?><span class="result-unit">rem</span></div>
                           </div>
                           <div class="result-value">
                              <div class="result-label">EM</div>
                              <div class="result-number"><?php echo $conversion_result['em']; ?><span class="result-unit">em</span></div>
                           </div>
                           <?php elseif ($conversion_result['type'] === 'rem_to_px'): ?>
                           <div class="result-value">
                              <div class="result-label">REM</div>
                              <div class="result-number"><?php echo $conversion_result['rem']; ?><span class="result-unit">rem</span></div>
                           </div>
                           <div class="result-value">
                              <div class="result-label">Pixels</div>
                              <div class="result-number"><?php echo $conversion_result['pixels']; ?><span class="result-unit">px</span></div>
                           </div>
                           <div class="result-value">
                              <div class="result-label">EM</div>
                              <div class="result-number"><?php echo $conversion_result['em']; ?><span class="result-unit">em</span></div>
                           </div>
                           <?php elseif ($conversion_result['type'] === 'em_to_px'): ?>
                           <div class="result-value">
                              <div class="result-label">EM</div>
                              <div class="result-number"><?php echo $conversion_result['em']; ?><span class="result-unit">em</span></div>
                           </div>
                           <div class="result-value">
                              <div class="result-label">Pixels</div>
                              <div class="result-number"><?php echo $conversion_result['pixels']; ?><span class="result-unit">px</span></div>
                           </div>
                           <div class="result-value">
                              <div class="result-label">REM</div>
                              <div class="result-number"><?php echo $conversion_result['rem']; ?><span class="result-unit">rem</span></div>
                           </div>
                           <?php endif; ?>
                     </div>
                     
                     <div style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--back-dark);">
                           <div style="font-size: 0.9rem; color: #666;">
                              <i class="fas fa-info-circle"></i>
                              Base font size: <strong><?php echo $conversion_result['base']; ?>px</strong>
                           </div>
                           <div style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                              <i class="fas fa-code"></i>
                              Formula: 
                              <?php if ($conversion_result['type'] === 'px_to_rem'): ?>
                              <code>REM = Pixels / <?php echo $conversion_result['base']; ?></code>
                              <?php elseif ($conversion_result['type'] === 'rem_to_px'): ?>
                              <code>Pixels = REM × <?php echo $conversion_result['base']; ?></code>
                              <?php elseif ($conversion_result['type'] === 'em_to_px'): ?>
                              <code>Pixels = EM × <?php echo $conversion_result['base']; ?></code>
                              <?php endif; ?>
                           </div>
                     </div>
                  </div>
                  
                  <div class="form-actions">
                     <button type="button" class="form-btn btn-success" onclick="newConversion()">
                           <i class="fas fa-plus"></i> New Conversion
                     </button>
                     
                     <button type="button" class="form-btn btn-primary" onclick="swapConversion()">
                           <i class="fas fa-exchange-alt"></i> Swap Conversion
                     </button>
                  </div>
                  <?php endif; ?>
               </form>
               
               <div class="common-conversions">
                  <h3>Common Conversions (Base: 16px)</h3>
                  <table class="conversions-table">
                     <thead>
                           <tr>
                              <th>Pixels (px)</th>
                              <th>REM</th>
                              <th>EM</th>
                           </tr>
                     </thead>
                     <tbody>
                           <?php foreach ($common_conversions as $conversion): ?>
                           <tr>
                              <td><?php echo $conversion['px']; ?>px</td>
                              <td><?php echo $conversion['rem']; ?>rem</td>
                              <td><?php echo $conversion['em']; ?>em</td>
                           </tr>
                           <?php endforeach; ?>
                     </tbody>
                  </table>
               </div>
         </div>
         
         <div class="tool-info scroll-effect">
               <h2>About Pixel to REM/EM Converter</h2>
               <p>This tool helps you convert between pixel, REM, and EM units for responsive web design. REM units are relative to the root font-size, while EM units are relative to the parent element's font-size.</p>
               
               <div class="features-list">
                  <div class="feature-item">
                     <h4>Three-way Conversion</h4>
                     <p>Convert between pixels, REM, and EM units in any direction with customizable base font sizes.</p>
                  </div>
                  
                  <div class="feature-item">
                     <h4>Responsive Design</h4>
                     <p>Perfect for creating responsive layouts that scale properly across different screen sizes.</p>
                  </div>
                  
                  <div class="feature-item">
                     <h4>Common Conversions</h4>
                     <p>Quick reference table for common pixel to REM/EM conversions based on standard 16px base.</p>
                  </div>
                  
                  <div class="feature-item">
                     <h4>CSS Ready</h4>
                     <p>Copy generated CSS code directly to use in your stylesheets with proper units.</p>
                  </div>
               </div>
               
               <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary);">
                  <h4 style="color: var(--text-color); margin-bottom: 0.5rem;">How to Use:</h4>
                  <ol style="color: var(--text-color); opacity: 0.8; line-height: 1.6; padding-left: 1.2rem;">
                     <li>Set your base font size (default is 16px)</li>
                     <li>Choose the type of conversion you need</li>
                     <li>Enter the value you want to convert</li>
                     <li>Click "Convert" to see the results</li>
                     <li>Copy the CSS code for use in your project</li>
                  </ol>
               </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      // Update input section based on conversion type
      document.querySelectorAll('input[name="conversion_type"]').forEach(radio => {
         radio.addEventListener('change', function() {
               updateInputSection();
         });
      });
      
      function updateInputSection() {
         const conversionType = document.querySelector('input[name="conversion_type"]:checked').value;
         const inputSection = document.getElementById('inputSection');
         
         let html = '';
         switch (conversionType) {
               case 'px_to_rem':
                  html = `
                     <div class="form-group">
                           <label for="pixel_value">Pixels (px):</label>
                           <input type="number" id="pixel_value" name="pixel_value" 
                                 class="number-input" min="0" step="0.5" required>
                     </div>
                  `;
                  break;
               case 'rem_to_px':
                  html = `
                     <div class="form-group">
                           <label for="rem_value">REM:</label>
                           <input type="number" id="rem_value" name="rem_value" 
                                 class="number-input" min="0" step="0.01" required>
                     </div>
                  `;
                  break;
               case 'em_to_px':
                  html = `
                     <div class="form-group">
                           <label for="em_value">EM:</label>
                           <input type="number" id="em_value" name="em_value" 
                                 class="number-input" min="0" step="0.01" required>
                     </div>
                  `;
                  break;
         }
         
         inputSection.innerHTML = html;
      }
      
      function copyResult() {
         <?php if ($conversion_result): ?>
         let cssCode = '';
         <?php if ($conversion_result['type'] === 'px_to_rem'): ?>
         cssCode = `font-size: <?php echo $conversion_result['rem']; ?>rem; /* <?php echo $conversion_result['pixels']; ?>px / <?php echo $conversion_result['base']; ?>px */`;
         <?php elseif ($conversion_result['type'] === 'rem_to_px'): ?>
         cssCode = `font-size: <?php echo $conversion_result['pixels']; ?>px; /* <?php echo $conversion_result['rem']; ?>rem × <?php echo $conversion_result['base']; ?>px */`;
         <?php elseif ($conversion_result['type'] === 'em_to_px'): ?>
         cssCode = `font-size: <?php echo $conversion_result['pixels']; ?>px; /* <?php echo $conversion_result['em']; ?>em × <?php echo $conversion_result['base']; ?>px */`;
         <?php endif; ?>
         
         copyToClipboard(cssCode, 'CSS code copied to clipboard!');
         <?php endif; ?>
      }
      
      function copyToClipboard(text, message) {
         navigator.clipboard.writeText(text).then(() => {
               showNotification(message || 'Copied to clipboard!');
               
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
      
      function newConversion() {
         // Clear form and results
         document.getElementById('converterForm').reset();
         const resultCard = document.querySelector('.result-card');
         if (resultCard) {
               resultCard.remove();
         }
         const newButtons = document.querySelectorAll('.form-actions')[1];
         if (newButtons) {
               newButtons.remove();
         }
      }
      
      function swapConversion() {
         <?php if ($conversion_result): ?>
         let newType = '';
         let value = '';
         
         <?php if ($conversion_result['type'] === 'px_to_rem'): ?>
         newType = 'rem_to_px';
         value = <?php echo $conversion_result['rem']; ?>;
         <?php elseif ($conversion_result['type'] === 'rem_to_px'): ?>
         newType = 'px_to_rem';
         value = <?php echo $conversion_result['pixels']; ?>;
         <?php elseif ($conversion_result['type'] === 'em_to_px'): ?>
         newType = 'px_to_rem';
         value = <?php echo $conversion_result['pixels']; ?>;
         <?php endif; ?>
         
         // Set the new conversion type
         document.querySelector(`input[name="conversion_type"][value="${newType}"]`).checked = true;
         
         // Update the input field
         updateInputSection();
         
         // Set the value in the new input field
         setTimeout(() => {
               if (newType === 'rem_to_px') {
                  document.getElementById('rem_value').value = value;
               } else if (newType === 'px_to_rem') {
                  document.getElementById('pixel_value').value = value;
               }
               
               // Submit the form
               document.getElementById('converterForm').submit();
         }, 100);
         <?php endif; ?>
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
   </script>
</body>
</html>