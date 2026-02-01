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
   <title>SVG Wave Generator - Code Library</title>
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
      .wave-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .wave-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .wave-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .wave-header p {
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
      .control-group select, .color-inputs {
         width: 100%;
         padding: 0.8rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         font-size: 1rem;
         background: var(--back-light);
         color: var(--text-color);
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
      .wave-preview {
         background: var(--back-light);
         border-radius: 12px;
         padding: 2rem;
         text-align: center;
         min-height: 400px;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
         border: 2px dashed var(--back-dark);
      }
      #waveSvg {
         width: 100%;
         max-width: 600px;
         height: auto;
      }
      .wave-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .wave-btn {
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
      .wave-btn.primary {
         background: var(--primary);
         color: white;
      }
      .wave-btn.primary:hover {
         background: var(--secondary);
      }
      .wave-btn.secondary {
         background: transparent;
         color: var(--primary);
         border: 2px solid var(--primary);
      }
      .wave-btn.secondary:hover {
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
      .wave-types {
         display: grid;
         grid-template-columns: repeat(4, 1fr);
         gap: 0.5rem;
         margin-top: 0.5rem;
      }
      .wave-type-btn {
         padding: 0.5rem;
         border: 2px solid var(--back-dark);
         border-radius: 6px;
         background: white;
         cursor: pointer;
         transition: all 0.3s;
         text-align: center;
         font-size: 0.9rem;
      }
      .wave-type-btn:hover {
         border-color: var(--primary);
      }
      .wave-type-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      @media screen and (max-width: 1200px) {
         .wave-container {
            padding: 2% 5% 5%;
         }
         .generator-wrapper {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .wave-container {
            padding: 2% 1rem 5%;
         }
         .wave-header h1 {
            font-size: 2.2rem;
         }
         .wave-types {
            grid-template-columns: repeat(2, 1fr);
         }
         .wave-actions {
            flex-direction: column;
         }
         .wave-btn {
            width: 100%;
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
      <section class="wave-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="wave-header scroll-effect">
            <h1>SVG Wave Generator</h1>
            <p>Create beautiful SVG waves for your designs</p>
         </div>
         
         <div class="generator-wrapper scroll-effect">
            <div class="controls-section">
               <h2 class="section-title">Wave Controls</h2>
               
               <div class="control-group">
                  <label>
                     Wave Type
                     <span class="value-display" id="waveTypeValue">Smooth</span>
                  </label>
                  <div class="wave-types">
                     <button class="wave-type-btn active" data-type="smooth">Smooth</button>
                     <button class="wave-type-btn" data-type="sharp">Sharp</button>
                     <button class="wave-type-btn" data-type="complex">Complex</button>
                     <button class="wave-type-btn" data-type="random">Random</button>
                  </div>
               </div>
               
               <div class="control-group">
                  <label>
                     Wave Height: <span class="value-display" id="heightValue">60</span>px
                  </label>
                  <input type="range" id="heightControl" min="20" max="200" value="60" step="5">
               </div>
               
               <div class="control-group">
                  <label>
                     Wave Count: <span class="value-display" id="countValue">4</span>
                  </label>
                  <input type="range" id="countControl" min="1" max="8" value="4" step="1">
               </div>
               
               <div class="control-group">
                  <label>
                     Complexity: <span class="value-display" id="complexityValue">3</span>
                  </label>
                  <input type="range" id="complexityControl" min="1" max="5" value="3" step="1">
               </div>
               
               <div class="control-group">
                  <label>
                     Colors
                  </label>
                  <div class="color-inputs">
                     <div class="color-input">
                        <input type="color" id="color1" value="#30BCED">
                        <span>Primary</span>
                     </div>
                     <div class="color-input">
                        <input type="color" id="color2" value="#3066BE">
                        <span>Secondary</span>
                     </div>
                  </div>
               </div>
               
               <div class="control-group">
                  <label>
                     Wave Direction
                  </label>
                  <select id="directionControl">
                     <option value="up">Wave Up</option>
                     <option value="down">Wave Down</option>
                     <option value="both">Both Directions</option>
                  </select>
               </div>
               
               <div class="wave-actions">
                  <button class="wave-btn primary" onclick="generateWave()">
                     <i class="fas fa-sync-alt"></i> Generate Wave
                  </button>
                  <button class="wave-btn secondary" onclick="randomizeWave()">
                     <i class="fas fa-random"></i> Randomize
                  </button>
               </div>
            </div>
            
            <div class="preview-section">
               <h2 class="section-title">Preview</h2>
               
               <div class="wave-preview">
                  <svg id="waveSvg" viewBox="0 0 1200 400" xmlns="http://www.w3.org/2000/svg">
                     <!-- Wave will be generated here -->
                  </svg>
               </div>
               
               <div class="wave-actions">
                  <button class="wave-btn primary" onclick="downloadWave()">
                     <i class="fas fa-download"></i> Download SVG
                  </button>
                  <button class="wave-btn secondary" onclick="copyWaveCode()">
                     <i class="fas fa-copy"></i> Copy SVG Code
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  SVG code copied to clipboard!
               </div>
            </div>
         </div>
         
         <div class="code-output scroll-effect">
            <h3>SVG Code</h3>
            <pre id="svgCode"></pre>
         </div>
         
         <div class="info-box">
            <h3>About SVG Waves</h3>
            <p>SVG waves are resolution-independent vector graphics that can be scaled to any size without losing quality. They're perfect for website backgrounds, section dividers, and decorative elements. The generated SVG code can be directly embedded in your HTML or saved as an .svg file.</p>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      // Default wave parameters
      let currentWaveType = 'smooth';
      let waveHeight = 60;
      let waveCount = 4;
      let complexity = 3;
      let color1 = '#30BCED';
      let color2 = '#3066BE';
      let direction = 'up';
      
      // Initialize controls
      document.addEventListener('DOMContentLoaded', function() {
         // Set up event listeners
         document.getElementById('heightControl').addEventListener('input', updateHeight);
         document.getElementById('countControl').addEventListener('input', updateCount);
         document.getElementById('complexityControl').addEventListener('input', updateComplexity);
         document.getElementById('color1').addEventListener('input', updateColor1);
         document.getElementById('color2').addEventListener('input', updateColor2);
         document.getElementById('directionControl').addEventListener('change', updateDirection);
         
         // Wave type buttons
         document.querySelectorAll('.wave-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               document.querySelectorAll('.wave-type-btn').forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               currentWaveType = this.dataset.type;
               document.getElementById('waveTypeValue').textContent = this.textContent;
               generateWave();
            });
         });
         
         // Generate initial wave
         generateWave();
      });
      
      function updateHeight(e) {
         waveHeight = parseInt(e.target.value);
         document.getElementById('heightValue').textContent = waveHeight;
         generateWave();
      }
      
      function updateCount(e) {
         waveCount = parseInt(e.target.value);
         document.getElementById('countValue').textContent = waveCount;
         generateWave();
      }
      
      function updateComplexity(e) {
         complexity = parseInt(e.target.value);
         document.getElementById('complexityValue').textContent = complexity;
         generateWave();
      }
      
      function updateColor1(e) {
         color1 = e.target.value;
         generateWave();
      }
      
      function updateColor2(e) {
         color2 = e.target.value;
         generateWave();
      }
      
      function updateDirection(e) {
         direction = e.target.value;
         generateWave();
      }
      
      function generateWave() {
         const svg = document.getElementById('waveSvg');
         const svgCode = document.getElementById('svgCode');
         
         // Clear SVG
         svg.innerHTML = '';
         
         // Create wave based on type
         let pathData;
         switch(currentWaveType) {
            case 'smooth':
               pathData = generateSmoothWave();
               break;
            case 'sharp':
               pathData = generateSharpWave();
               break;
            case 'complex':
               pathData = generateComplexWave();
               break;
            case 'random':
               pathData = generateRandomWave();
               break;
            default:
               pathData = generateSmoothWave();
         }
         
         // Create gradient
         const defs = document.createElementNS('http://www.w3.org/2000/svg', 'defs');
         const gradient = document.createElementNS('http://www.w3.org/2000/svg', 'linearGradient');
         gradient.setAttribute('id', 'waveGradient');
         gradient.setAttribute('x1', '0%');
         gradient.setAttribute('y1', '0%');
         gradient.setAttribute('x2', '100%');
         gradient.setAttribute('y2', '0%');
         
         const stop1 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
         stop1.setAttribute('offset', '0%');
         stop1.setAttribute('stop-color', color1);
         
         const stop2 = document.createElementNS('http://www.w3.org/2000/svg', 'stop');
         stop2.setAttribute('offset', '100%');
         stop2.setAttribute('stop-color', color2);
         
         gradient.appendChild(stop1);
         gradient.appendChild(stop2);
         defs.appendChild(gradient);
         svg.appendChild(defs);
         
         // Create wave path
         const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
         path.setAttribute('d', pathData);
         path.setAttribute('fill', 'url(#waveGradient)');
         
         svg.appendChild(path);
         
         // Generate and display code
         const code = `<svg viewBox="0 0 1200 400" xmlns="http://www.w3.org/2000/svg">
   <defs>
      <linearGradient id="waveGradient" x1="0%" y1="0%" x2="100%" y2="0%">
         <stop offset="0%" stop-color="${color1}" />
         <stop offset="100%" stop-color="${color2}" />
      </linearGradient>
   </defs>
   <path d="${pathData}" fill="url(#waveGradient)" />
</svg>`;
         
         svgCode.textContent = code;
      }
      
      function generateSmoothWave() {
         let path = '';
         const points = [];
         const step = 1200 / (waveCount * 2);
         
         // Starting point
         path += `M 0,200 `;
         
         for(let i = 0; i <= waveCount * 2; i++) {
            const x = i * step;
            const y = direction === 'down' ? 
                      (i % 2 === 0 ? 200 - waveHeight : 200 + waveHeight) :
                      (i % 2 === 0 ? 200 + waveHeight : 200 - waveHeight);
            
            if(i === 0) {
               path += `L ${x},${y} `;
            } else {
               const cp1x = (i-1) * step + step/2;
               const cp1y = y;
               const cp2x = cp1x;
               const cp2y = y;
               
               path += `C ${cp1x},${cp1y} ${cp2x},${cp2y} ${x},${y} `;
            }
         }
         
         // Close the path
         path += `L 1200,400 L 0,400 Z`;
         
         return path;
      }
      
      function generateSharpWave() {
         let path = '';
         const step = 1200 / waveCount;
         
         path += `M 0,200 `;
         
         for(let i = 0; i <= waveCount; i++) {
            const x = i * step;
            const midX = x + step/2;
            
            if(direction === 'down') {
               path += `L ${midX},${200 - waveHeight} `;
               path += `L ${x + step},200 `;
            } else {
               path += `L ${midX},${200 + waveHeight} `;
               path += `L ${x + step},200 `;
            }
         }
         
         path += `L 1200,400 L 0,400 Z`;
         
         return path;
      }
      
      function generateComplexWave() {
         let path = '';
         const segments = waveCount * complexity * 2;
         const step = 1200 / segments;
         
         path += `M 0,200 `;
         
         for(let i = 0; i <= segments; i++) {
            const x = i * step;
            const amplitude = waveHeight * (0.5 + Math.random() * 0.5);
            const y = direction === 'down' ? 
                      200 - amplitude * Math.sin(i * Math.PI / complexity) :
                      200 + amplitude * Math.sin(i * Math.PI / complexity);
            
            if(i === 0) {
               path += `L ${x},${y} `;
            } else {
               const cp1x = x - step/3;
               const cp1y = y;
               const cp2x = x + step/3;
               const cp2y = y;
               
               path += `C ${cp1x},${cp1y} ${cp2x},${cp2y} ${x},${y} `;
            }
         }
         
         path += `L 1200,400 L 0,400 Z`;
         
         return path;
      }
      
      function generateRandomWave() {
         let path = '';
         const segments = 20;
         const step = 1200 / segments;
         
         path += `M 0,200 `;
         
         for(let i = 0; i <= segments; i++) {
            const x = i * step;
            const y = 200 + (Math.random() - 0.5) * waveHeight * 2;
            
            path += `L ${x},${y} `;
         }
         
         path += `L 1200,400 L 0,400 Z`;
         
         return path;
      }
      
      function randomizeWave() {
         waveHeight = Math.floor(Math.random() * 180) + 20;
         waveCount = Math.floor(Math.random() * 7) + 1;
         complexity = Math.floor(Math.random() * 5) + 1;
         color1 = '#' + Math.floor(Math.random()*16777215).toString(16);
         color2 = '#' + Math.floor(Math.random()*16777215).toString(16);
         direction = ['up', 'down', 'both'][Math.floor(Math.random() * 3)];
         
         // Update controls
         document.getElementById('heightControl').value = waveHeight;
         document.getElementById('countControl').value = waveCount;
         document.getElementById('complexityControl').value = complexity;
         document.getElementById('color1').value = color1;
         document.getElementById('color2').value = color2;
         document.getElementById('directionControl').value = direction;
         
         // Update displays
         document.getElementById('heightValue').textContent = waveHeight;
         document.getElementById('countValue').textContent = waveCount;
         document.getElementById('complexityValue').textContent = complexity;
         
         generateWave();
      }
      
      function downloadWave() {
         const svg = document.getElementById('svgCode').textContent;
         const blob = new Blob([svg], { type: 'image/svg+xml' });
         const url = URL.createObjectURL(blob);
         
         const a = document.createElement('a');
         a.href = url;
         a.download = 'wave-' + Date.now() + '.svg';
         document.body.appendChild(a);
         a.click();
         document.body.removeChild(a);
         URL.revokeObjectURL(url);
      }
      
      function copyWaveCode() {
         const code = document.getElementById('svgCode').textContent;
         navigator.clipboard.writeText(code).then(() => {
            const successMsg = document.getElementById('copySuccess');
            successMsg.style.display = 'block';
            setTimeout(() => {
               successMsg.style.display = 'none';
            }, 3000);
         }).catch(err => {
            console.error('Failed to copy: ', err);
            alert('Failed to copy code to clipboard');
         });
      }
   </script>
</body>
</html>