<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Error - Code Library</title>
   <link rel="stylesheet" href="../css/general.css">
   <link rel="stylesheet" href="../css/home.css">
   <style>
      .error-container {
         min-height: 100vh;
         display: flex;
         align-items: center;
         justify-content: center;
         background: linear-gradient(135deg, var(--back-light) 0%, #e8f4f8 50%, var(--back-dark) 100%);
         position: relative;
         overflow: hidden;
      }
      .error-content {
         text-align: center;
         background: white;
         padding: 4rem 3rem;
         border-radius: 20px;
         box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
         position: relative;
         z-index: 2;
         max-width: 500px;
         width: 90%;
      }
      .error-icon {
         width: 100px;
         height: 100px;
         background: linear-gradient(135deg, #ff6b6b, #ee5a52);
         border-radius: 50%;
         display: flex;
         align-items: center;
         justify-content: center;
         margin: 0 auto 2rem;
         color: white;
      }
      .error-content h1 {
         font-size: 3rem;
         margin-bottom: 1rem;
         color: var(--text-color);
      }
      .error-content h2 {
         font-size: 1.5rem;
         margin-bottom: 1.5rem;
         color: var(--text-color);
         opacity: 0.8;
      }
      .error-content p {
         font-size: 1.1rem;
         margin-bottom: 2.5rem;
         line-height: 1.6;
         color: var(--text-color);
         opacity: 0.7;
      }
      .error-code {
         background: var(--back-dark);
         padding: 0.5rem 1rem;
         border-radius: 10px;
         font-family: monospace;
         margin: 1rem 0;
         display: inline-block;
      }
   </style>
</head>
<body>
   <div class="error-container">
      <div class="floating-balls">
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
         <div class="ball"></div>
      </div>
      <div class="error-content">
         <div class="error-icon">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="50" height="50" fill="white">
               <path d="M256 512A256 256 0 1 0 256 0a256 256 0 1 0 0 512zM175 175c9.4-9.4 24.6-9.4 33.9 0l47 47 47-47c9.4-9.4 24.6-9.4 33.9 0s9.4 24.6 0 33.9l-47 47 47 47c9.4 9.4 9.4 24.6 0 33.9s-24.6 9.4-33.9 0l-47-47-47 47c-9.4 9.4-24.6 9.4-33.9 0s-9.4-24.6 0-33.9l47-47-47-47c-9.4-9.4-9.4-24.6 0-33.9z"/>
            </svg>
         </div>
         <h1>Oops!</h1>
         <h2>Something Went Wrong</h2>
         <p>We encountered an error while processing your request. This might be temporary, so please try again.</p>
         <?php if (isset($_GET['code'])): ?>
            <div class="error-code">Error Code: <?php echo htmlspecialchars($_GET['code']); ?></div>
         <?php endif; ?>
         <div class="btns">
            <a href="../index.php" class="primary_btn">
               <span>Back to Home</span>
            </a>
            <a href="javascript:history.back()" class="secondary_btn">
               <span>Go Back</span>
            </a>
         </div>
      </div>
   </div>
   <script src="../js/scroll.js"></script>
   <script>
      // Initialize scroll effects
      document.addEventListener('DOMContentLoaded', function() {
         const scrollElements = document.querySelectorAll('.scroll-effect');
         const elementInView = (el, dividend = 1) => {
            const elementTop = el.getBoundingClientRect().top;
            return (
                  elementTop <=
                  (window.innerHeight || document.documentElement.clientHeight) / dividend
            );
         };
         const displayScrollElement = (element) => {
            element.classList.add('visible');
         };
         const handleScrollAnimation = () => {
            scrollElements.forEach((el) => {
                  if (elementInView(el, 1.25)) {
                     displayScrollElement(el);
                  }
            });
         };
         window.addEventListener('scroll', () => {
            handleScrollAnimation();
         });
         // Trigger once on load
         handleScrollAnimation();
      });
   </script>
</body>
</html>