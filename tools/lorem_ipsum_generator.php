   <?php
   require_once '../assets/config.php';

   if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
      header("Location: signin.php");
      exit;
   }

   $current_page = 'web_tools';

   // Default values
   $paragraphs = 3;
   $sentences_per_paragraph = 5;
   $words_per_sentence = 15;
   $include_html = true;
   $output = '';

   // Lorem Ipsum words
   $lorem_words = [
      'lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consectetur', 'adipiscing', 'elit',
      'sed', 'do', 'eiusmod', 'tempor', 'incididunt', 'ut', 'labore', 'et', 'dolore',
      'magna', 'aliqua', 'enim', 'ad', 'minim', 'veniam', 'quis', 'nostrud',
      'exercitation', 'ullamco', 'laboris', 'nisi', 'ut', 'aliquip', 'ex', 'ea',
      'commodo', 'consequat', 'duis', 'aute', 'irure', 'dolor', 'in', 'reprehenderit',
      'voluptate', 'velit', 'esse', 'cillum', 'dolore', 'eu', 'fugiat', 'nulla',
      'pariatur', 'excepteur', 'sint', 'occaecat', 'cupidatat', 'non', 'proident',
      'sunt', 'in', 'culpa', 'qui', 'officia', 'deserunt', 'mollit', 'anim', 'id', 'est', 'laborum'
   ];

   function generateSentence($words, $min_words = 8, $max_words = 20) {
      $sentence_length = rand($min_words, $max_words);
      $sentence_words = [];
      
      for ($i = 0; $i < $sentence_length; $i++) {
         $sentence_words[] = $words[array_rand($words)];
      }
      
      // Capitalize first word
      $sentence_words[0] = ucfirst($sentence_words[0]);
      
      return implode(' ', $sentence_words) . '.';
   }

   function generateParagraph($words, $sentences = 5, $min_words = 8, $max_words = 20) {
      $paragraph_sentences = [];
      
      for ($i = 0; $i < $sentences; $i++) {
         $paragraph_sentences[] = generateSentence($words, $min_words, $max_words);
      }
      
      return implode(' ', $paragraph_sentences);
   }

   if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      $paragraphs = isset($_POST['paragraphs']) ? intval($_POST['paragraphs']) : 3;
      $sentences_per_paragraph = isset($_POST['sentences_per_paragraph']) ? intval($_POST['sentences_per_paragraph']) : 5;
      $words_per_sentence = isset($_POST['words_per_sentence']) ? intval($_POST['words_per_sentence']) : 15;
      $include_html = isset($_POST['include_html']) ? true : false;
      $output_type = isset($_POST['output_type']) ? $_POST['output_type'] : 'text';
      
      // Validate inputs
      $paragraphs = max(1, min(20, $paragraphs));
      $sentences_per_paragraph = max(1, min(50, $sentences_per_paragraph));
      $words_per_sentence = max(5, min(100, $words_per_sentence));
      
      // Generate Lorem Ipsum
      $generated_paragraphs = [];
      
      for ($p = 0; $p < $paragraphs; $p++) {
         $paragraph_text = generateParagraph($lorem_words, $sentences_per_paragraph, 
                                             $words_per_sentence, $words_per_sentence + 5);
         
         if ($include_html && $output_type !== 'plain') {
            $generated_paragraphs[] = "<p>$paragraph_text</p>";
         } else {
            $generated_paragraphs[] = $paragraph_text;
         }
      }
      
      // Format output based on type
      switch ($output_type) {
         case 'html':
            $output = implode("\n\n", $generated_paragraphs);
            break;
         case 'plain':
            $output = strip_tags(implode("\n\n", $generated_paragraphs));
            break;
         case 'array':
            $output = json_encode($generated_paragraphs, JSON_PRETTY_PRINT);
            break;
         default:
            $output = implode("\n\n", $generated_paragraphs);
      }
   }
   ?>
   <!DOCTYPE html>
   <html lang="en">
   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>Lorem Ipsum Generator - Code Library</title>
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
         .lorem-container {
            min-height: 100vh;
            padding: 2% 5% 5%;
            position: relative;
            z-index: 2;
         }
         .lorem-header {
            text-align: center;
            margin-bottom: 3rem;
         }
         .lorem-header h1 {
            font-size: 2.8rem;
            margin-bottom: 1rem;
            color: var(--text-color);
         }
         .lorem-header p {
            font-size: 1.2rem;
            color: var(--text-color);
            opacity: 0.8;
         }
         .lorem-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            margin-bottom: 2rem;
         }
         .lorem-form {
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
         .checkbox-group {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin: 1rem 0;
         }
         .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
         }
         .radio-group {
            display: flex;
            gap: 2rem;
            flex-wrap: wrap;
         }
         .radio-option {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
         }
         .radio-option input {
            width: 18px;
            height: 18px;
         }
         .output-container {
            margin-top: 2rem;
         }
         .output-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
         }
         .output-header h3 {
            color: var(--text-color);
            font-size: 1.2rem;
         }
         .output-text {
            width: 100%;
            min-height: 300px;
            padding: 1.5rem;
            border: 2px solid var(--back-dark);
            border-radius: 8px;
            font-family: var(--text_font);
            font-size: 1rem;
            line-height: 1.6;
            resize: vertical;
            background: #f8f9fa;
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
         .btn-success {
            background: #4CAF50;
            color: white;
         }
         .btn-success:hover {
            background: #45a049;
            transform: translateY(-2px);
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
         @media screen and (max-width: 768px) {
            .lorem-container {
                  padding: 2% 1rem 5%;
            }
            .lorem-header h1 {
                  font-size: 2.2rem;
            }
            .lorem-card, .tool-info {
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
         <section class="lorem-container">
            <div class="floating-balls">
                  <div class="ball"></div>
                  <div class="ball"></div>
                  <div class="ball"></div>
                  <div class="ball"></div>
                  <div class="ball"></div>
                  <div class="ball"></div>
            </div>
            
            <div class="lorem-header scroll-effect">
                  <h1>Lorem Ipsum Generator</h1>
                  <p>Generate placeholder text for your design mockups and projects</p>
            </div>
            
            <div class="lorem-card scroll-effect">
                  <form method="POST" class="lorem-form" id="loremForm">
                     <div class="form-row">
                        <div class="form-group">
                              <label for="paragraphs">Number of Paragraphs:</label>
                              <input type="number" id="paragraphs" name="paragraphs" class="number-input" 
                                    min="1" max="20" value="<?php echo $paragraphs; ?>">
                        </div>
                        
                        <div class="form-group">
                              <label for="sentences_per_paragraph">Sentences per Paragraph:</label>
                              <input type="number" id="sentences_per_paragraph" name="sentences_per_paragraph" 
                                    class="number-input" min="1" max="50" value="<?php echo $sentences_per_paragraph; ?>">
                        </div>
                        
                        <div class="form-group">
                              <label for="words_per_sentence">Words per Sentence:</label>
                              <input type="number" id="words_per_sentence" name="words_per_sentence" 
                                    class="number-input" min="5" max="100" value="<?php echo $words_per_sentence; ?>">
                        </div>
                     </div>
                     
                     <div class="form-group">
                        <label>Output Format:</label>
                        <div class="radio-group">
                              <label class="radio-option">
                                 <input type="radio" name="output_type" value="html" <?php echo (!isset($_POST['output_type']) || $_POST['output_type'] === 'html') ? 'checked' : ''; ?>>
                                 HTML Format
                              </label>
                              <label class="radio-option">
                                 <input type="radio" name="output_type" value="plain" <?php echo (isset($_POST['output_type']) && $_POST['output_type'] === 'plain') ? 'checked' : ''; ?>>
                                 Plain Text
                              </label>
                              <label class="radio-option">
                                 <input type="radio" name="output_type" value="array" <?php echo (isset($_POST['output_type']) && $_POST['output_type'] === 'array') ? 'checked' : ''; ?>>
                                 JSON Array
                              </label>
                        </div>
                     </div>
                     
                     <div class="checkbox-group">
                        <input type="checkbox" id="include_html" name="include_html" <?php echo $include_html ? 'checked' : ''; ?>>
                        <label for="include_html">Wrap paragraphs in &lt;p&gt; tags</label>
                     </div>
                     
                     <div class="form-actions">
                        <button type="submit" class="form-btn btn-primary">
                              <i class="fas fa-magic"></i> Generate Text
                        </button>
                        
                        <a href="lorem_ipsum_generator.php" class="form-btn btn-secondary">
                              <i class="fas fa-redo"></i> Reset
                        </a>
                        
                        <a href="../web_tools.php" class="form-btn btn-secondary">
                              <i class="fas fa-arrow-left"></i> Back to Tools
                        </a>
                     </div>
                     
                     <?php if (!empty($output)): ?>
                     <div class="output-container">
                        <div class="output-header">
                              <h3>Generated Text:</h3>
                              <button type="button" class="copy-btn" onclick="copyOutput()">
                                 <i class="fas fa-copy"></i> Copy
                              </button>
                        </div>
                        <textarea class="output-text" id="outputText" readonly><?php echo htmlspecialchars($output); ?></textarea>
                        
                        <div class="output-header" style="margin-top: 1rem; justify-content: space-between;">
                              <div style="font-size: 0.9rem; color: #666;">
                                 <i class="fas fa-file-alt"></i>
                                 Paragraphs: <strong><?php echo $paragraphs; ?></strong>
                              </div>
                              <div style="font-size: 0.9rem; color: #666;">
                                 <i class="fas fa-font"></i>
                                 Characters: <strong><?php echo strlen($output); ?></strong>
                              </div>
                              <div style="font-size: 0.9rem; color: #666;">
                                 <i class="fas fa-words"></i>
                                 Words: <strong><?php echo str_word_count(strip_tags($output)); ?></strong>
                              </div>
                        </div>
                     </div>
                     
                     <div class="form-actions">
                        <button type="button" class="form-btn btn-success" onclick="downloadText()">
                              <i class="fas fa-download"></i> Download Text
                        </button>
                        
                        <button type="button" class="form-btn btn-primary" onclick="generateMore()">
                              <i class="fas fa-plus"></i> Generate More
                        </button>
                     </div>
                     <?php endif; ?>
                  </form>
            </div>
            
            <div class="tool-info scroll-effect">
                  <h2>About Lorem Ipsum Generator</h2>
                  <p>This tool generates placeholder text commonly used in the design and typesetting industry. Lorem Ipsum has been the industry's standard dummy text since the 1500s.</p>
                  
                  <div class="features-list">
                     <div class="feature-item">
                        <h4>Customizable Length</h4>
                        <p>Control the number of paragraphs, sentences, and words to generate the exact amount of text you need.</p>
                     </div>
                     
                     <div class="feature-item">
                        <h4>Multiple Formats</h4>
                        <p>Generate text in HTML, plain text, or JSON array format for different use cases.</p>
                     </div>
                     
                     <div class="feature-item">
                        <h4>HTML Ready</h4>
                        <p>Option to automatically wrap paragraphs in &lt;p&gt; tags for easy integration into web projects.</p>
                     </div>
                     
                     <div class="feature-item">
                        <h4>Copy & Download</h4>
                        <p>Easily copy generated text to clipboard or download it as a text file for offline use.</p>
                     </div>
                  </div>
                  
                  <div style="margin-top: 2rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary);">
                     <h4 style="color: var(--text-color); margin-bottom: 0.5rem;">How to Use:</h4>
                     <ol style="color: var(--text-color); opacity: 0.8; line-height: 1.6; padding-left: 1.2rem;">
                        <li>Set the number of paragraphs you need</li>
                        <li>Configure sentences per paragraph and words per sentence</li>
                        <li>Choose your preferred output format</li>
                        <li>Click "Generate Text" to create your placeholder content</li>
                        <li>Copy or download the generated text</li>
                     </ol>
                  </div>
            </div>
         </section>
      </main>
      
      <?php include '../assets/footer.php' ?>
      
      <script src="../js/scroll.js"></script>
      <script src="../js/fly-in.js"></script>
      <script>
         function copyOutput() {
            const outputText = document.getElementById('outputText');
            if (outputText) {
                  copyToClipboard(outputText.value, 'Text copied to clipboard!');
            }
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
                  showNotification('Failed to copy text', 'error');
            });
         }
         
         function downloadText() {
            const outputText = document.getElementById('outputText');
            if (!outputText || !outputText.value.trim()) {
                  showNotification('No text to download', 'error');
                  return;
            }
            
            const format = document.querySelector('input[name="output_type"]:checked').value;
            const extension = format === 'html' ? 'html' : format === 'json' ? 'json' : 'txt';
            const filename = `lorem-ipsum.${extension}`;
            const blob = new Blob([outputText.value], { type: 'text/plain' });
            const url = URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
            
            showNotification('Text downloaded successfully!');
         }
         
         function generateMore() {
            document.getElementById('loremForm').submit();
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
         
         function autoResize(textarea) {
            textarea.style.height = 'auto';
            textarea.style.height = (textarea.scrollHeight) + 'px';
         }
         
         const outputTextarea = document.getElementById('outputText');
         if (outputTextarea) {
            outputTextarea.addEventListener('input', function() {
                  autoResize(this);
            });
            autoResize(outputTextarea);
         }
      </script>
   </body>
   </html>