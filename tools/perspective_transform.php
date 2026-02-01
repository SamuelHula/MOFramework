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
   <title>Perspective Transform Tool - Code Library</title>
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
      .perspective-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .perspective-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .perspective-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .perspective-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .perspective-generator {
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
      .perspective-preview-container {
         position: relative;
         width: 100%;
         height: 300px;
         background: var(--back-light);
         border-radius: 12px;
         margin-bottom: 2rem;
         overflow: hidden;
         border: 2px solid var(--back-dark);
         perspective: 1000px;
      }
      .perspective-stage {
         width: 100%;
         height: 100%;
         position: relative;
         transform-style: preserve-3d;
      }
      .transformed-box {
         position: absolute;
         top: 50%;
         left: 50%;
         width: 200px;
         height: 150px;
         background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
         border: 3px solid white;
         border-radius: 8px;
         box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
         transform-origin: center center;
         transform: translate(-50%, -50%);
         transition: all 0.3s ease;
      }
      .control-handle {
         position: absolute;
         width: 24px;
         height: 24px;
         background: white;
         border: 2px solid var(--primary);
         border-radius: 50%;
         cursor: grab;
         transform: translate(-50%, -50%);
         box-shadow: 0 3px 10px rgba(0, 0, 0, 0.2);
         transition: all 0.2s;
         z-index: 10;
      }
      .control-handle:hover {
         background: var(--primary);
         transform: translate(-50%, -50%) scale(1.2);
      }
      .control-handle.active {
         background: var(--secondary);
         border-color: var(--secondary);
      }
      .control-grid {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 1.5rem;
         margin-bottom: 2rem;
      }
      .control-group {
         display: flex;
         flex-direction: column;
      }
      .control-group label {
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 0.9rem;
      }
      .slider-container {
         display: flex;
         align-items: center;
         gap: 1rem;
      }
      .slider-value {
         min-width: 40px;
         font-weight: 600;
         color: var(--primary);
         text-align: center;
      }
      .slider {
         flex: 1;
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
      .transform-axes {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
         gap: 1rem;
         margin-bottom: 2rem;
      }
      .axis-control {
         background: var(--back-light);
         padding: 1rem;
         border-radius: 8px;
         text-align: center;
         border: 1px solid var(--back-dark);
      }
      .axis-control label {
         display: block;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 0.9rem;
      }
      .preset-selector {
         margin-bottom: 2rem;
      }
      .preset-grid {
         display: grid;
         grid-template-columns: repeat(3, 1fr);
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
         gap: 0.5rem;
      }
      .preset-btn:hover {
         background: var(--back-dark);
      }
      .preset-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .preset-icon {
         font-size: 1.2rem;
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
      .perspective-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .perspective-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .perspective-btn.copy {
         background: var(--primary);
         color: white;
      }
      .perspective-btn.copy:hover {
         background: var(--secondary);
      }
      .perspective-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .perspective-btn.reset:hover {
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
      .control-mode {
         display: flex;
         gap: 1rem;
         margin-bottom: 1.5rem;
      }
      .mode-btn {
         padding: 0.6rem 1.2rem;
         background: var(--back-light);
         border: 2px solid var(--back-dark);
         border-radius: 6px;
         cursor: pointer;
         transition: all 0.3s;
         font-weight: 600;
         font-size: 0.9rem;
      }
      .mode-btn:hover {
         background: var(--back-dark);
      }
      .mode-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .grid-lines {
         position: absolute;
         top: 0;
         left: 0;
         width: 100%;
         height: 100%;
         pointer-events: none;
         opacity: 0.3;
      }
      .grid-line {
         position: absolute;
         background: var(--primary);
      }
      .grid-line.vertical {
         width: 1px;
         height: 100%;
         left: 50%;
         transform: translateX(-50%);
      }
      .grid-line.horizontal {
         width: 100%;
         height: 1px;
         top: 50%;
         transform: translateY(-50%);
      }
      @media screen and (max-width: 1200px) {
         .perspective-container {
            padding: 2% 5% 5%;
         }
         .control-grid {
            grid-template-columns: repeat(2, 1fr);
         }
         .preset-grid {
            grid-template-columns: repeat(2, 1fr);
         }
      }
      @media screen and (max-width: 992px) {
         .perspective-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .perspective-container {
            padding: 2% 1rem 5%;
         }
         .perspective-header h1 {
            font-size: 2.2rem;
         }
         .perspective-actions {
            flex-direction: column;
         }
         .control-grid {
            grid-template-columns: 1fr;
         }
         .preset-grid {
            grid-template-columns: 1fr;
         }
         .transform-axes {
            grid-template-columns: 1fr;
         }
         .transformed-box {
            width: 150px;
            height: 100px;
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
      <section class="perspective-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="perspective-header scroll-effect">
            <h1>Perspective Transform Tool</h1>
            <p>Create 3D perspective effects with interactive controls</p>
         </div>
         
         <div class="perspective-generator scroll-effect">
            <div class="input-section">
               <h2 class="section-title">3D Transform Controls</h2>
               
               <div class="control-mode">
                  <button type="button" class="mode-btn active" data-mode="transform">Transform Controls</button>
                  <button type="button" class="mode-btn" data-mode="perspective">Perspective Controls</button>
               </div>
               
               <div class="perspective-preview-container" id="previewContainer">
                  <div class="grid-lines">
                     <div class="grid-line vertical"></div>
                     <div class="grid-line horizontal"></div>
                  </div>
                  <div class="perspective-stage" id="perspectiveStage">
                     <div class="transformed-box" id="transformedBox"></div>
                  </div>
                  <!-- Control handles will be added dynamically -->
               </div>
               
               <div class="control-group" id="transformControls">
                  <div class="transform-axes">
                     <div class="axis-control">
                        <label>Rotate X</label>
                        <div class="slider-container">
                           <input type="range" class="slider" id="rotateX" min="-180" max="180" value="0">
                           <span class="slider-value" id="rotateXValue">0°</span>
                        </div>
                     </div>
                     <div class="axis-control">
                        <label>Rotate Y</label>
                        <div class="slider-container">
                           <input type="range" class="slider" id="rotateY" min="-180" max="180" value="0">
                           <span class="slider-value" id="rotateYValue">0°</span>
                        </div>
                     </div>
                     <div class="axis-control">
                        <label>Rotate Z</label>
                        <div class="slider-container">
                           <input type="range" class="slider" id="rotateZ" min="-180" max="180" value="0">
                           <span class="slider-value" id="rotateZValue">0°</span>
                        </div>
                     </div>
                  </div>
                  
                  <div class="control-grid">
                     <div class="control-group">
                        <label>Translate X: <span id="translateXValue">0px</span></label>
                        <input type="range" class="slider" id="translateX" min="-100" max="100" value="0">
                     </div>
                     <div class="control-group">
                        <label>Translate Y: <span id="translateYValue">0px</span></label>
                        <input type="range" class="slider" id="translateY" min="-100" max="100" value="0">
                     </div>
                     <div class="control-group">
                        <label>Translate Z: <span id="translateZValue">0px</span></label>
                        <input type="range" class="slider" id="translateZ" min="-200" max="200" value="0">
                     </div>
                     <div class="control-group">
                        <label>Scale X: <span id="scaleXValue">1.0</span></label>
                        <input type="range" class="slider" id="scaleX" min="0.1" max="3" step="0.1" value="1">
                     </div>
                     <div class="control-group">
                        <label>Scale Y: <span id="scaleYValue">1.0</span></label>
                        <input type="range" class="slider" id="scaleY" min="0.1" max="3" step="0.1" value="1">
                     </div>
                     <div class="control-group">
                        <label>Scale Z: <span id="scaleZValue">1.0</span></label>
                        <input type="range" class="slider" id="scaleZ" min="0.1" max="3" step="0.1" value="1">
                     </div>
                     <div class="control-group">
                        <label>Skew X: <span id="skewXValue">0°</span></label>
                        <input type="range" class="slider" id="skewX" min="-45" max="45" value="0">
                     </div>
                     <div class="control-group">
                        <label>Skew Y: <span id="skewYValue">0°</span></label>
                        <input type="range" class="slider" id="skewY" min="-45" max="45" value="0">
                     </div>
                  </div>
               </div>
               
               <div class="control-group" id="perspectiveControls" style="display: none;">
                  <div class="control-grid">
                     <div class="control-group">
                        <label>Perspective: <span id="perspectiveValue">1000px</span></label>
                        <input type="range" class="slider" id="perspectiveSlider" min="200" max="2000" value="1000">
                     </div>
                     <div class="control-group">
                        <label>Perspective Origin X: <span id="perspectiveOriginXValue">50%</span></label>
                        <input type="range" class="slider" id="perspectiveOriginX" min="0" max="100" value="50">
                     </div>
                     <div class="control-group">
                        <label>Perspective Origin Y: <span id="perspectiveOriginYValue">50%</span></label>
                        <input type="range" class="slider" id="perspectiveOriginY" min="0" max="100" value="50">
                     </div>
                  </div>
               </div>
               
               <div class="preset-selector">
                  <label>Transform Presets</label>
                  <div class="preset-grid" id="presetGrid">
                     <button type="button" class="preset-btn" data-preset="identity">
                        <i class="fas fa-cube preset-icon"></i>
                        Identity
                     </button>
                     <button type="button" class="preset-btn" data-preset="rotate-3d">
                        <i class="fas fa-sync-alt preset-icon"></i>
                        3D Rotate
                     </button>
                     <button type="button" class="preset-btn" data-preset="flip-horizontal">
                        <i class="fas fa-exchange-alt preset-icon"></i>
                        Flip Horizontal
                     </button>
                     <button type="button" class="preset-btn" data-preset="flip-vertical">
                        <i class="fas fa-arrows-alt-v preset-icon"></i>
                        Flip Vertical
                     </button>
                     <button type="button" class="preset-btn" data-preset="tilt-left">
                        <i class="fas fa-angle-double-left preset-icon"></i>
                        Tilt Left
                     </button>
                     <button type="button" class="preset-btn" data-preset="tilt-right">
                        <i class="fas fa-angle-double-right preset-icon"></i>
                        Tilt Right
                     </button>
                     <button type="button" class="preset-btn" data-preset="card-flip">
                        <i class="fas fa-id-card preset-icon"></i>
                        Card Flip
                     </button>
                     <button type="button" class="preset-btn" data-preset="isometric">
                        <i class="fas fa-cube preset-icon"></i>
                        Isometric
                     </button>
                     <button type="button" class="preset-btn" data-preset="zoom-in">
                        <i class="fas fa-search-plus preset-icon"></i>
                        Zoom In
                     </button>
                  </div>
               </div>
               
               <div class="perspective-actions">
                  <button type="button" class="perspective-btn reset" onclick="resetTransforms()">
                     <i class="fas fa-redo"></i> Reset All
                  </button>
               </div>
            </div>
            
            <div class="output-section">
               <h2 class="section-title">Generated CSS</h2>
               
               <div class="output-code" id="outputCode">
<span class="css-comment">/* Generated 3D Transform CSS */</span>
<span class="css-property">transform</span>: <span class="css-value">perspective(1000px)</span> <span class="css-value">rotateX(0deg)</span> <span class="css-value">rotateY(0deg)</span> <span class="css-value">rotateZ(0deg)</span> <span class="css-value">translate3d(0px, 0px, 0px)</span> <span class="css-value">scale3d(1, 1, 1)</span> <span class="css-value">skew(0deg, 0deg)</span>;
<span class="css-property">transform-origin</span>: <span class="css-value">center center</span>;
<span class="css-property">transform-style</span>: <span class="css-value">preserve-3d</span>;

<span class="css-comment">/* Container perspective */</span>
<span class="css-property">perspective</span>: <span class="css-value">1000px</span>;
<span class="css-property">perspective-origin</span>: <span class="css-value">50% 50%</span>;
               </div>
               
               <div class="perspective-actions">
                  <button type="button" class="perspective-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy CSS
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  CSS transform copied to clipboard!
               </div>
               
               <div class="perspective-info" style="margin-top: 2rem; padding: 1rem; background: #f0f8ff; border-radius: 8px;">
                  <p><strong>How to use:</strong> Adjust the sliders or use presets to create 3D transforms. The CSS code updates in real-time. Apply the generated CSS to any element for 3D effects.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      let currentTransform = {
         rotateX: 0,
         rotateY: 0,
         rotateZ: 0,
         translateX: 0,
         translateY: 0,
         translateZ: 0,
         scaleX: 1,
         scaleY: 1,
         scaleZ: 1,
         skewX: 0,
         skewY: 0
      };
      
      let perspectiveSettings = {
         perspective: 1000,
         perspectiveOriginX: 50,
         perspectiveOriginY: 50
      };
      
      let currentRawCss = '';
      let isDragging = false;
      let activeHandle = null;
      let startX = 0, startY = 0;
      let startTransform = {};
      
      document.addEventListener('DOMContentLoaded', function() {
         initializeControls();
         updateTransform();
         updateCssOutput();
         
         // Mode switching
         document.querySelectorAll('.mode-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               document.querySelectorAll('.mode-btn').forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               
               const mode = this.getAttribute('data-mode');
               if (mode === 'transform') {
                  document.getElementById('transformControls').style.display = 'block';
                  document.getElementById('perspectiveControls').style.display = 'none';
               } else {
                  document.getElementById('transformControls').style.display = 'none';
                  document.getElementById('perspectiveControls').style.display = 'block';
               }
            });
         });
         
         // Slider events
         const sliders = [
            'rotateX', 'rotateY', 'rotateZ',
            'translateX', 'translateY', 'translateZ',
            'scaleX', 'scaleY', 'scaleZ',
            'skewX', 'skewY',
            'perspectiveSlider', 'perspectiveOriginX', 'perspectiveOriginY'
         ];
         
         sliders.forEach(sliderId => {
            const slider = document.getElementById(sliderId);
            if (slider) {
               slider.addEventListener('input', handleSliderChange);
            }
         });
         
         // Preset buttons
         document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
               this.classList.add('active');
               applyPreset(this.getAttribute('data-preset'));
            });
         });
         
         // Setup drag handles
         setupDragHandles();
      });
      
      function initializeControls() {
         // Set initial slider values
         document.getElementById('rotateX').value = currentTransform.rotateX;
         document.getElementById('rotateY').value = currentTransform.rotateY;
         document.getElementById('rotateZ').value = currentTransform.rotateZ;
         document.getElementById('translateX').value = currentTransform.translateX;
         document.getElementById('translateY').value = currentTransform.translateY;
         document.getElementById('translateZ').value = currentTransform.translateZ;
         document.getElementById('scaleX').value = currentTransform.scaleX;
         document.getElementById('scaleY').value = currentTransform.scaleY;
         document.getElementById('scaleZ').value = currentTransform.scaleZ;
         document.getElementById('skewX').value = currentTransform.skewX;
         document.getElementById('skewY').value = currentTransform.skewY;
         
         document.getElementById('perspectiveSlider').value = perspectiveSettings.perspective;
         document.getElementById('perspectiveOriginX').value = perspectiveSettings.perspectiveOriginX;
         document.getElementById('perspectiveOriginY').value = perspectiveSettings.perspectiveOriginY;
         
         updateSliderValues();
      }
      
      function handleSliderChange(e) {
         const sliderId = e.target.id;
         const value = parseFloat(e.target.value);
         
         switch(sliderId) {
            case 'rotateX':
               currentTransform.rotateX = value;
               document.getElementById('rotateXValue').textContent = value + '°';
               break;
            case 'rotateY':
               currentTransform.rotateY = value;
               document.getElementById('rotateYValue').textContent = value + '°';
               break;
            case 'rotateZ':
               currentTransform.rotateZ = value;
               document.getElementById('rotateZValue').textContent = value + '°';
               break;
            case 'translateX':
               currentTransform.translateX = value;
               document.getElementById('translateXValue').textContent = value + 'px';
               break;
            case 'translateY':
               currentTransform.translateY = value;
               document.getElementById('translateYValue').textContent = value + 'px';
               break;
            case 'translateZ':
               currentTransform.translateZ = value;
               document.getElementById('translateZValue').textContent = value + 'px';
               break;
            case 'scaleX':
               currentTransform.scaleX = value;
               document.getElementById('scaleXValue').textContent = value.toFixed(1);
               break;
            case 'scaleY':
               currentTransform.scaleY = value;
               document.getElementById('scaleYValue').textContent = value.toFixed(1);
               break;
            case 'scaleZ':
               currentTransform.scaleZ = value;
               document.getElementById('scaleZValue').textContent = value.toFixed(1);
               break;
            case 'skewX':
               currentTransform.skewX = value;
               document.getElementById('skewXValue').textContent = value + '°';
               break;
            case 'skewY':
               currentTransform.skewY = value;
               document.getElementById('skewYValue').textContent = value + '°';
               break;
            case 'perspectiveSlider':
               perspectiveSettings.perspective = value;
               document.getElementById('perspectiveValue').textContent = value + 'px';
               updatePerspective();
               break;
            case 'perspectiveOriginX':
               perspectiveSettings.perspectiveOriginX = value;
               document.getElementById('perspectiveOriginXValue').textContent = value + '%';
               updatePerspective();
               break;
            case 'perspectiveOriginY':
               perspectiveSettings.perspectiveOriginY = value;
               document.getElementById('perspectiveOriginYValue').textContent = value + '%';
               updatePerspective();
               break;
         }
         
         updateTransform();
         updateCssOutput();
      }
      
      function updateSliderValues() {
         document.getElementById('rotateXValue').textContent = currentTransform.rotateX + '°';
         document.getElementById('rotateYValue').textContent = currentTransform.rotateY + '°';
         document.getElementById('rotateZValue').textContent = currentTransform.rotateZ + '°';
         document.getElementById('translateXValue').textContent = currentTransform.translateX + 'px';
         document.getElementById('translateYValue').textContent = currentTransform.translateY + 'px';
         document.getElementById('translateZValue').textContent = currentTransform.translateZ + 'px';
         document.getElementById('scaleXValue').textContent = currentTransform.scaleX.toFixed(1);
         document.getElementById('scaleYValue').textContent = currentTransform.scaleY.toFixed(1);
         document.getElementById('scaleZValue').textContent = currentTransform.scaleZ.toFixed(1);
         document.getElementById('skewXValue').textContent = currentTransform.skewX + '°';
         document.getElementById('skewYValue').textContent = currentTransform.skewY + '°';
         
         document.getElementById('perspectiveValue').textContent = perspectiveSettings.perspective + 'px';
         document.getElementById('perspectiveOriginXValue').textContent = perspectiveSettings.perspectiveOriginX + '%';
         document.getElementById('perspectiveOriginYValue').textContent = perspectiveSettings.perspectiveOriginY + '%';
      }
      
      function setupDragHandles() {
         // Create drag handles for corners
         const handles = [
            { id: 'top-left', x: -100, y: -75 },
            { id: 'top-right', x: 100, y: -75 },
            { id: 'bottom-right', x: 100, y: 75 },
            { id: 'bottom-left', x: -100, y: 75 }
         ];
         
         const stage = document.getElementById('perspectiveStage');
         handles.forEach(handle => {
            const handleEl = document.createElement('div');
            handleEl.className = 'control-handle';
            handleEl.id = 'handle-' + handle.id;
            handleEl.style.left = '50%';
            handleEl.style.top = '50%';
            handleEl.style.transform = `translate(calc(-50% + ${handle.x}px), calc(-50% + ${handle.y}px))`;
            
            // Add event listeners for dragging
            handleEl.addEventListener('mousedown', startDrag);
            handleEl.addEventListener('touchstart', startDragTouch);
            
            stage.appendChild(handleEl);
         });
         
         // Add global mouse/touch events
         document.addEventListener('mousemove', drag);
         document.addEventListener('mouseup', stopDrag);
         document.addEventListener('touchmove', dragTouch);
         document.addEventListener('touchend', stopDrag);
      }
      
      function startDrag(e) {
         e.preventDefault();
         isDragging = true;
         activeHandle = e.target;
         startX = e.clientX;
         startY = e.clientY;
         startTransform = { ...currentTransform };
         activeHandle.classList.add('active');
      }
      
      function startDragTouch(e) {
         if (e.touches.length === 1) {
            e.preventDefault();
            isDragging = true;
            activeHandle = e.target;
            const touch = e.touches[0];
            startX = touch.clientX;
            startY = touch.clientY;
            startTransform = { ...currentTransform };
            activeHandle.classList.add('active');
         }
      }
      
      function drag(e) {
         if (!isDragging || !activeHandle) return;
         
         const deltaX = e.clientX - startX;
         const deltaY = e.clientY - startY;
         
         // Calculate rotation based on which handle is being dragged
         const handleId = activeHandle.id;
         
         if (handleId.includes('top-left')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         } else if (handleId.includes('top-right')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         } else if (handleId.includes('bottom-right')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         } else if (handleId.includes('bottom-left')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         }
         
         // Update sliders and UI
         document.getElementById('rotateX').value = currentTransform.rotateX;
         document.getElementById('rotateY').value = currentTransform.rotateY;
         updateSliderValues();
         updateTransform();
         updateCssOutput();
      }
      
      function dragTouch(e) {
         if (!isDragging || !activeHandle || e.touches.length !== 1) return;
         
         const touch = e.touches[0];
         const deltaX = touch.clientX - startX;
         const deltaY = touch.clientY - startY;
         
         // Calculate rotation based on which handle is being dragged
         const handleId = activeHandle.id;
         
         if (handleId.includes('top-left')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         } else if (handleId.includes('top-right')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         } else if (handleId.includes('bottom-right')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         } else if (handleId.includes('bottom-left')) {
            currentTransform.rotateY = startTransform.rotateY + deltaX * 0.5;
            currentTransform.rotateX = startTransform.rotateX - deltaY * 0.5;
         }
         
         // Update sliders and UI
         document.getElementById('rotateX').value = currentTransform.rotateX;
         document.getElementById('rotateY').value = currentTransform.rotateY;
         updateSliderValues();
         updateTransform();
         updateCssOutput();
      }
      
      function stopDrag() {
         isDragging = false;
         if (activeHandle) {
            activeHandle.classList.remove('active');
            activeHandle = null;
         }
      }
      
      function updateTransform() {
         const box = document.getElementById('transformedBox');
         const transform = generateTransformString();
         box.style.transform = transform;
      }
      
      function updatePerspective() {
         const container = document.getElementById('previewContainer');
         container.style.perspective = perspectiveSettings.perspective + 'px';
         container.style.perspectiveOrigin = `${perspectiveSettings.perspectiveOriginX}% ${perspectiveSettings.perspectiveOriginY}%`;
      }
      
      function generateTransformString() {
         const t = currentTransform;
         return `translate(-50%, -50%) ` +
                `perspective(${perspectiveSettings.perspective}px) ` +
                `rotateX(${t.rotateX}deg) ` +
                `rotateY(${t.rotateY}deg) ` +
                `rotateZ(${t.rotateZ}deg) ` +
                `translate3d(${t.translateX}px, ${t.translateY}px, ${t.translateZ}px) ` +
                `scale3d(${t.scaleX}, ${t.scaleY}, ${t.scaleZ}) ` +
                `skew(${t.skewX}deg, ${t.skewY}deg)`;
      }
      
      function applyPreset(presetName) {
         const presets = {
            'identity': {
               rotateX: 0, rotateY: 0, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 0,
               scaleX: 1, scaleY: 1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'rotate-3d': {
               rotateX: 25, rotateY: 45, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 50,
               scaleX: 1, scaleY: 1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'flip-horizontal': {
               rotateX: 0, rotateY: 180, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 0,
               scaleX: 1, scaleY: 1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'flip-vertical': {
               rotateX: 180, rotateY: 0, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 0,
               scaleX: 1, scaleY: 1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'tilt-left': {
               rotateX: 15, rotateY: -25, rotateZ: 0,
               translateX: -20, translateY: 10, translateZ: 30,
               scaleX: 1, scaleY: 1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'tilt-right': {
               rotateX: 15, rotateY: 25, rotateZ: 0,
               translateX: 20, translateY: 10, translateZ: 30,
               scaleX: 1, scaleY: 1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'card-flip': {
               rotateX: 0, rotateY: -90, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 100,
               scaleX: 1.1, scaleY: 1.1, scaleZ: 1,
               skewX: 0, skewY: 0
            },
            'isometric': {
               rotateX: 30, rotateY: 45, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 0,
               scaleX: 0.9, scaleY: 0.9, scaleZ: 0.9,
               skewX: 0, skewY: 0
            },
            'zoom-in': {
               rotateX: 0, rotateY: 0, rotateZ: 0,
               translateX: 0, translateY: 0, translateZ: 150,
               scaleX: 1.5, scaleY: 1.5, scaleZ: 1,
               skewX: 0, skewY: 0
            }
         };
         
         if (presets[presetName]) {
            const preset = presets[presetName];
            currentTransform = { ...preset };
            initializeControls();
            updateTransform();
            updateCssOutput();
         }
      }
      
      function updateCssOutput() {
         const t = currentTransform;
         const p = perspectiveSettings;
         
         let css = '/* Generated 3D Transform CSS */\n';
         css += 'transform: ' + generateTransformString().replace('translate(-50%, -50%) ', '') + ';\n';
         css += 'transform-origin: center center;\n';
         css += 'transform-style: preserve-3d;\n\n';
         css += '/* Container perspective */\n';
         css += 'perspective: ' + p.perspective + 'px;\n';
         css += 'perspective-origin: ' + p.perspectiveOriginX + '% ' + p.perspectiveOriginY + '%;';
         
         currentRawCss = css;
         
         // Add syntax highlighting
         const highlighted = css
            .replace(/\/\*.*?\*\//g, '<span class="css-comment">$&</span>')
            .replace(/(transform|transform-origin|transform-style|perspective|perspective-origin):/g, '<span class="css-property">$1</span>:')
            .replace(/(perspective|rotateX|rotateY|rotateZ|translate3d|scale3d|skew)\([^)]+\)/g, '<span class="css-value">$&</span>')
            .replace(/(center|preserve-3d)/g, '<span class="css-value">$1</span>')
            .replace(/(\d+(\.\d+)?)(px|deg|%)/g, '<span class="css-value">$&</span>');
         
         document.getElementById('outputCode').innerHTML = highlighted;
      }
      
      function resetTransforms() {
         currentTransform = {
            rotateX: 0,
            rotateY: 0,
            rotateZ: 0,
            translateX: 0,
            translateY: 0,
            translateZ: 0,
            scaleX: 1,
            scaleY: 1,
            scaleZ: 1,
            skewX: 0,
            skewY: 0
         };
         
         perspectiveSettings = {
            perspective: 1000,
            perspectiveOriginX: 50,
            perspectiveOriginY: 50
         };
         
         document.querySelectorAll('.preset-btn').forEach(b => b.classList.remove('active'));
         document.querySelector('.preset-btn[data-preset="identity"]').classList.add('active');
         
         initializeControls();
         updateTransform();
         updatePerspective();
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