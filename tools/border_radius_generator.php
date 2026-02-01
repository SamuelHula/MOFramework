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
   <title>Border Radius Generator - Code Library</title>
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
      .border-container {
         min-height: 100vh;
         padding: 2% 5% 5%;
         position: relative;
      }
      .border-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .border-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .border-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .border-generator {
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
      .visual-editor {
         background: white;
         padding: 2rem;
         border-radius: 15px;
         box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
         margin-bottom: 2rem;
         height: 400px;
         position: relative;
         overflow: hidden;
      }
      .border-preview {
         width: 300px;
         height: 200px;
         background: linear-gradient(135deg, var(--primary), var(--secondary));
         position: absolute;
         top: 50%;
         left: 50%;
         transform: translate(-50%, -50%);
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
         transition: all 0.3s ease;
      }
      .control-points {
         position: absolute;
         width: 100%;
         height: 100%;
      }
      .control-point {
         position: absolute;
         width: 20px;
         height: 20px;
         background: white;
         border: 2px solid var(--primary);
         border-radius: 50%;
         cursor: grab;
         transform: translate(-50%, -50%);
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
         transition: all 0.2s;
      }
      .control-point:hover {
         transform: translate(-50%, -50%) scale(1.2);
         background: var(--primary);
      }
      .control-point.active {
         background: var(--secondary);
         border-color: var(--secondary);
      }
      .control-point:nth-child(1) { top: 0; left: 0; }
      .control-point:nth-child(2) { top: 0; left: 100%; }
      .control-point:nth-child(3) { top: 100%; left: 100%; }
      .control-point:nth-child(4) { top: 100%; left: 0; }
      .control-values {
         display: grid;
         grid-template-columns: repeat(4, 1fr);
         gap: 1rem;
         margin-bottom: 2rem;
      }
      .value-group {
         text-align: center;
      }
      .value-group label {
         display: block;
         font-weight: 600;
         margin-bottom: 0.5rem;
         color: var(--text-color);
         font-size: 0.9rem;
      }
      .value-input {
         width: 100%;
         padding: 0.8rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         text-align: center;
         font-size: 1.1rem;
         font-weight: 600;
         transition: all 0.3s;
         background: var(--back-light);
      }
      .value-input:focus {
         outline: none;
         border-color: var(--primary);
         background: white;
      }
      .slider-container {
         margin-bottom: 1.5rem;
      }
      .slider-container label {
         display: block;
         font-weight: 600;
         margin-bottom: 0.5rem;
         color: var(--text-color);
      }
      .slider {
         width: 100%;
         height: 6px;
         -webkit-appearance: none;
         appearance: none;
         background: var(--back-dark);
         border-radius: 3px;
         outline: none;
      }
      .slider::-webkit-slider-thumb {
         -webkit-appearance: none;
         appearance: none;
         width: 20px;
         height: 20px;
         border-radius: 50%;
         background: var(--primary);
         cursor: pointer;
         border: 2px solid white;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      }
      .slider::-moz-range-thumb {
         width: 20px;
         height: 20px;
         border-radius: 50%;
         background: var(--primary);
         cursor: pointer;
         border: 2px solid white;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      }
      .presets {
         margin-bottom: 2rem;
      }
      .preset-grid {
         display: grid;
         grid-template-columns: repeat(4, 1fr);
         gap: 0.8rem;
         margin-top: 1rem;
      }
      .preset-btn {
         padding: 0.8rem;
         background: var(--back-light);
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 0.9rem;
         font-weight: 600;
         display: flex;
         flex-direction: column;
         align-items: center;
         justify-content: center;
      }
      .preset-btn:hover {
         background: var(--back-dark);
      }
      .preset-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .preset-preview {
         width: 40px;
         height: 40px;
         background: var(--text-color);
         margin-bottom: 0.5rem;
         border-radius: 4px;
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
         max-height: 200px;
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
      .border-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .border-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .border-btn.copy {
         background: var(--primary);
         color: white;
      }
      .border-btn.copy:hover {
         background: var(--secondary);
      }
      .border-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .border-btn.reset:hover {
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
      @media screen and (max-width: 1200px) {
         .border-generator {
            grid-template-columns: 1fr;
         }
         .control-values {
            grid-template-columns: repeat(2, 1fr);
         }
         .preset-grid {
            grid-template-columns: repeat(3, 1fr);
         }
      }
      @media screen and (max-width: 768px) {
         .border-container {
            padding: 2% 1rem 5%;
         }
         .border-header h1 {
            font-size: 2.2rem;
         }
         .control-values {
            grid-template-columns: 1fr;
         }
         .preset-grid {
            grid-template-columns: repeat(2, 1fr);
         }
         .border-preview {
            width: 250px;
            height: 150px;
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
      <section class="border-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="border-header scroll-effect">
            <h1>Fancy Border Radius Generator</h1>
            <p>Create custom border-radius shapes with visual controls</p>
         </div>
         
         <div class="visual-editor scroll-effect">
            <div class="border-preview" id="borderPreview"></div>
            <div class="control-points">
               <div class="control-point" data-index="0"></div>
               <div class="control-point" data-index="1"></div>
               <div class="control-point" data-index="2"></div>
               <div class="control-point" data-index="3"></div>
            </div>
         </div>
         
         <div class="border-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">Radius Controls</h2>
               
               <div class="control-values">
                  <div class="value-group">
                     <label>Top Left</label>
                     <input type="text" class="value-input" id="topLeft" value="50" data-index="0">
                  </div>
                  <div class="value-group">
                     <label>Top Right</label>
                     <input type="text" class="value-input" id="topRight" value="50" data-index="1">
                  </div>
                  <div class="value-group">
                     <label>Bottom Right</label>
                     <input type="text" class="value-input" id="bottomRight" value="50" data-index="2">
                  </div>
                  <div class="value-group">
                     <label>Bottom Left</label>
                     <input type="text" class="value-input" id="bottomLeft" value="50" data-index="3">
                  </div>
               </div>
               
               <div class="slider-container">
                  <label>Overall Radius: <span id="radiusValue">50%</span></label>
                  <input type="range" class="slider" id="radiusSlider" min="0" max="100" value="50">
               </div>
               
               <div class="presets">
                  <label>Quick Presets:</label>
                  <div class="preset-grid">
                     <button class="preset-btn" data-values="0,0,0,0">
                        <div class="preset-preview" style="border-radius: 0;"></div>
                        Square
                     </button>
                     <button class="preset-btn" data-values="50,50,50,50">
                        <div class="preset-preset" style="border-radius: 50%;"></div>
                        Circle
                     </button>
                     <button class="preset-btn" data-values="20,20,20,20">
                        <div class="preset-preview" style="border-radius: 20%;"></div>
                        Rounded
                     </button>
                     <button class="preset-btn active" data-values="50,0,50,0">
                        <div class="preset-preview" style="border-radius: 50% 0 50% 0;"></div>
                        Diagonal
                     </button>
                     <button class="preset-btn" data-values="0,50,0,50">
                        <div class="preset-preview" style="border-radius: 0 50% 0 50%;"></div>
                        Opposite
                     </button>
                     <button class="preset-btn" data-values="30,70,30,70">
                        <div class="preset-preview" style="border-radius: 30% 70% 30% 70%;"></div>
                        Wave
                     </button>
                     <button class="preset-btn" data-values="70,30,70,30">
                        <div class="preset-preview" style="border-radius: 70% 30% 70% 30%;"></div>
                        Inverted
                     </button>
                     <button class="preset-btn" data-values="100,0,0,0">
                        <div class="preset-preview" style="border-radius: 100% 0 0 0;"></div>
                        Top Left
                     </button>
                  </div>
               </div>
               
               <div class="border-actions">
                  <button class="border-btn reset" onclick="resetControls()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated CSS</h2>
               
               <div class="output-code" id="outputCode">
/* Generated border-radius CSS */
border-radius: 50% 0 50% 0;
               </div>
               
               <div class="border-actions">
                  <button class="border-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy CSS
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  CSS copied to clipboard!
               </div>
               
               <div class="border-description" style="margin-top: 2rem; padding: 1rem; background: #f0f8ff; border-radius: 8px;">
                  <p><strong>How to use:</strong> Drag the control points or use the sliders to adjust the border radius. The CSS will update automatically.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include '../assets/footer.php' ?>
   
   <script src="../js/scroll.js"></script>
   <script src="../js/fly-in.js"></script>
   <script>
      document.addEventListener('DOMContentLoaded', function() {
         let activeControl = null;
         let isDragging = false;
         const preview = document.getElementById('borderPreview');
         const controlPoints = document.querySelectorAll('.control-point');
         const valueInputs = document.querySelectorAll('.value-input');
         const radiusSlider = document.getElementById('radiusSlider');
         const radiusValue = document.getElementById('radiusValue');
         const presetButtons = document.querySelectorAll('.preset-btn');
         
         // Initialize values
         const values = [50, 0, 50, 0];
         updatePreview();
         updateOutput();
         
         // Control point dragging
         controlPoints.forEach(point => {
            point.addEventListener('mousedown', function(e) {
               e.preventDefault();
               activeControl = this;
               isDragging = true;
               controlPoints.forEach(p => p.classList.remove('active'));
               this.classList.add('active');
               document.addEventListener('mousemove', handleDrag);
               document.addEventListener('mouseup', stopDrag);
            });
            
            point.addEventListener('touchstart', function(e) {
               e.preventDefault();
               activeControl = this;
               isDragging = true;
               controlPoints.forEach(p => p.classList.remove('active'));
               this.classList.add('active');
               document.addEventListener('touchmove', handleDragTouch);
               document.addEventListener('touchend', stopDrag);
            });
         });
         
         function handleDrag(e) {
            if (!isDragging || !activeControl) return;
            
            const rect = preview.getBoundingClientRect();
            const x = ((e.clientX - rect.left) / rect.width) * 100;
            const y = ((e.clientY - rect.top) / rect.height) * 100;
            
            updateControlPosition(activeControl, x, y);
         }
         
         function handleDragTouch(e) {
            if (!isDragging || !activeControl) return;
            
            const rect = preview.getBoundingClientRect();
            const touch = e.touches[0];
            const x = ((touch.clientX - rect.left) / rect.width) * 100;
            const y = ((touch.clientY - rect.top) / rect.height) * 100;
            
            updateControlPosition(activeControl, x, y);
         }
         
         function updateControlPosition(control, x, y) {
            const index = parseInt(control.getAttribute('data-index'));
            
            // Calculate radius value based on position
            let radius;
            switch(index) {
               case 0: // Top left
                  radius = Math.min(x, y);
                  break;
               case 1: // Top right
                  radius = Math.min(100 - x, y);
                  break;
               case 2: // Bottom right
                  radius = Math.min(100 - x, 100 - y);
                  break;
               case 3: // Bottom left
                  radius = Math.min(x, 100 - y);
                  break;
            }
            
            radius = Math.max(0, Math.min(100, Math.round(radius)));
            values[index] = radius;
            updatePreview();
            updateInputs();
            updateOutput();
         }
         
         function stopDrag() {
            isDragging = false;
            activeControl = null;
            controlPoints.forEach(p => p.classList.remove('active'));
         }
         
         // Input value changes
         valueInputs.forEach(input => {
            input.addEventListener('input', function() {
               const index = parseInt(this.getAttribute('data-index'));
               let value = parseInt(this.value) || 0;
               value = Math.max(0, Math.min(100, value));
               values[index] = value;
               updatePreview();
               updateOutput();
            });
            
            input.addEventListener('change', function() {
               let value = parseInt(this.value) || 0;
               value = Math.max(0, Math.min(100, value));
               this.value = value;
               const index = parseInt(this.getAttribute('data-index'));
               values[index] = value;
               updatePreview();
               updateOutput();
            });
         });
         
         // Radius slider
         radiusSlider.addEventListener('input', function() {
            const value = parseInt(this.value);
            radiusValue.textContent = value + '%';
            
            // Update all values proportionally
            const currentMax = Math.max(...values);
            if (currentMax > 0) {
               const factor = value / currentMax;
               for (let i = 0; i < 4; i++) {
                  values[i] = Math.round(values[i] * factor);
               }
            } else {
               values.fill(value);
            }
            
            updatePreview();
            updateInputs();
            updateOutput();
         });
         
         // Preset buttons
         presetButtons.forEach(button => {
            button.addEventListener('click', function() {
               const valuesStr = this.getAttribute('data-values');
               const newValues = valuesStr.split(',').map(v => parseInt(v));
               
               presetButtons.forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               
               for (let i = 0; i < 4; i++) {
                  values[i] = newValues[i];
               }
               
               updatePreview();
               updateInputs();
               updateOutput();
               
               // Update slider to match the average
               const avg = Math.round(newValues.reduce((a, b) => a + b, 0) / 4);
               radiusSlider.value = avg;
               radiusValue.textContent = avg + '%';
            });
         });
         
         function updatePreview() {
            const borderRadius = `${values[0]}% ${values[1]}% ${values[2]}% ${values[3]}%`;
            preview.style.borderRadius = borderRadius;
         }
         
         function updateInputs() {
            valueInputs.forEach((input, index) => {
               input.value = values[index];
            });
         }
         
         function updateOutput() {
            const borderRadius = `${values[0]}% ${values[1]}% ${values[2]}% ${values[3]}%`;
            const cssCode = `.your-element {
  border-radius: ${borderRadius};
  /* Alternative syntax: */
  /* border-top-left-radius: ${values[0]}%; */
  /* border-top-right-radius: ${values[1]}%; */
  /* border-bottom-right-radius: ${values[2]}%; */
  /* border-bottom-left-radius: ${values[3]}%; */
}`;
            
            document.getElementById('outputCode').textContent = cssCode;
         }
      });
      
      function resetControls() {
         const values = [0, 0, 0, 0];
         const slider = document.getElementById('radiusSlider');
         const valueInputs = document.querySelectorAll('.value-input');
         const presetButtons = document.querySelectorAll('.preset-btn');
         
         slider.value = 0;
         document.getElementById('radiusValue').textContent = '0%';
         
         valueInputs.forEach((input, index) => {
            input.value = values[index];
         });
         
         presetButtons.forEach(b => b.classList.remove('active'));
         presetButtons[0].classList.add('active');
         
         // Trigger update
         const event = new Event('input');
         slider.dispatchEvent(event);
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