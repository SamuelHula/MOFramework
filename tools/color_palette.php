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
   <title>Color Palette Generator - Code Library</title>
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
      .color-container {
         min-height: 100vh;
         padding: 2% 15% 5%;
         position: relative;
      }
      .color-header {
         text-align: center;
         margin-bottom: 3rem;
      }
      .color-header h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .color-header p {
         font-size: 1.2rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .color-generator {
         display: grid;
         grid-template-columns: 1fr;
         gap: 2rem;
         margin-bottom: 3rem;
      }
      .controls-section, .palette-section {
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
      .color-controls {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
         gap: 1.5rem;
         margin-bottom: 2rem;
      }
      .control-group {
         margin-bottom: 1rem;
      }
      .control-group label {
         display: block;
         font-weight: 600;
         color: var(--text-color);
         margin-bottom: 0.5rem;
         font-size: 1rem;
      }
      .color-picker {
         width: 100%;
         height: 50px;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         cursor: pointer;
         padding: 0.2rem;
      }
      .range-slider {
         width: 100%;
         margin: 0.5rem 0;
      }
      .range-value {
         font-size: 1.1rem;
         font-weight: 600;
         color: var(--primary);
         text-align: center;
         margin-top: 0.5rem;
      }
      .palette-types {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
         gap: 1rem;
         margin-top: 1.5rem;
      }
      .palette-type-btn {
         padding: 0.8rem;
         border: 2px solid var(--back-dark);
         border-radius: 8px;
         background: white;
         cursor: pointer;
         transition: all 0.3s;
         font-weight: 500;
         text-align: center;
      }
      .palette-type-btn.active {
         background: var(--primary);
         color: white;
         border-color: var(--primary);
      }
      .palette-type-btn:hover:not(.active) {
         border-color: var(--primary);
      }
      .color-actions {
         display: flex;
         gap: 1rem;
         margin-top: 2rem;
         flex-wrap: wrap;
      }
      .color-btn {
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
      .color-btn.generate {
         background: var(--primary);
         color: white;
      }
      .color-btn.generate:hover {
         background: var(--secondary);
      }
      .color-btn.copy {
         background: #4CAF50;
         color: white;
      }
      .color-btn.copy:hover {
         background: #45a049;
      }
      .color-btn.reset {
         background: transparent;
         color: var(--text-color);
         border: 2px solid var(--back-dark);
      }
      .color-btn.reset:hover {
         background: var(--back-dark);
      }
      .palette-display {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
         gap: 1.5rem;
         margin-top: 2rem;
      }
      .color-card {
         border-radius: 10px;
         overflow: hidden;
         box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
         transition: transform 0.3s;
      }
      .color-card:hover {
         transform: translateY(-5px);
      }
      .color-swatch {
         height: 120px;
         display: flex;
         align-items: center;
         justify-content: center;
         font-weight: 600;
         color: white;
         text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
         font-size: 1.2rem;
         cursor: pointer;
         transition: all 0.3s;
      }
      .color-swatch:hover {
         filter: brightness(1.1);
      }
      .color-info {
         background: white;
         padding: 1rem;
         text-align: center;
      }
      .color-hex {
         font-family: 'Consolas', monospace;
         font-weight: 600;
         font-size: 1.1rem;
         margin-bottom: 0.3rem;
         cursor: pointer;
         transition: color 0.3s;
      }
      .color-hex:hover {
         color: var(--primary);
      }
      .color-rgb {
         font-size: 0.9rem;
         color: #666;
         font-family: 'Consolas', monospace;
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
      .palette-export {
         margin-top: 2rem;
         padding-top: 1.5rem;
         border-top: 2px solid var(--back-light);
      }
      .export-formats {
         display: flex;
         gap: 1rem;
         margin-top: 1rem;
         flex-wrap: wrap;
      }
      .export-btn {
         padding: 0.6rem 1.5rem;
         border-radius: 6px;
         border: none;
         cursor: pointer;
         font-weight: 500;
         transition: all 0.3s;
         background: var(--back-light);
         color: var(--text-color);
      }
      .export-btn:hover {
         background: var(--primary);
         color: white;
      }
      .code-preview {
         background: #1e1e1e;
         color: #d4d4d4;
         padding: 1.5rem;
         border-radius: 8px;
         font-family: 'Consolas', 'Monaco', 'Courier New', monospace;
         font-size: 0.9rem;
         line-height: 1.5;
         white-space: pre-wrap;
         word-wrap: break-word;
         margin-top: 1rem;
         max-height: 200px;
         overflow-y: auto;
         display: none;
      }
      @media screen and (max-width: 1200px) {
         .color-container {
            padding: 2% 5% 5%;
         }
      }
      @media screen and (max-width: 768px) {
         .color-container {
            padding: 2% 1rem 5%;
         }
         .color-header h1 {
            font-size: 2.2rem;
         }
         .color-actions {
            flex-direction: column;
         }
         .color-btn {
            width: 100%;
         }
         .palette-display {
            grid-template-columns: repeat(2, 1fr);
         }
      }
      @media screen and (max-width: 480px) {
         .palette-display {
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
      <section class="color-container">
         <div class="floating-balls">
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
            <div class="ball"></div>
         </div>
         
         <div class="color-header scroll-effect">
            <h1>Color Palette Generator</h1>
            <p>Create beautiful color schemes for your projects</p>
         </div>
         
         <div class="color-generator scroll-effect">
            <div class="controls-section">
               <h2 class="section-title">Color Controls</h2>
               
               <div class="color-controls">
                  <div class="control-group">
                     <label for="baseColor">Base Color</label>
                     <input type="color" id="baseColor" class="color-picker" value="#3498db">
                  </div>
                  
                  <div class="control-group">
                     <label for="paletteSize">Number of Colors: <span id="sizeValue">5</span></label>
                     <input type="range" id="paletteSize" class="range-slider" min="3" max="8" value="5">
                  </div>
                  
                  <div class="control-group">
                     <label for="saturation">Saturation: <span id="saturationValue">50</span>%</label>
                     <input type="range" id="saturation" class="range-slider" min="10" max="100" value="50">
                  </div>
                  
                  <div class="control-group">
                     <label for="lightness">Lightness: <span id="lightnessValue">50</span>%</label>
                     <input type="range" id="lightness" class="range-slider" min="10" max="90" value="50">
                  </div>
               </div>
               
               <h3 class="section-title" style="font-size: 1.5rem; margin-top: 2rem;">Palette Type</h3>
               <div class="palette-types">
                  <button class="palette-type-btn active" data-type="analogous">Analogous</button>
                  <button class="palette-type-btn" data-type="complementary">Complementary</button>
                  <button class="palette-type-btn" data-type="triadic">Triadic</button>
                  <button class="palette-type-btn" data-type="tetradic">Tetradic</button>
                  <button class="palette-type-btn" data-type="monochromatic">Monochromatic</button>
                  <button class="palette-type-btn" data-type="random">Random</button>
               </div>
               
               <div class="color-actions">
                  <button class="color-btn generate" onclick="generatePalette()">
                     <i class="fas fa-palette"></i> Generate Palette
                  </button>
                  <button class="color-btn reset" onclick="resetControls()">
                     <i class="fas fa-redo"></i> Reset
                  </button>
               </div>
            </div>
            
            <div class="palette-section">
               <div class="section-title">
                  Color Palette
                  <span style="font-size: 1rem; color: #666; margin-left: 1rem;">Click any color to copy</span>
               </div>
               
               <div class="palette-display" id="paletteDisplay">
                  <!-- Colors will be generated here -->
               </div>
               
               <div class="copy-success" id="copySuccess">
                  Color copied to clipboard!
               </div>
               
               <div class="palette-export">
                  <h3 class="section-title" style="font-size: 1.5rem;">Export Palette</h3>
                  <div class="export-formats">
                     <button class="export-btn" onclick="exportAsCSS()">CSS Variables</button>
                     <button class="export-btn" onclick="exportAsSCSS()">SCSS Variables</button>
                     <button class="export-btn" onclick="exportAsTailwind()">Tailwind Colors</button>
                     <button class="export-btn" onclick="exportAsJSON()">JSON</button>
                  </div>
                  
                  <div class="code-preview" id="codePreview"></div>
               </div>
            </div>
         </div>
      </section>
   </main>
   
   <?php include './assets/footer.php' ?>
   
   <script src="./js/scroll.js"></script>
   <script src="./js/fly-in.js"></script>
   <script>
      let currentPaletteType = 'analogous';
      let currentColors = [];
      
      document.addEventListener('DOMContentLoaded', function() {
         // Update range value displays
         const sliders = ['paletteSize', 'saturation', 'lightness'];
         sliders.forEach(sliderId => {
            const slider = document.getElementById(sliderId);
            const valueSpan = document.getElementById(sliderId + 'Value');
            
            slider.addEventListener('input', function() {
               valueSpan.textContent = this.value;
            });
         });
         
         // Palette type buttons
         const typeButtons = document.querySelectorAll('.palette-type-btn');
         typeButtons.forEach(button => {
            button.addEventListener('click', function() {
               typeButtons.forEach(btn => btn.classList.remove('active'));
               this.classList.add('active');
               currentPaletteType = this.dataset.type;
               generatePalette();
            });
         });
         
         // Generate initial palette
         generatePalette();
         
         // Load random palette button
         const randomBtn = document.createElement('button');
         randomBtn.type = 'button';
         randomBtn.className = 'color-btn generate';
         randomBtn.style.marginTop = '1rem';
         randomBtn.style.width = '100%';
         randomBtn.innerHTML = '<i class="fas fa-random"></i> Generate Random Palette';
         randomBtn.onclick = generateRandomPalette;
         
         const actions = document.querySelector('.color-actions');
         if (actions) {
            actions.parentNode.insertBefore(randomBtn, actions);
         }
      });
      
      function generatePalette() {
         const baseColor = document.getElementById('baseColor').value;
         const size = parseInt(document.getElementById('paletteSize').value);
         const saturation = parseInt(document.getElementById('saturation').value);
         const lightness = parseInt(document.getElementById('lightness').value);
         
         // Convert hex to HSL
         const baseHSL = hexToHSL(baseColor);
         
         // Generate palette based on type
         let palette = [];
         
         switch(currentPaletteType) {
            case 'analogous':
               palette = generateAnalogousPalette(baseHSL, size, saturation, lightness);
               break;
            case 'complementary':
               palette = generateComplementaryPalette(baseHSL, size, saturation, lightness);
               break;
            case 'triadic':
               palette = generateTriadicPalette(baseHSL, size, saturation, lightness);
               break;
            case 'tetradic':
               palette = generateTetradicPalette(baseHSL, size, saturation, lightness);
               break;
            case 'monochromatic':
               palette = generateMonochromaticPalette(baseHSL, size, saturation, lightness);
               break;
            case 'random':
               palette = generateRandomPaletteColors(size);
               break;
         }
         
         currentColors = palette;
         displayPalette(palette);
      }
      
      function generateRandomPalette() {
         const size = parseInt(document.getElementById('paletteSize').value);
         const palette = generateRandomPaletteColors(size);
         currentColors = palette;
         displayPalette(palette);
      }
      
      function generateRandomPaletteColors(size) {
         const colors = [];
         for (let i = 0; i < size; i++) {
            colors.push({
               hex: '#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'),
               rgb: hexToRgb('#' + Math.floor(Math.random() * 16777215).toString(16).padStart(6, '0'))
            });
         }
         return colors;
      }
      
      function generateAnalogousPalette(baseHSL, size, saturation, lightness) {
         const colors = [];
         const hueStep = 30;
         const startHue = baseHSL.h - Math.floor(size / 2) * hueStep;
         
         for (let i = 0; i < size; i++) {
            const hue = (startHue + i * hueStep) % 360;
            const hex = hslToHex(hue, saturation, lightness);
            colors.push({
               hex: hex,
               rgb: hexToRgb(hex)
            });
         }
         return colors;
      }
      
      function generateComplementaryPalette(baseHSL, size, saturation, lightness) {
         const colors = [];
         const complementaryHue = (baseHSL.h + 180) % 360;
         
         // Generate shades between base and complementary
         for (let i = 0; i < size; i++) {
            const ratio = i / (size - 1);
            const hue = (baseHSL.h + (complementaryHue - baseHSL.h) * ratio) % 360;
            const hex = hslToHex(hue, saturation, lightness);
            colors.push({
               hex: hex,
               rgb: hexToRgb(hex)
            });
         }
         return colors;
      }
      
      function generateTriadicPalette(baseHSL, size, saturation, lightness) {
         const colors = [];
         const triadicHues = [baseHSL.h, (baseHSL.h + 120) % 360, (baseHSL.h + 240) % 360];
         
         // Distribute colors from triadic hues
         for (let i = 0; i < size; i++) {
            const hueIndex = i % triadicHues.length;
            const lightnessVariation = lightness + (i % 3 - 1) * 10;
            const hex = hslToHex(triadicHues[hueIndex], saturation, Math.max(10, Math.min(90, lightnessVariation)));
            colors.push({
               hex: hex,
               rgb: hexToRgb(hex)
            });
         }
         return colors;
      }
      
      function generateTetradicPalette(baseHSL, size, saturation, lightness) {
         const colors = [];
         const tetradicHues = [baseHSL.h, (baseHSL.h + 90) % 360, (baseHSL.h + 180) % 360, (baseHSL.h + 270) % 360];
         
         for (let i = 0; i < size; i++) {
            const hueIndex = i % tetradicHues.length;
            const hex = hslToHex(tetradicHues[hueIndex], saturation, lightness);
            colors.push({
               hex: hex,
               rgb: hexToRgb(hex)
            });
         }
         return colors;
      }
      
      function generateMonochromaticPalette(baseHSL, size, saturation, lightness) {
         const colors = [];
         const lightnessStep = 70 / (size - 1);
         
         for (let i = 0; i < size; i++) {
            const light = 15 + i * lightnessStep;
            const hex = hslToHex(baseHSL.h, saturation, light);
            colors.push({
               hex: hex,
               rgb: hexToRgb(hex)
            });
         }
         return colors;
      }
      
      function displayPalette(colors) {
         const paletteDisplay = document.getElementById('paletteDisplay');
         paletteDisplay.innerHTML = '';
         
         colors.forEach((color, index) => {
            const colorCard = document.createElement('div');
            colorCard.className = 'color-card';
            
            const luminance = getLuminance(color.rgb);
            const textColor = luminance > 0.5 ? '#000' : '#fff';
            
            colorCard.innerHTML = `
               <div class="color-swatch" style="background: ${color.hex}; color: ${textColor};" 
                    onclick="copyColor('${color.hex}')" title="Click to copy">
                  ${color.hex.toUpperCase()}
               </div>
               <div class="color-info">
                  <div class="color-hex" onclick="copyColor('${color.hex}')">${color.hex.toUpperCase()}</div>
                  <div class="color-rgb">rgb(${color.rgb.r}, ${color.rgb.g}, ${color.rgb.b})</div>
               </div>
            `;
            
            paletteDisplay.appendChild(colorCard);
         });
      }
      
      function copyColor(hex) {
         const textarea = document.createElement('textarea');
         textarea.value = hex;
         document.body.appendChild(textarea);
         textarea.select();
         document.execCommand('copy');
         document.body.removeChild(textarea);
         
         const successMsg = document.getElementById('copySuccess');
         successMsg.textContent = `Color ${hex.toUpperCase()} copied to clipboard!`;
         successMsg.style.display = 'block';
         
         setTimeout(() => {
            successMsg.style.display = 'none';
         }, 3000);
      }
      
      function resetControls() {
         document.getElementById('baseColor').value = '#3498db';
         document.getElementById('paletteSize').value = 5;
         document.getElementById('saturation').value = 50;
         document.getElementById('lightness').value = 50;
         
         document.getElementById('sizeValue').textContent = '5';
         document.getElementById('saturationValue').textContent = '50';
         document.getElementById('lightnessValue').textContent = '50';
         
         // Reset to analogous
         const typeButtons = document.querySelectorAll('.palette-type-btn');
         typeButtons.forEach(btn => btn.classList.remove('active'));
         typeButtons[0].classList.add('active');
         currentPaletteType = 'analogous';
         
         generatePalette();
      }
      
      // Color conversion utilities
      function hexToHSL(hex) {
         const rgb = hexToRgb(hex);
         const r = rgb.r / 255;
         const g = rgb.g / 255;
         const b = rgb.b / 255;
         
         const max = Math.max(r, g, b);
         const min = Math.min(r, g, b);
         let h, s, l = (max + min) / 2;
         
         if (max === min) {
            h = s = 0;
         } else {
            const d = max - min;
            s = l > 0.5 ? d / (2 - max - min) : d / (max + min);
            
            switch(max) {
               case r: h = (g - b) / d + (g < b ? 6 : 0); break;
               case g: h = (b - r) / d + 2; break;
               case b: h = (r - g) / d + 4; break;
            }
            h /= 6;
         }
         
         return {
            h: Math.round(h * 360),
            s: Math.round(s * 100),
            l: Math.round(l * 100)
         };
      }
      
      function hexToRgb(hex) {
         const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
         return result ? {
            r: parseInt(result[1], 16),
            g: parseInt(result[2], 16),
            b: parseInt(result[3], 16)
         } : { r: 0, g: 0, b: 0 };
      }
      
      function hslToHex(h, s, l) {
         h /= 360;
         s /= 100;
         l /= 100;
         
         let r, g, b;
         
         if (s === 0) {
            r = g = b = l;
         } else {
            const hue2rgb = (p, q, t) => {
               if (t < 0) t += 1;
               if (t > 1) t -= 1;
               if (t < 1/6) return p + (q - p) * 6 * t;
               if (t < 1/2) return q;
               if (t < 2/3) return p + (q - p) * (2/3 - t) * 6;
               return p;
            };
            
            const q = l < 0.5 ? l * (1 + s) : l + s - l * s;
            const p = 2 * l - q;
            
            r = hue2rgb(p, q, h + 1/3);
            g = hue2rgb(p, q, h);
            b = hue2rgb(p, q, h - 1/3);
         }
         
         const toHex = x => {
            const hex = Math.round(x * 255).toString(16);
            return hex.length === 1 ? '0' + hex : hex;
         };
         
         return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
      }
      
      function getLuminance(rgb) {
         const [r, g, b] = [rgb.r, rgb.g, rgb.b].map(v => {
            v /= 255;
            return v <= 0.03928 ? v / 12.92 : Math.pow((v + 0.055) / 1.055, 2.4);
         });
         return 0.2126 * r + 0.7152 * g + 0.0722 * b;
      }
      
      // Export functions
      function exportAsCSS() {
         let css = ':root {\n';
         currentColors.forEach((color, index) => {
            css += `  --color-${index + 1}: ${color.hex};\n`;
         });
         css += '}';
         showCodePreview(css, 'css');
      }
      
      function exportAsSCSS() {
         let scss = '';
         currentColors.forEach((color, index) => {
            scss += `$color-${index + 1}: ${color.hex};\n`;
         });
         showCodePreview(scss, 'scss');
      }
      
      function exportAsTailwind() {
         let tailwind = 'module.exports = {\n  theme: {\n    extend: {\n      colors: {\n';
         currentColors.forEach((color, index) => {
            tailwind += `        'color-${index + 1}': '${color.hex}',\n`;
         });
         tailwind += '      }\n    }\n  }\n}';
         showCodePreview(tailwind, 'javascript');
      }
      
      function exportAsJSON() {
         const json = {
            palette: currentColors.map(color => ({
               hex: color.hex,
               rgb: color.rgb
            })),
            generated: new Date().toISOString(),
            type: currentPaletteType
         };
         showCodePreview(JSON.stringify(json, null, 2), 'json');
      }
      
      function showCodePreview(code, language) {
         const preview = document.getElementById('codePreview');
         preview.textContent = code;
         preview.style.display = 'block';
         
         // Scroll to preview
         preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
         
         // Copy button
         const copyBtn = document.createElement('button');
         copyBtn.className = 'export-btn';
         copyBtn.style.marginTop = '1rem';
         copyBtn.innerHTML = '<i class="fas fa-copy"></i> Copy Code';
         copyBtn.onclick = () => {
            const textarea = document.createElement('textarea');
            textarea.value = code;
            document.body.appendChild(textarea);
            textarea.select();
            document.execCommand('copy');
            document.body.removeChild(textarea);
            
            const successMsg = document.getElementById('copySuccess');
            successMsg.textContent = `${language.toUpperCase()} code copied to clipboard!`;
            successMsg.style.display = 'block';
            
            setTimeout(() => {
               successMsg.style.display = 'none';
            }, 3000);
         };
         
         // Remove existing copy button if any
         const existingBtn = preview.parentNode.querySelector('.copy-code-btn');
         if (existingBtn) {
            existingBtn.remove();
         }
         
         copyBtn.className = 'export-btn copy-code-btn';
         preview.parentNode.appendChild(copyBtn);
      }
   </script>
</body>
</html>