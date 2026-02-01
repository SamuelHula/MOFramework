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
   <title>Box Shadow Generator - Code Library</title>
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
      .shadow-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .shadow-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .shadow-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .shadow-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .shadow-generator {
         display: grid;
         grid-template-columns: 1fr 1fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .control-section, .preview-section {
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
         margin-bottom: 2rem;
      }
      .control-group h3 {
         font-size: 1.2rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .slider-control {
         margin-bottom: 1.5rem;
      }
      .slider-control label {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 0.5rem;
         font-weight: 600;
      }
      .slider-control input[type="range"] {
         width: 100%;
         height: 8px;
         -webkit-appearance: none;
         background: var(--back-dark);
         border-radius: 4px;
         outline: none;
      }
      .slider-control input[type="range"]::-webkit-slider-thumb {
         -webkit-appearance: none;
         width: 20px;
         height: 20px;
         background: var(--primary);
         border-radius: 50%;
         cursor: pointer;
         transition: all 0.3s;
      }
      .slider-control input[type="range"]::-webkit-slider-thumb:hover {
         background: var(--secondary);
         transform: scale(1.1);
      }
      .value-display {
         display: inline-block;
         min-width: 40px;
         text-align: right;
         font-weight: 600;
         color: var(--primary);
      }
      .color-control {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 1.5rem;
      }
      .color-control label {
         font-weight: 600;
         min-width: 120px;
      }
      .color-picker {
         width: 60px;
         height: 40px;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
         padding: 2px;
      }
      .color-value {
         font-family: monospace;
         background: var(--back-light);
         padding: 0.5rem 1rem;
         border-radius: 6px;
         border: 1px solid var(--back-dark);
         flex-grow: 1;
      }
      .checkbox-control {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 1.5rem;
      }
      .checkbox-control input[type="checkbox"] {
         width: 20px;
         height: 20px;
         accent-color: var(--primary);
      }
      .checkbox-control label {
         font-weight: 600;
         cursor: pointer;
      }
      .shadow-count-control {
         display: flex;
         align-items: center;
         gap: 1rem;
         margin-bottom: 1.5rem;
      }
      .count-btn {
         width: 40px;
         height: 40px;
         border: none;
         background: var(--primary);
         color: white;
         border-radius: 8px;
         cursor: pointer;
         font-size: 1.5rem;
         display: flex;
         align-items: center;
         justify-content: center;
         transition: all 0.3s;
      }
      .count-btn:hover {
         background: var(--secondary);
      }
      .shadow-count {
         font-size: 1.2rem;
         font-weight: 600;
         min-width: 40px;
         text-align: center;
      }
      .shadow-layers {
         margin-top: 2rem;
         padding-top: 1rem;
         border-top: 2px solid var(--back-dark);
      }
      .shadow-layer {
         background: var(--back-light);
         padding: 1rem;
         border-radius: 8px;
         margin-bottom: 1rem;
         border: 1px solid var(--back-dark);
      }
      .layer-header {
         display: flex;
         justify-content: space-between;
         align-items: center;
         margin-bottom: 1rem;
      }
      .layer-header h4 {
         margin: 0;
         color: var(--text-color);
      }
      .remove-layer {
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
      }
      .preview-box {
         width: 300px;
         height: 300px;
         background: white;
         border-radius: 15px;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 2rem;
         position: relative;
         border: 2px solid var(--back-dark);
         transition: all 0.3s;
      }
      .preview-content {
         text-align: center;
         padding: 1rem;
      }
      .preview-content h3 {
         color: var(--text-color);
         margin-bottom: 0.5rem;
      }
      .preview-content p {
         color: var(--text-color);
         opacity: 0.8;
         font-size: 0.9rem;
      }
      .code-output {
         background: #1e1e1e;
         color: #d4d4d4;
         padding: 1.5rem;
         border-radius: 8px;
         font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
         font-size: 1rem;
         line-height: 1.5;
         white-space: pre-wrap;
         word-wrap: break-word;
         max-height: 300px;
         overflow-y: auto;
         margin-bottom: 1.5rem;
      }
      .code-output::-webkit-scrollbar {
         width: 8px;
      }
      .code-output::-webkit-scrollbar-track {
         background: #2d2d2d;
      }
      .code-output::-webkit-scrollbar-thumb {
         background: #555;
         border-radius: 4px;
      }
      .generator-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
      }
      .shadow-btn {
         padding: 0.8rem 2rem;
         border-radius: 8px;
         border: none;
         font-weight: 600;
         cursor: pointer;
         transition: all 0.3s;
         font-size: 1rem;
         flex: 1;
      }
      .shadow-btn.generate {
         background: var(--primary);
         color: white;
      }
      .shadow-btn.generate:hover {
         background: var(--secondary);
      }
      .shadow-btn.copy {
         background: var(--secondary);
         color: white;
      }
      .shadow-btn.copy:hover {
         background: var(--primary);
      }
      .shadow-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .shadow-btn.reset:hover {
         background: var(--back-dark);
      }
      .copy-success {
         background: #4CAF50;
         color: white;
         padding: 0.8rem 1.5rem;
         border-radius: 8px;
         margin-top: 1rem;
         text-align: center;
         display: none;
         animation: fadeInOut 3s ease;
      }
      @keyframes fadeInOut {
         0%, 100% { opacity: 0; }
         10%, 90% { opacity: 1; }
      }
      .shadow-presets {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
         gap: 1rem;
         margin-top: 1rem;
      }
      .preset-btn {
         padding: 1rem;
         background: var(--back-light);
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
         transition: all 0.3s;
         text-align: center;
         font-weight: 600;
      }
      .preset-btn:hover {
         background: var(--back-dark);
         transform: translateY(-2px);
      }
      .preset-box {
         width: 60px;
         height: 60px;
         background: white;
         border-radius: 8px;
         margin: 0 auto 0.5rem;
      }
      @media screen and (max-width: 1200px) {
         .shadow-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 992px) {
         .shadow-generator {
            grid-template-columns: 1fr;
         }
      }
      @media screen and (max-width: 768px) {
         .shadow-container {
            padding: 2% 1rem 5%;
         }
         .shadow-header h1 {
            font-size: 2.2rem;
         }
         .generator-actions {
            flex-direction: column;
         }
         .preview-box {
            width: 250px;
            height: 250px;
         }
         .shadow-presets {
            grid-template-columns: repeat(2, 1fr);
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
      <section class="shadow-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="shadow-header scroll-effect">
            <h1>Box Shadow Generator</h1>
            <p>Create beautiful CSS box shadows with real-time preview</p>
         </div>
         
         <div class="shadow-generator scroll-effect">
            <div class="control-section">
               <h2 class="section-title">Shadow Controls</h2>
               
               <div class="control-group">
                  <h3>Shadow Properties</h3>
                  
                  <div class="slider-control">
                     <label>
                        Horizontal Offset: <span class="value-display" id="hOffsetValue">10px</span>
                     </label>
                     <input type="range" id="hOffset" min="-100" max="100" value="10" step="1">
                  </div>
                  
                  <div class="slider-control">
                     <label>
                        Vertical Offset: <span class="value-display" id="vOffsetValue">10px</span>
                     </label>
                     <input type="range" id="vOffset" min="-100" max="100" value="10" step="1">
                  </div>
                  
                  <div class="slider-control">
                     <label>
                        Blur Radius: <span class="value-display" id="blurValue">20px</span>
                     </label>
                     <input type="range" id="blur" min="0" max="100" value="20" step="1">
                  </div>
                  
                  <div class="slider-control">
                     <label>
                        Spread Radius: <span class="value-display" id="spreadValue">0px</span>
                     </label>
                     <input type="range" id="spread" min="-50" max="50" value="0" step="1">
                  </div>
                  
                  <div class="color-control">
                     <label>Shadow Color:</label>
                     <input type="color" id="shadowColor" class="color-picker" value="#000000">
                     <div class="color-value" id="colorValueDisplay">#000000</div>
                  </div>
                  
                  <div class="color-control">
                     <label>Box Color:</label>
                     <input type="color" id="boxColor" class="color-picker" value="#ffffff">
                     <div class="color-value" id="boxColorValueDisplay">#ffffff</div>
                  </div>
                  
                  <div class="checkbox-control">
                     <input type="checkbox" id="insetCheckbox">
                     <label for="insetCheckbox">Inset Shadow (Inner Shadow)</label>
                  </div>
               </div>
               
               <div class="control-group">
                  <h3>Multiple Shadows</h3>
                  
                  <div class="shadow-count-control">
                     <button class="count-btn" id="decreaseCount">-</button>
                     <span class="shadow-count" id="shadowCount">1</span> Shadow Layer(s)
                     <button class="count-btn" id="increaseCount">+</button>
                  </div>
                  
                  <div class="shadow-layers" id="shadowLayers">
                     <!-- Shadow layers will be added here dynamically -->
                  </div>
               </div>
               
               <div class="generator-actions">
                  <button class="shadow-btn generate" onclick="generateShadow()">
                     <i class="fas fa-sync-alt"></i> Update Preview
                  </button>
                  <button class="shadow-btn reset" onclick="resetControls()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="preview-section">
               <h2 class="section-title">Preview & Code</h2>
               
               <div class="preview-box" id="previewBox">
                  <div class="preview-content">
                     <h3>Preview Box</h3>
                     <p>Real-time shadow preview</p>
                  </div>
               </div>
               
               <h3 style="margin-bottom: 1rem;">Quick Presets</h3>
               <div class="shadow-presets">
                  <div class="preset-btn" data-preset="soft">
                     <div class="preset-box" style="box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);"></div>
                     Soft
                  </div>
                  <div class="preset-btn" data-preset="medium">
                     <div class="preset-box" style="box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);"></div>
                     Medium
                  </div>
                  <div class="preset-btn" data-preset="strong">
                     <div class="preset-box" style="box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);"></div>
                     Strong
                  </div>
                  <div class="preset-btn" data-preset="floating">
                     <div class="preset-box" style="box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);"></div>
                     Floating
                  </div>
                  <div class="preset-btn" data-preset="inner">
                     <div class="preset-box" style="box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);"></div>
                     Inner
                  </div>
                  <div class="preset-btn" data-preset="neon">
                     <div class="preset-box" style="box-shadow: 0 0 15px var(--primary);"></div>
                     Neon
                  </div>
               </div>
               
               <h3 style="margin: 2rem 0 1rem;">Generated CSS</h3>
               <div class="code-output" id="codeOutput">
.box-shadow-example {
   box-shadow: 10px 10px 20px 0px rgba(0, 0, 0, 0.5);
}
               </div>
               
               <div class="generator-actions">
                  <button class="shadow-btn copy" onclick="copyToClipboard()">
                     <i class="fas fa-copy"></i> Copy CSS
                  </button>
               </div>
               
               <div class="copy-success" id="copySuccess">
                  CSS code copied to clipboard!
               </div>
               
               <div class="shadow-description" style="margin-top: 2rem; padding: 1rem; background: #f0f8ff; border-radius: 8px;">
                  <p><strong>Usage:</strong> Copy the CSS code and apply it to your elements. You can adjust all properties in real-time.</p>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      class ShadowLayer {
         constructor(id) {
            this.id = id;
            this.hOffset = 10;
            this.vOffset = 10;
            this.blur = 20;
            this.spread = 0;
            this.color = '#000000';
            this.inset = false;
         }
         
         getShadowString() {
            const h = this.hOffset + 'px';
            const v = this.vOffset + 'px';
            const blur = this.blur + 'px';
            const spread = this.spread + 'px';
            const inset = this.inset ? 'inset ' : '';
            return `${inset}${h} ${v} ${blur} ${spread} ${this.color}`;
         }
      }
      
      let shadowLayers = [new ShadowLayer(1)];
      let activeLayerIndex = 0;
      
      document.addEventListener('DOMContentLoaded', function() {
         updateUIFromLayer();
         generateShadow();
         renderLayerControls();
         
         // Event listeners for sliders
         document.getElementById('hOffset').addEventListener('input', function() {
            shadowLayers[activeLayerIndex].hOffset = parseInt(this.value);
            document.getElementById('hOffsetValue').textContent = this.value + 'px';
            updateUIFromLayer();
            generateShadow();
         });
         
         document.getElementById('vOffset').addEventListener('input', function() {
            shadowLayers[activeLayerIndex].vOffset = parseInt(this.value);
            document.getElementById('vOffsetValue').textContent = this.value + 'px';
            updateUIFromLayer();
            generateShadow();
         });
         
         document.getElementById('blur').addEventListener('input', function() {
            shadowLayers[activeLayerIndex].blur = parseInt(this.value);
            document.getElementById('blurValue').textContent = this.value + 'px';
            updateUIFromLayer();
            generateShadow();
         });
         
         document.getElementById('spread').addEventListener('input', function() {
            shadowLayers[activeLayerIndex].spread = parseInt(this.value);
            document.getElementById('spreadValue').textContent = this.value + 'px';
            updateUIFromLayer();
            generateShadow();
         });
         
         document.getElementById('shadowColor').addEventListener('input', function() {
            shadowLayers[activeLayerIndex].color = this.value;
            document.getElementById('colorValueDisplay').textContent = this.value;
            updateUIFromLayer();
            generateShadow();
         });
         
         document.getElementById('boxColor').addEventListener('input', function() {
            document.getElementById('boxColorValueDisplay').textContent = this.value;
            document.getElementById('previewBox').style.backgroundColor = this.value;
            generateShadow();
         });
         
         document.getElementById('insetCheckbox').addEventListener('change', function() {
            shadowLayers[activeLayerIndex].inset = this.checked;
            updateUIFromLayer();
            generateShadow();
         });
         
         // Shadow count controls
         document.getElementById('increaseCount').addEventListener('click', function() {
            if (shadowLayers.length < 5) {
               const newLayer = new ShadowLayer(shadowLayers.length + 1);
               shadowLayers.push(newLayer);
               activeLayerIndex = shadowLayers.length - 1;
               updateShadowCount();
               renderLayerControls();
               updateUIFromLayer();
               generateShadow();
            }
         });
         
         document.getElementById('decreaseCount').addEventListener('click', function() {
            if (shadowLayers.length > 1) {
               shadowLayers.pop();
               if (activeLayerIndex >= shadowLayers.length) {
                  activeLayerIndex = shadowLayers.length - 1;
               }
               updateShadowCount();
               renderLayerControls();
               updateUIFromLayer();
               generateShadow();
            }
         });
         
         // Preset buttons
         document.querySelectorAll('.preset-btn').forEach(btn => {
            btn.addEventListener('click', function() {
               const preset = this.getAttribute('data-preset');
               applyPreset(preset);
            });
         });
      });
      
      function updateShadowCount() {
         document.getElementById('shadowCount').textContent = shadowLayers.length;
      }
      
      function renderLayerControls() {
         const container = document.getElementById('shadowLayers');
         container.innerHTML = '';
         
         shadowLayers.forEach((layer, index) => {
            const layerDiv = document.createElement('div');
            layerDiv.className = 'shadow-layer';
            layerDiv.innerHTML = `
               <div class="layer-header">
                  <h4>Layer ${index + 1}</h4>
                  ${shadowLayers.length > 1 ? `<button class="remove-layer" onclick="removeLayer(${index})">&times;</button>` : ''}
               </div>
               <div class="checkbox-control">
                  <input type="radio" id="layerRadio${index}" name="activeLayer" ${index === activeLayerIndex ? 'checked' : ''} 
                         onchange="setActiveLayer(${index})">
                  <label for="layerRadio${index}">Active Layer</label>
               </div>
               <div style="font-size: 0.9rem; color: #666; margin-top: 0.5rem;">
                  ${layer.getShadowString()}
               </div>
            `;
            container.appendChild(layerDiv);
         });
      }
      
      function setActiveLayer(index) {
         activeLayerIndex = index;
         updateUIFromLayer();
      }
      
      function removeLayer(index) {
         if (shadowLayers.length > 1) {
            shadowLayers.splice(index, 1);
            if (activeLayerIndex >= index && activeLayerIndex > 0) {
               activeLayerIndex--;
            }
            updateShadowCount();
            renderLayerControls();
            updateUIFromLayer();
            generateShadow();
         }
      }
      
      function updateUIFromLayer() {
         const layer = shadowLayers[activeLayerIndex];
         document.getElementById('hOffset').value = layer.hOffset;
         document.getElementById('hOffsetValue').textContent = layer.hOffset + 'px';
         document.getElementById('vOffset').value = layer.vOffset;
         document.getElementById('vOffsetValue').textContent = layer.vOffset + 'px';
         document.getElementById('blur').value = layer.blur;
         document.getElementById('blurValue').textContent = layer.blur + 'px';
         document.getElementById('spread').value = layer.spread;
         document.getElementById('spreadValue').textContent = layer.spread + 'px';
         document.getElementById('shadowColor').value = layer.color;
         document.getElementById('colorValueDisplay').textContent = layer.color;
         document.getElementById('insetCheckbox').checked = layer.inset;
         
         // Update radio buttons
         document.querySelectorAll('input[name="activeLayer"]').forEach((radio, index) => {
            radio.checked = index === activeLayerIndex;
         });
      }
      
      function applyPreset(presetName) {
         switch(presetName) {
            case 'soft':
               shadowLayers[activeLayerIndex] = new ShadowLayer(activeLayerIndex + 1);
               shadowLayers[activeLayerIndex].hOffset = 0;
               shadowLayers[activeLayerIndex].vOffset = 4;
               shadowLayers[activeLayerIndex].blur = 6;
               shadowLayers[activeLayerIndex].color = 'rgba(0, 0, 0, 0.1)';
               break;
            case 'medium':
               shadowLayers[activeLayerIndex] = new ShadowLayer(activeLayerIndex + 1);
               shadowLayers[activeLayerIndex].hOffset = 0;
               shadowLayers[activeLayerIndex].vOffset = 6;
               shadowLayers[activeLayerIndex].blur = 12;
               shadowLayers[activeLayerIndex].color = 'rgba(0, 0, 0, 0.15)';
               break;
            case 'strong':
               shadowLayers[activeLayerIndex] = new ShadowLayer(activeLayerIndex + 1);
               shadowLayers[activeLayerIndex].hOffset = 0;
               shadowLayers[activeLayerIndex].vOffset = 10;
               shadowLayers[activeLayerIndex].blur = 20;
               shadowLayers[activeLayerIndex].color = 'rgba(0, 0, 0, 0.2)';
               break;
            case 'floating':
               shadowLayers[activeLayerIndex] = new ShadowLayer(activeLayerIndex + 1);
               shadowLayers[activeLayerIndex].hOffset = 0;
               shadowLayers[activeLayerIndex].vOffset = 20;
               shadowLayers[activeLayerIndex].blur = 40;
               shadowLayers[activeLayerIndex].color = 'rgba(0, 0, 0, 0.3)';
               break;
            case 'inner':
               shadowLayers[activeLayerIndex] = new ShadowLayer(activeLayerIndex + 1);
               shadowLayers[activeLayerIndex].hOffset = 0;
               shadowLayers[activeLayerIndex].vOffset = 2;
               shadowLayers[activeLayerIndex].blur = 4;
               shadowLayers[activeLayerIndex].color = 'rgba(0, 0, 0, 0.1)';
               shadowLayers[activeLayerIndex].inset = true;
               break;
            case 'neon':
               shadowLayers[activeLayerIndex] = new ShadowLayer(activeLayerIndex + 1);
               shadowLayers[activeLayerIndex].hOffset = 0;
               shadowLayers[activeLayerIndex].vOffset = 0;
               shadowLayers[activeLayerIndex].blur = 15;
               shadowLayers[activeLayerIndex].color = getComputedStyle(document.documentElement).getPropertyValue('--primary').trim();
               break;
         }
         
         updateUIFromLayer();
         generateShadow();
      }
      
      function generateShadow() {
         const boxColor = document.getElementById('boxColor').value;
         const box = document.getElementById('previewBox');
         
         // Apply box color
         box.style.backgroundColor = boxColor;
         
         // Generate shadow string
         const shadowStrings = shadowLayers.map(layer => layer.getShadowString());
         const boxShadowValue = shadowStrings.join(', ');
         
         // Apply to preview
         box.style.boxShadow = boxShadowValue;
         
         // Generate CSS code
         const cssCode = `.box-element {
   box-shadow: ${boxShadowValue};
   background-color: ${boxColor};
}`;
         
         // Display with syntax highlighting
         const highlighted = cssCode
            .replace(/\.box-element/g, '<span style="color: #569cd6;">.box-element</span>')
            .replace(/(box-shadow|background-color)/g, '<span style="color: #9cdcfe;">$1</span>')
            .replace(/:/g, '<span style="color: #d4d4d4;">:</span>')
            .replace(/;/g, '<span style="color: #d4d4d4;">;</span>')
            .replace(/#[0-9a-f]{3,6}|rgba?\([^)]+\)/gi, '<span style="color: #ce9178;">$&</span>');
         
         document.getElementById('codeOutput').innerHTML = highlighted;
      }
      
      function resetControls() {
         shadowLayers = [new ShadowLayer(1)];
         activeLayerIndex = 0;
         document.getElementById('boxColor').value = '#ffffff';
         document.getElementById('boxColorValueDisplay').textContent = '#ffffff';
         updateShadowCount();
         renderLayerControls();
         updateUIFromLayer();
         generateShadow();
      }
      
      function copyToClipboard() {
         const boxShadowValue = shadowLayers.map(layer => layer.getShadowString()).join(', ');
         const boxColor = document.getElementById('boxColor').value;
         const cssCode = `/* Box Shadow CSS */
.box-element {
   box-shadow: ${boxShadowValue};
   background-color: ${boxColor};
}`;
         
         navigator.clipboard.writeText(cssCode).then(() => {
            const successMsg = document.getElementById('copySuccess');
            successMsg.style.display = 'block';
            
            setTimeout(() => {
               successMsg.style.display = 'none';
            }, 3000);
         });
      }
      
      // Initialize
      updateShadowCount();
   </script>
</body>
</html>