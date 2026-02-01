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
   <title>Background Gradient Generator - Code Library</title>
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
      .gradient-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .gradient-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .gradient-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .gradient-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .gradient-generator {
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
      .gradient-preview {
         width: 100%;
         height: 200px;
         border-radius: 12px;
         margin-bottom: 2rem;
         border: 2px solid var(--back-dark);
         background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
         transition: all 0.3s ease;
      }
      .gradient-controls {
         margin-bottom: 2rem;
      }
      .control-group {
         margin-bottom: 1.5rem;
      }
      .control-group label {
         display: block;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1rem;
      }
      .control-row {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 1rem;
      }
      .color-stops-container {
         margin-top: 1.5rem;
      }
      .color-stop {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 1rem;
         padding: 1rem;
         background: var(--back-light);
         border-radius: 8px;
         border: 1px solid var(--back-dark);
      }
      .color-picker {
         width: 50px;
         height: 40px;
         border: none;
         border-radius: 6px;
         cursor: pointer;
         padding: 0;
      }
      .color-slider {
         flex: 1;
         height: 6px;
         -webkit-appearance: none;
         appearance: none;
         background: var(--back-dark);
         border-radius: 3px;
         outline: none;
      }
      .color-slider::-webkit-slider-thumb {
         -webkit-appearance: none;
         appearance: none;
         width: 20px;
         height: 20px;
         border-radius: 50%;
         background: white;
         cursor: pointer;
         border: 2px solid var(--primary);
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      }
      .color-slider::-moz-range-thumb {
         width: 20px;
         height: 20px;
         border-radius: 50%;
         background: white;
         cursor: pointer;
         border: 2px solid var(--primary);
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      }
      .color-stop-value {
         font-weight: 600;
         color: var(--text-color);
         min-width: 40px;
         text-align: center;
      }
      .remove-color {
         background: #ff6b6b;
         color: white;
         border: none;
         width: 30px;
         height: 30px;
         border-radius: 50%;
         cursor: pointer;
         display: flex;
         align-items: center;
         justify-content: center;
         font-size: 1rem;
      }
      .add-color-btn {
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
      .add-color-btn:hover {
         background: var(--back-dark);
      }
      .angle-control {
         width: 100%;
         height: 6px;
         -webkit-appearance: none;
         appearance: none;
         background: linear-gradient(to right, #667eea, #764ba2);
         border-radius: 3px;
         outline: none;
         margin-top: 0.5rem;
      }
      .angle-control::-webkit-slider-thumb {
         -webkit-appearance: none;
         appearance: none;
         width: 24px;
         height: 24px;
         border-radius: 50%;
         background: white;
         cursor: pointer;
         border: 2px solid var(--primary);
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
      }
      .angle-control::-moz-range-thumb {
         width: 24px;
         height: 24px;
         border-radius: 50%;
         background: white;
         cursor: pointer;
         border: 2px solid var(--primary);
         box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
      }
      .angle-display {
         text-align: center;
         font-weight: 600;
         color: var(--text-color);
         margin-top: 0.5rem;
      }
      .preset-selector {
         margin-top: 2rem;
      }
      .preset-grid {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 1rem;
         margin-top: 1rem;
      }
      .preset-btn {
         height: 60px;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s;
         position: relative;
         overflow: hidden;
      }
      .preset-btn:hover {
         transform: translateY(-2px);
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
      }
      .preset-btn.active {
         border-color: var(--primary);
         box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
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
         max-height: 300px;
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
      .gradient-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .gradient-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .gradient-btn.copy {
         background: var(--primary);
         color: white;
      }
      .gradient-btn.copy:hover {
         background: var(--secondary);
      }
      .gradient-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .gradient-btn.reset:hover {
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
      .css-property {
         color: #569cd6;
      }
      .css-value {
         color: #ce9178;
      }
      .css-comment {
         color: #6a9955;
      }
      @media screen and (max-width: 1200px) {
         .gradient-container {
            padding: 2% 5% 5%;
         }
         .preset-grid {
            grid-template-columns: repeat(2, 1fr);
         }
      }
      @media screen and (max-width: 992px) {
         .gradient-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .gradient-container {
            padding: 2% 1rem 5%;
         }
         .gradient-header h1 {
            font-size: 2.2rem;
         }
         .gradient-actions {
            flex-direction: column;
         }
         .preset-grid {
            grid-template-columns: 1fr;
         }
         .control-row {
            flex-direction: column;
            align-items: stretch;
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
      <section class="gradient-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="gradient-header scroll-effect">
            <h1>Background Gradient Generator</h1>
            <p>Create beautiful CSS gradients with visual controls</p>
         </div>
         
         <div class="gradient-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Gradient Controls</h2>
               
               <div class="gradient-preview" id="gradientPreview"></div>
               
               <div class="gradient-controls">
                  <div class="control-group">
                     <label>Gradient Type</label>
                     <div class="control-row">
                        <button type="button" class="gradient-type-btn active" data-type="linear">Linear Gradient</button>
                        <button type="button" class="gradient-type-btn" data-type="radial">Radial Gradient</button>
                        <button type="button" class="gradient-type-btn" data-type="conic">Conic Gradient</button>
                     </div>
                  </div>
                  
                  <div class="control-group" id="angleControl">
                     <label>Gradient Angle: <span id="angleValue">135deg</span></label>
                     <input type="range" class="angle-control" id="angleSlider" min="0" max="360" value="135">
                  </div>
                  
                  <div class="color-stops-container">
                     <label>Color Stops</label>
                     <div id="colorStopsContainer">
                        <!-- Color stops will be added here dynamically -->
                     </div>
                     <button type="button" class="add-color-btn" onclick="addColorStop()">
                        <i class="fas fa-plus"></i> Add Color Stop
                     </button>
                  </div>
               </div>
               
               <div class="preset-selector">
                  <label>Quick Presets</label>
                  <div class="preset-grid" id="presetGrid">
                     <button type="button" class="preset-btn" data-preset="sunset" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="ocean" style="background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="forest" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="sunrise" style="background: linear-gradient(135deg, #ff6b6b 0%, #ffd166 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="cotton" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 50%, #36d1dc 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="magic" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="deep-space" style="background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="citrus" style="background: linear-gradient(135deg, #fdc830 0%, #f37335 100%);"></button>
                     <button type="button" class="preset-btn" data-preset="mint" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);"></button>
                  </div>
               </div>
               
               <div class="gradient-actions">
                  <button type="button" class="gradient-btn reset" onclick="resetGradient()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated CSS</h2>
               
               <div class="output-code" id="outputCode">
<span class="css-comment">/* Generated CSS Gradient */</span>
background: <span class="css-property">linear-gradient</span>(<span class="css-value">135deg</span>, <span class="css-value">#667eea 0%</span>, <span class="css-value">#764ba2 100%</span>);

<span class="css-comment">/* Cross-browser support */</span>
background: <span class="css-property">-webkit-linear-gradient</span>(<span class="css-value">135deg</span>, <span class="css-value">#667eea 0%</span>, <span class="css-value">#764ba2 100%</span>);
background: <span class="css-property">-moz-linear-gradient</span>(<span class="css-value">135deg</span>, <span class="css-value">#667eea 0%</span>, <span class="css-value">#764ba2 100%</span>);
               </div>
               
               <div class="gradient-actions">
                  <button type="button" class="gradient-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy CSS
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  CSS gradient copied to clipboard!
               </div>
               
               <div class="gradient-info" style="margin-top: 2rem; padding: 1rem; background: #f0f8ff; border-radius: 8px;">
                  <p><strong>How to use:</strong> Copy the CSS code and apply it to any element's background property. The code includes cross-browser support for better compatibility.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      let colorStops = [
         { color: '#667eea', position: 0 },
         { color: '#764ba2', position: 100 }
      ];
      let gradientType = 'linear';
      let angle = 135;
      let currentRawCss = '';
      
      document.addEventListener('DOMContentLoaded', function() {
         initializeGradient();
         updatePreview();
         updateCssOutput();
         
         // Gradient type buttons
         document.querySelectorAll('.gradient-type-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               document.querySelectorAll('.gradient-type-btn').forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               gradientType = this.getAttribute('data-type');
               updatePreview();
               updateCssOutput();
               
               // Show/hide angle control based on gradient type
               const angleControl = document.getElementById('angleControl');
               if (gradientType === 'radial' || gradientType === 'conic') {
                  angleControl.style.display = 'none';
               } else {
                  angleControl.style.display = 'block';
               }
            });
         });
         
         // Angle slider
         document.getElementById('angleSlider').addEventListener('input', function() {
            angle = parseInt(this.value);
            document.getElementById('angleValue').textContent = angle + 'deg';
            updatePreview();
            updateCssOutput();
         });
         
         // Preset buttons
         document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               applyPreset(this.getAttribute('data-preset'));
            });
         });
      });
      
      function initializeGradient() {
         const container = document.getElementById('colorStopsContainer');
         container.innerHTML = '';
         
         colorStops.forEach((stop, index) => {
            addColorStopElement(stop.color, stop.position, index);
         });
      }
      
      function addColorStop() {
         // Find the last position and add a new color stop after it
         const lastPosition = colorStops[colorStops.length - 1].position;
         const newPosition = Math.min(100, lastPosition + 20);
         const newColor = getRandomColor();
         
         colorStops.push({ color: newColor, position: newPosition });
         addColorStopElement(newColor, newPosition, colorStops.length - 1);
         updatePreview();
         updateCssOutput();
      }
      
      function addColorStopElement(color, position, index) {
         const container = document.getElementById('colorStopsContainer');
         
         const colorStopHtml = `
            <div class="color-stop" id="colorStop${index}">
               <input type="color" class="color-picker" value="${color}" data-index="${index}">
               <input type="range" class="color-slider" min="0" max="100" value="${position}" data-index="${index}">
               <span class="color-stop-value" id="position${index}">${position}%</span>
               <button type="button" class="remove-color" data-index="${index}">×</button>
            </div>
         `;
         
         if (container.children.length > index) {
            container.children[index].outerHTML = colorStopHtml;
         } else {
            container.insertAdjacentHTML('beforeend', colorStopHtml);
         }
         
         // Add event listeners
         const newStop = document.getElementById(`colorStop${index}`);
         const colorPicker = newStop.querySelector('.color-picker');
         const colorSlider = newStop.querySelector('.color-slider');
         const removeBtn = newStop.querySelector('.remove-color');
         
         colorPicker.addEventListener('input', function() {
            const idx = parseInt(this.getAttribute('data-index'));
            colorStops[idx].color = this.value;
            updatePreview();
            updateCssOutput();
         });
         
         colorSlider.addEventListener('input', function() {
            const idx = parseInt(this.getAttribute('data-index'));
            const position = parseInt(this.value);
            colorStops[idx].position = position;
            document.getElementById(`position${idx}`).textContent = position + '%';
            updatePreview();
            updateCssOutput();
         });
         
         removeBtn.addEventListener('click', function() {
            const idx = parseInt(this.getAttribute('data-index'));
            if (colorStops.length > 2) {
               colorStops.splice(idx, 1);
               // Reinitialize all color stops
               initializeGradient();
               updatePreview();
               updateCssOutput();
            } else {
               alert('You need at least 2 color stops for a gradient.');
            }
         });
      }
      
      function getRandomColor() {
         const letters = '0123456789ABCDEF';
         let color = '#';
         for (let i = 0; i < 6; i++) {
            color += letters[Math.floor(Math.random() * 16)];
         }
         return color;
      }
      
      function updatePreview() {
         const preview = document.getElementById('gradientPreview');
         let gradientString = '';
         
         switch(gradientType) {
            case 'linear':
               gradientString = `linear-gradient(${angle}deg, ${getColorStopsString()})`;
               break;
            case 'radial':
               gradientString = `radial-gradient(circle, ${getColorStopsString()})`;
               break;
            case 'conic':
               gradientString = `conic-gradient(from ${angle}deg, ${getColorStopsString()})`;
               break;
         }
         
         preview.style.background = gradientString;
      }
      
      function getColorStopsString() {
         // Sort color stops by position
         const sortedStops = [...colorStops].sort((a, b) => a.position - b.position);
         return sortedStops.map(stop => `${stop.color} ${stop.position}%`).join(', ');
      }
      
      function applyPreset(presetName) {
         const presets = {
            sunset: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#667eea', position: 0 },
                  { color: '#764ba2', position: 100 }
               ]
            },
            ocean: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#36d1dc', position: 0 },
                  { color: '#5b86e5', position: 100 }
               ]
            },
            forest: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#11998e', position: 0 },
                  { color: '#38ef7d', position: 100 }
               ]
            },
            sunrise: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#ff6b6b', position: 0 },
                  { color: '#ffd166', position: 100 }
               ]
            },
            cotton: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#667eea', position: 0 },
                  { color: '#764ba2', position: 50 },
                  { color: '#36d1dc', position: 100 }
               ]
            },
            magic: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#f093fb', position: 0 },
                  { color: '#f5576c', position: 100 }
               ]
            },
            'deep-space': {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#0f0c29', position: 0 },
                  { color: '#302b63', position: 50 },
                  { color: '#24243e', position: 100 }
               ]
            },
            citrus: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#fdc830', position: 0 },
                  { color: '#f37335', position: 100 }
               ]
            },
            mint: {
               type: 'linear',
               angle: 135,
               stops: [
                  { color: '#43e97b', position: 0 },
                  { color: '#38f9d7', position: 100 }
               ]
            }
         };
         
         if (presets[presetName]) {
            const preset = presets[presetName];
            gradientType = preset.type;
            angle = preset.angle;
            colorStops = [...preset.stops];
            
            // Update UI
            document.querySelectorAll('.gradient-type-btn').forEach(b => b.classList.remove('active'));
            document.querySelector(`.gradient-type-btn[data-type="${gradientType}"]`).classList.add('active');
            
            document.getElementById('angleSlider').value = angle;
            document.getElementById('angleValue').textContent = angle + 'deg';
            
            initializeGradient();
            updatePreview();
            updateCssOutput();
         }
      }
      
      function updateCssOutput() {
         const sortedStops = [...colorStops].sort((a, b) => a.position - b.position);
         const colorStopsString = sortedStops.map(stop => `${stop.color} ${stop.position}%`).join(', ');
         
         let css = '';
         
         switch(gradientType) {
            case 'linear':
               css = `/* Modern CSS Gradient */\n`;
               css += `background: linear-gradient(${angle}deg, ${colorStopsString});\n\n`;
               css += `/* Cross-browser support */\n`;
               css += `background: -webkit-linear-gradient(${angle}deg, ${colorStopsString});\n`;
               css += `background: -moz-linear-gradient(${angle}deg, ${colorStopsString});\n`;
               css += `background: -o-linear-gradient(${angle}deg, ${colorStopsString});`;
               break;
               
            case 'radial':
               css = `/* Radial Gradient */\n`;
               css += `background: radial-gradient(circle, ${colorStopsString});\n\n`;
               css += `/* Cross-browser support */\n`;
               css += `background: -webkit-radial-gradient(circle, ${colorStopsString});\n`;
               css += `background: -moz-radial-gradient(circle, ${colorStopsString});\n`;
               css += `background: -o-radial-gradient(circle, ${colorStopsString});`;
               break;
               
            case 'conic':
               css = `/* Conic Gradient */\n`;
               css += `background: conic-gradient(from ${angle}deg, ${colorStopsString});\n\n`;
               css += `/* Cross-browser support */\n`;
               css += `background: -webkit-conic-gradient(from ${angle}deg, ${colorStopsString});\n`;
               css += `background: -moz-conic-gradient(from ${angle}deg, ${colorStopsString});`;
               break;
         }
         
         currentRawCss = css;
         
         // Add syntax highlighting
         const highlighted = css
            .replace(/\/\*.*?\*\//g, '<span class="css-comment">$&</span>')
            .replace(/(background):/g, '<span class="css-property">$1</span>:')
            .replace(/(linear-gradient|radial-gradient|conic-gradient|-webkit-linear-gradient|-moz-linear-gradient|-o-linear-gradient|-webkit-radial-gradient|-moz-radial-gradient|-o-radial-gradient|-webkit-conic-gradient|-moz-conic-gradient)\(/g, '<span class="css-property">$1</span>(')
            .replace(/(\d+deg)/g, '<span class="css-value">$1</span>')
            .replace(/(#[0-9a-fA-F]{6}|#[0-9a-fA-F]{3})/g, '<span class="css-value">$1</span>')
            .replace(/(\d+%)/g, '<span class="css-value">$1</span>');
         
         document.getElementById('outputCode').innerHTML = highlighted;
      }
      
      function resetGradient() {
         colorStops = [
            { color: '#667eea', position: 0 },
            { color: '#764ba2', position: 100 }
         ];
         gradientType = 'linear';
         angle = 135;
         
         // Reset UI
         document.querySelectorAll('.gradient-type-btn').forEach(b => b.classList.remove('active'));
         document.querySelector('.gradient-type-btn[data-type="linear"]').classList.add('active');
         
         document.getElementById('angleSlider').value = angle;
         document.getElementById('angleValue').textContent = angle + 'deg';
         
         document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
         
         initializeGradient();
         updatePreview();
         updateCssOutput();
      }
      
      function copyToClipboard() {
         if (currentRawCss) {
            const textarea = document.createElement('textarea');
            textarea.value = currentRawCss;
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
   </script>
</body>
</html>