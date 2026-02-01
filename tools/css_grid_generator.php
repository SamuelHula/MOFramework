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
   <title>CSS Grid Generator - Code Library</title>
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
      .grid-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .grid-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .grid-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .grid-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .grid-generator {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .control-section, .output-section {
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
      .range-group {
         display: flex;
         align-items: center;
         gap: 1rem;
      }
      .range-value {
         min-width: 40px;
         font-weight: 600;
         color: var(--primary);
      }
      input[type="range"] {
         flex: 1;
         height: 6px;
         -webkit-appearance: none;
         background: var(--back-dark);
         border-radius: 3px;
         outline: none;
      }
      input[type="range"]::-webkit-slider-thumb {
         -webkit-appearance: none;
         width: 20px;
         height: 20px;
         background: var(--primary);
         border-radius: 50%;
         cursor: pointer;
      }
      input[type="color"] {
         width: 60px;
         height: 40px;
         padding: 2px;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
      }
      .preview-container {
         background: #f8f9fa;
         border-radius: 12px;
         padding: 1.5rem;
         margin-bottom: 1.5rem;
         min-height: 300px;
         display: flex;
         align-items: center;
         justify-content: center;
         border: 2px solid var(--back-dark);
         overflow: hidden;
      }
      .grid-preview {
         width: 100%;
         height: 100%;
         display: grid;
         gap: 10px;
         padding: 10px;
      }
      .grid-item {
         background: var(--primary);
         border-radius: 6px;
         display: flex;
         align-items: center;
         justify-content: center;
         color: white;
         font-weight: 600;
         transition: all 0.3s;
         box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      }
      .grid-item:hover {
         transform: scale(1.05);
         box-shadow: 0 4px 8px rgba(0,0,0,0.2);
      }
      .grid-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .grid-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .grid-btn.generate {
         background: var(--primary);
         color: white;
      }
      .grid-btn.generate:hover {
         background: var(--secondary);
      }
      .grid-btn.copy {
         background: var(--secondary);
         color: white;
      }
      .grid-btn.copy:hover {
         background: var(--primary);
      }
      .grid-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .grid-btn.reset:hover {
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
      .dimension-controls {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 1rem;
         margin-bottom: 1.5rem;
      }
      .template-controls {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 1rem;
         margin-bottom: 1.5rem;
      }
      .template-btn {
         padding: 0.8rem;
         background: var(--back-light);
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         text-align: center;
      }
      .template-btn:hover {
         background: var(--back-dark);
      }
      .template-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      @media screen and (max-width: 1200px) {
         .grid-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 992px) {
         .grid-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .grid-container {
            padding: 2% 1rem 5%;
         }
         .grid-header h1 {
            font-size: 2.2rem;
         }
         .grid-actions {
            flex-direction: column;
         }
         .dimension-controls, .template-controls {
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
      <?php include './assets/nav_bar.php' ?>
   </header>
   
   <main id="main">
      <section class="grid-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="grid-header scroll-effect">
            <h1>CSS Grid Generator</h1>
            <p>Visualize and generate CSS Grid code with an interactive builder</p>
         </div>
         
         <div class="grid-generator scroll-effect">
            <div class="control-section">
               <h2 class="section-title">Grid Controls</h2>
               
               <div class="template-controls">
                  <button class="template-btn" data-rows="3" data-columns="3" data-gap="10">3×3 Grid</button>
                  <button class="template-btn" data-rows="4" data-columns="4" data-gap="15">4×4 Grid</button>
                  <button class="template-btn" data-rows="2" data-columns="5" data-gap="20">2×5 Grid</button>
               </div>
               
               <div class="dimension-controls">
                  <div class="form-group">
                     <label for="rows">Rows: <span id="rowsValue">3</span></label>
                     <div class="range-group">
                        <input type="range" id="rows" min="1" max="8" value="3">
                     </div>
                  </div>
                  
                  <div class="form-group">
                     <label for="columns">Columns: <span id="columnsValue">3</span></label>
                     <div class="range-group">
                        <input type="range" id="columns" min="1" max="8" value="3">
                     </div>
                  </div>
               </div>
               
               <div class="form-group">
                  <label for="gap">Grid Gap (px): <span id="gapValue">10</span></label>
                  <div class="range-group">
                     <input type="range" id="gap" min="0" max="30" value="10">
                  </div>
               </div>
               
               <div class="dimension-controls">
                  <div class="form-group">
                     <label for="justifyContent">Justify Content</label>
                     <select id="justifyContent">
                        <option value="start">Start</option>
                        <option value="end">End</option>
                        <option value="center" selected>Center</option>
                        <option value="stretch">Stretch</option>
                        <option value="space-between">Space Between</option>
                        <option value="space-around">Space Around</option>
                        <option value="space-evenly">Space Evenly</option>
                     </select>
                  </div>
                  
                  <div class="form-group">
                     <label for="alignContent">Align Content</label>
                     <select id="alignContent">
                        <option value="start">Start</option>
                        <option value="end">End</option>
                        <option value="center" selected>Center</option>
                        <option value="stretch">Stretch</option>
                        <option value="space-between">Space Between</option>
                        <option value="space-around">Space Around</option>
                        <option value="space-evenly">Space Evenly</option>
                     </select>
                  </div>
               </div>
               
               <div class="form-group">
                  <label for="itemColor">Grid Item Color</label>
                  <input type="color" id="itemColor" value="#30BCED">
               </div>
               
               <div class="form-group">
                  <label for="containerWidth">Container Width (%)</label>
                  <div class="range-group">
                     <input type="range" id="containerWidth" min="50" max="100" value="80">
                     <span class="range-value" id="containerWidthValue">80%</span>
                  </div>
               </div>
               
               <div class="grid-actions">
                  <button class="grid-btn generate" onclick="generateGrid()">
                     <i class="fas fa-sync-alt"></i> Generate Grid
                  </button>
                  <button class="grid-btn reset" onclick="resetControls()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Preview & Code</h2>
               
               <div class="preview-container">
                  <div class="grid-preview" id="gridPreview">
                     <!-- Grid items will be generated here -->
                  </div>
               </div>
               
               <div class="output-code" id="outputCode">
/* Generated CSS Grid code will appear here */
               </div>
               
               <div class="grid-actions">
                  <button class="grid-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy CSS Code
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  CSS code copied to clipboard!
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         // Initialize with default grid
         generateGrid();
         
         // Update value displays
         document.getElementById('rows').addEventListener('input', function() {
            document.getElementById('rowsValue').textContent = this.value;
         });
         
         document.getElementById('columns').addEventListener('input', function() {
            document.getElementById('columnsValue').textContent = this.value;
         });
         
         document.getElementById('gap').addEventListener('input', function() {
            document.getElementById('gapValue').textContent = this.value + 'px';
         });
         
         document.getElementById('containerWidth').addEventListener('input', function() {
            document.getElementById('containerWidthValue').textContent = this.value + '%';
         });
         
         // Template buttons
         const templateBtns = document.querySelectorAll('.template-btn');
         templateBtns.forEach(btn => {
            btn.addEventListener('click', function() {
               const rows = this.getAttribute('data-rows');
               const columns = this.getAttribute('data-columns');
               const gap = this.getAttribute('data-gap');
               
               document.getElementById('rows').value = rows;
               document.getElementById('columns').value = columns;
               document.getElementById('gap').value = gap;
               
               document.getElementById('rowsValue').textContent = rows;
               document.getElementById('columnsValue').textContent = columns;
               document.getElementById('gapValue').textContent = gap + 'px';
               
               templateBtns.forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               
               generateGrid();
            });
         });
      });
      
      function generateGrid() {
         const rows = parseInt(document.getElementById('rows').value);
         const columns = parseInt(document.getElementById('columns').value);
         const gap = parseInt(document.getElementById('gap').value);
         const justifyContent = document.getElementById('justifyContent').value;
         const alignContent = document.getElementById('alignContent').value;
         const itemColor = document.getElementById('itemColor').value;
         const containerWidth = document.getElementById('containerWidth').value;
         
         // Generate preview grid
         const gridPreview = document.getElementById('gridPreview');
         gridPreview.innerHTML = '';
         
         // Set grid styles
         gridPreview.style.gridTemplateRows = `repeat(${rows}, 1fr)`;
         gridPreview.style.gridTemplateColumns = `repeat(${columns}, 1fr)`;
         gridPreview.style.gap = `${gap}px`;
         gridPreview.style.justifyContent = justifyContent;
         gridPreview.style.alignContent = alignContent;
         gridPreview.style.width = `${containerWidth}%`;
         
         // Create grid items
         const totalItems = rows * columns;
         for (let i = 1; i <= totalItems; i++) {
            const gridItem = document.createElement('div');
            gridItem.className = 'grid-item';
            gridItem.textContent = i;
            gridItem.style.backgroundColor = itemColor;
            gridPreview.appendChild(gridItem);
         }
         
         // Generate CSS code
         generateCSSCode(rows, columns, gap, justifyContent, alignContent, itemColor);
      }
      
      function generateCSSCode(rows, columns, gap, justifyContent, alignContent, itemColor) {
         const cssCode = `.grid-container {
  display: grid;
  grid-template-rows: repeat(${rows}, 1fr);
  grid-template-columns: repeat(${columns}, 1fr);
  gap: ${gap}px;
  justify-content: ${justifyContent};
  align-content: ${alignContent};
  width: 100%;
  height: 300px; /* Adjust as needed */
}

.grid-item {
  background-color: ${itemColor};
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  font-weight: bold;
  transition: all 0.3s ease;
}

.grid-item:hover {
  transform: scale(1.05);
  box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* HTML structure example:
<div class="grid-container">
  <div class="grid-item">1</div>
  <div class="grid-item">2</div>
  <!-- ... more items -->
</div>
*/`;
         
         document.getElementById('outputCode').textContent = cssCode;
      }
      
      function resetControls() {
         document.getElementById('rows').value = 3;
         document.getElementById('columns').value = 3;
         document.getElementById('gap').value = 10;
         document.getElementById('justifyContent').value = 'center';
         document.getElementById('alignContent').value = 'center';
         document.getElementById('itemColor').value = '#30BCED';
         document.getElementById('containerWidth').value = 80;
         
         document.getElementById('rowsValue').textContent = '3';
         document.getElementById('columnsValue').textContent = '3';
         document.getElementById('gapValue').textContent = '10px';
         document.getElementById('containerWidthValue').textContent = '80%';
         
         const templateBtns = document.querySelectorAll('.template-btn');
         templateBtns.forEach(btn => btn.classList.remove('active'));
         
         generateGrid();
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