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
   <title>Glassmorphism Generator - Code Library</title>
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
      .glass-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .glass-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .glass-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .glass-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .generator-wrapper {
         display: grid;
         grid-template-columns: 1fr 1.5fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .controls-section, .preview-section {
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
      .control-group {
         margin-bottom: 1.5rem;
      }
      .control-group label {
         display: flex;
         justify-content: space-between;
         align-items: center;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1rem;
      }
      .control-group .value-display {
         font-weight: normal;
         color: var(--primary);
      }
      .control-group input[type="range"] {
         width: 100%;
         height: 8px;
         border-radius: 4px;
         background: var(--back-dark);
         outline: none;
         -webkit-appearance: none;
      }
      .control-group input[type="range"]::-webkit-slider-thumb {
         -webkit-appearance: none;
         width: 20px;
         height: 20px;
         border-radius: 50%;
         background: var(--primary);
         cursor: pointer;
         border: 2px solid white;
         box-shadow: 0 2px 5px rgba(0,0,0,0.2);
      }
      .color-inputs {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 1rem;
         border: none;
         padding: 0;
      }
      .color-input {
         position: relative;
      }
      .color-input input {
         width: 100%;
         height: 45px;
         border-radius: 8px;
         border: 2px solid var(--back-dark);
         padding: 0;
         cursor: pointer;
      }
      .color-input span {
         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         color: white;
         font-weight: 600;
         text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
         pointer-events: none;
      }
      .glass-preview {
         min-height: 400px;
         border-radius: 20px;
         padding: 3rem;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         position: relative;
         overflow: hidden;
         border: 2px solid rgba(255, 255, 255, 0.3);
      }
      .glass-element {
         width: 300px;
         height: 200px;
         border-radius: 20px;
         padding: 2rem;
         display: flex;
         flex-direction: column;
         justify-content: center;
         align-items: center;
         text-align: center;
         backdrop-filter: blur(10px);
         -webkit-backdrop-filter: blur(10px);
         box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
         border: 1px solid rgba(255, 255, 255, 0.2);
      }
      .glass-element h3 {
         color: white;
         font-size: 1.5rem;
         margin-bottom: 1rem;
         text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
      }
      .glass-element p {
         color: rgba(255, 255, 255, 0.9);
         font-size: 0.9rem;
         line-height: 1.5;
      }
      .glass-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .glass-btn {
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
      .glass-btn.primary {
         background: var(--primary);
         color: white;
      }
      .glass-btn.primary:hover {
         background: var(--secondary);
      }
      .glass-btn.secondary {
         background: transparent;
         color: var(--primary);
         border: 2px solid var(--primary);
      }
      .glass-btn.secondary:hover {
         background: var(--primary);
         color: white;
      }
      .code-output {
         margin-top: 2rem;
      }
      .code-output h3 {
         font-size: 1.5rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      pre {
         background: #1e1e1e;
         color: #d4d4d4;
         padding: 1.5rem;
         border-radius: 8px;
         font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
         font-size: 0.9rem;
         line-height: 1.5;
         white-space: pre-wrap;
         word-wrap: break-word;
         overflow-x: auto;
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
      .example-buttons {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 0.5rem;
         margin-top: 0.5rem;
      }
      .example-btn {
         padding: 0.5rem;
         border: 2px solid var(--back-dark);
         border-radius: 6px;
         background: white;
         cursor: pointer;
         transition: all 0.3s;
         text-align: center;
         font-size: 0.9rem;
      }
      .example-btn:hover {
         border-color: var(--primary);
      }
      .example-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      @media screen and (max-width: 1200px) {
         .glass-container {
            padding: 2% 5% 5%;
         }
         .generator-wrapper {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .glass-container {
            padding: 2% 1rem 5%;
         }
         .glass-header h1 {
            font-size: 2.2rem;
         }
         .example-buttons {
            grid-template-columns: repeat(2, 1fr);
         }
         .glass-actions {
            flex-direction: column;
         }
         .glass-btn {
            width: 100%;
         }
         .glass-element {
            width: 250px;
            height: 180px;
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
      <section class="glass-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="glass-header scroll-effect">
            <h1>Glassmorphism Generator</h1>
            <p>Create stunning glass-like UI effects</p>
         </div>
         
         <div class="generator-wrapper scroll-effect">
            <div class="controls-section">
               <h2 class="section-title">Glass Controls</h2>
               
               <div class="control-group">
                  <label>
                     Quick Examples
                  </label>
                  <div class="example-buttons">
                     <button class="example-btn active" data-example="frosted">Frosted</button>
                     <button class="example-btn" data-example="crystal">Crystal</button>
                     <button class="example-btn" data-example="smoke">Smoke</button>
                     <button class="example-btn" data-example="prism">Prism</button>
                     <button class="example-btn" data-example="neon">Neon</button>
                     <button class="example-btn" data-example="custom">Custom</button>
                  </div>
               </div>
               
               <div class="control-group">
                  <label>
                     Blur Amount: <span class="value-display" id="blurValue">10</span>px
                  </label>
                  <input type="range" id="blurControl" min="0" max="50" value="10" step="1">
               </div>
               
               <div class="control-group">
                  <label>
                     Transparency: <span class="value-display" id="alphaValue">30</span>%
                  </label>
                  <input type="range" id="alphaControl" min="5" max="80" value="30" step="5">
               </div>
               
               <div class="control-group">
                  <label>
                     Border Width: <span class="value-display" id="borderValue">1</span>px
                  </label>
                  <input type="range" id="borderControl" min="0" max="10" value="1" step="0.5">
               </div>
               
               <div class="control-group">
                  <label>
                     Shadow Intensity: <span class="value-display" id="shadowValue">32</span>
                  </label>
                  <input type="range" id="shadowControl" min="0" max="100" value="32" step="1">
               </div>
               
               <div class="control-group">
                  <label>
                     Border Radius: <span class="value-display" id="radiusValue">20</span>px
                  </label>
                  <input type="range" id="radiusControl" min="0" max="50" value="20" step="1">
               </div>
               
               <div class="control-group">
                  <label>
                     Background Color
                  </label>
                  <div class="color-inputs">
                     <div class="color-input">
                        <input type="color" id="bgColor" value="#ffffff">
                        <span>Glass Color</span>
                     </div>
                     <div class="color-input">
                        <input type="color" id="borderColor" value="#ffffff">
                        <span>Border Color</span>
                     </div>
                  </div>
               </div>
               
               <div class="glass-actions">
                  <button class="glass-btn primary" onclick="updateGlassEffect()">
                     <i class="fas fa-sync-alt"></i> Update Effect
                  </button>
                  <button class="glass-btn secondary" onclick="randomizeGlass()">
                     <i class="fas fa-random"></i> Randomize
                  </button>
               </div>
            </div>
            
            <div class="preview-section">
               <h2 class="section-title">Preview</h2>
               
               <div class="glass-preview" id="glassPreview">
                  <div class="glass-element" id="glassElement">
                     <h3>Glass Effect</h3>
                     <p>This is a glassmorphism element with blur, transparency, and subtle borders.</p>
                  </div>
               </div>
               
               <div class="glass-actions">
                  <button class="glass-btn primary" onclick="copyCSSCode()">
                     <i class="fas fa-copy"></i> Copy CSS Code
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  CSS code copied to clipboard!
               </div>
            </div>
         </div>
         
         <div class="code-output scroll-effect">
            <h3>CSS Code</h3>
            <pre id="cssCode"></pre>
         </div>
         
         <div class="info-box">
            <h3>About Glassmorphism</h3>
            <p>Glassmorphism is a modern UI design trend that creates a glass-like effect using background blur, transparency, and subtle borders. It's achieved with CSS properties like backdrop-filter, rgba colors, and box-shadow. This effect works best on non-white backgrounds and creates depth in your designs.</p>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      // Default glass parameters
      let currentExample = 'frosted';
      let blurAmount = 10;
      let transparency = 30;
      let borderWidth = 1;
      let shadowIntensity = 32;
      let borderRadius = 20;
      let bgColor = '#ffffff';
      let borderColor = '#ffffff';
      
      // Example presets
      const presets = {
         frosted: { blur: 10, alpha: 30, border: 1, shadow: 32, radius: 20, bg: '#ffffff', border: '#ffffff' },
         crystal: { blur: 20, alpha: 15, border: 2, shadow: 20, radius: 25, bg: '#ffffff', border: '#ffffff' },
         smoke: { blur: 15, alpha: 40, border: 0, shadow: 40, radius: 15, bg: '#f0f0f0', border: '#f0f0f0' },
         prism: { blur: 25, alpha: 20, border: 3, shadow: 50, radius: 30, bg: '#ffffff', border: '#30BCED' },
         neon: { blur: 8, alpha: 50, border: 2, shadow: 60, radius: 10, bg: '#ffffff', border: '#3066BE' }
      };
      
      // Initialize controls
      document.addEventListener('DOMContentLoaded', function() {
         // Set up event listeners
         document.getElementById('blurControl').addEventListener('input', updateBlur);
         document.getElementById('alphaControl').addEventListener('input', updateAlpha);
         document.getElementById('borderControl').addEventListener('input', updateBorder);
         document.getElementById('shadowControl').addEventListener('input', updateShadow);
         document.getElementById('radiusControl').addEventListener('input', updateRadius);
         document.getElementById('bgColor').addEventListener('input', updateBgColor);
         document.getElementById('borderColor').addEventListener('input', updateBorderColor);
         
         // Example buttons
         document.querySelectorAll('.example-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               document.querySelectorAll('.example-btn').forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               currentExample = this.dataset.example;
               
               if (currentExample !== 'custom' && presets[currentExample]) {
                  loadPreset(presets[currentExample]);
               }
               
               updateGlassEffect();
            });
         });
         
         // Set background for preview area
         const preview = document.getElementById('glassPreview');
         preview.style.background = 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)';
         
         // Generate initial effect
         updateGlassEffect();
      });
      
      function updateBlur(e) {
         blurAmount = parseInt(e.target.value);
         document.getElementById('blurValue').textContent = blurAmount;
         updateGlassEffect();
      }
      
      function updateAlpha(e) {
         transparency = parseInt(e.target.value);
         document.getElementById('alphaValue').textContent = transparency;
         updateGlassEffect();
      }
      
      function updateBorder(e) {
         borderWidth = parseFloat(e.target.value);
         document.getElementById('borderValue').textContent = borderWidth;
         updateGlassEffect();
      }
      
      function updateShadow(e) {
         shadowIntensity = parseInt(e.target.value);
         document.getElementById('shadowValue').textContent = shadowIntensity;
         updateGlassEffect();
      }
      
      function updateRadius(e) {
         borderRadius = parseInt(e.target.value);
         document.getElementById('radiusValue').textContent = borderRadius;
         updateGlassEffect();
      }
      
      function updateBgColor(e) {
         bgColor = e.target.value;
         updateGlassEffect();
      }
      
      function updateBorderColor(e) {
         borderColor = e.target.value;
         updateGlassEffect();
      }
      
      function loadPreset(preset) {
         blurAmount = preset.blur;
         transparency = preset.alpha;
         borderWidth = preset.border;
         shadowIntensity = preset.shadow;
         borderRadius = preset.radius;
         bgColor = preset.bg;
         borderColor = preset.border;
         
         // Update controls
         document.getElementById('blurControl').value = blurAmount;
         document.getElementById('alphaControl').value = transparency;
         document.getElementById('borderControl').value = borderWidth;
         document.getElementById('shadowControl').value = shadowIntensity;
         document.getElementById('radiusControl').value = borderRadius;
         document.getElementById('bgColor').value = bgColor;
         document.getElementById('borderColor').value = borderColor;
         
         // Update displays
         document.getElementById('blurValue').textContent = blurAmount;
         document.getElementById('alphaValue').textContent = transparency;
         document.getElementById('borderValue').textContent = borderWidth;
         document.getElementById('shadowValue').textContent = shadowIntensity;
         document.getElementById('radiusValue').textContent = borderRadius;
      }
      
      function updateGlassEffect() {
         const glassElement = document.getElementById('glassElement');
         const cssCode = document.getElementById('cssCode');
         
         // Convert hex to rgba
         function hexToRgba(hex, alpha) {
            const r = parseInt(hex.slice(1, 3), 16);
            const g = parseInt(hex.slice(3, 5), 16);
            const b = parseInt(hex.slice(5, 7), 16);
            return `rgba(${r}, ${g}, ${b}, ${alpha / 100})`;
         }
         
         // Apply styles to element
         glassElement.style.backdropFilter = `blur(${blurAmount}px)`;
         glassElement.style.webkitBackdropFilter = `blur(${blurAmount}px)`;
         glassElement.style.backgroundColor = hexToRgba(bgColor, transparency);
         glassElement.style.border = `${borderWidth}px solid ${hexToRgba(borderColor, transparency * 0.8)}`;
         glassElement.style.borderRadius = `${borderRadius}px`;
         glassElement.style.boxShadow = `0 ${shadowIntensity/8}px ${shadowIntensity/2}px rgba(0, 0, 0, ${shadowIntensity/200})`;
         
         // Generate CSS code
         const css = `.glass-element {
   /* Glassmorphism Effect */
   backdrop-filter: blur(${blurAmount}px);
   -webkit-backdrop-filter: blur(${blurAmount}px);
   background-color: ${hexToRgba(bgColor, transparency)};
   border: ${borderWidth}px solid ${hexToRgba(borderColor, transparency * 0.8)};
   border-radius: ${borderRadius}px;
   box-shadow: 0 ${shadowIntensity/8}px ${shadowIntensity/2}px rgba(0, 0, 0, ${shadowIntensity/200});
   
   /* Optional additional styling */
   padding: 2rem;
   color: white;
   text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
}`;
         
         cssCode.textContent = css;
      }
      
      function randomizeGlass() {
         blurAmount = Math.floor(Math.random() * 30) + 5;
         transparency = Math.floor(Math.random() * 50) + 10;
         borderWidth = Math.random() * 5;
         shadowIntensity = Math.floor(Math.random() * 60) + 10;
         borderRadius = Math.floor(Math.random() * 40) + 5;
         bgColor = '#' + Math.floor(Math.random()*16777215).toString(16);
         borderColor = '#' + Math.floor(Math.random()*16777215).toString(16);
         
         // Update controls
         document.getElementById('blurControl').value = blurAmount;
         document.getElementById('alphaControl').value = transparency;
         document.getElementById('borderControl').value = borderWidth;
         document.getElementById('shadowControl').value = shadowIntensity;
         document.getElementById('radiusControl').value = borderRadius;
         document.getElementById('bgColor').value = bgColor;
         document.getElementById('borderColor').value = borderColor;
         
         // Update displays
         document.getElementById('blurValue').textContent = blurAmount;
         document.getElementById('alphaValue').textContent = transparency;
         document.getElementById('borderValue').textContent = borderWidth.toFixed(1);
         document.getElementById('shadowValue').textContent = shadowIntensity;
         document.getElementById('radiusValue').textContent = borderRadius;
         
         // Set to custom example
         document.querySelectorAll('.example-btn').forEach(b => b.classList.remove('active'));
         document.querySelector('.example-btn[data-example="custom"]').classList.add('active');
         currentExample = 'custom';
         
         updateGlassEffect();
      }
      
      function copyCSSCode() {
         const code = document.getElementById('cssCode').textContent;
         navigator.clipboard.writeText(code).then(() => {
            const successMsg = document.getElementById('copySuccess');
            successMsg.style.display = 'block';
            setTimeout(() => {
               successMsg.style.display = 'none';
            }, 3000);
         }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy CSS to clipboard');
         });
      }
   </script>
</body>
</html>